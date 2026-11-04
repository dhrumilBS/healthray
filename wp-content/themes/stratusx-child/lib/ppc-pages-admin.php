<?php

/**
 * Admin: "PPC Pages" menu.
 *
 * A PPC page is a page served under /ppc/ by the virtual URL map
 * (lib/virtual-urls.php). This screen lists the live (published) ones with
 * their URL and template, links to view them, edit their settings (title,
 * slug, SEO) and edit the template file where their content lives, and lets
 * an administrator add a page to /ppc/ or take one out. The menu label shows
 * how many PPC pages are live.
 *
 * It never creates, publishes or deletes pages: adding or removing only
 * changes which pages are served under /ppc/.
 *
 * @package stratusx-child
 */

if (! defined('ABSPATH')) {
	exit;
}

const HR_PPC_ADMIN_SLUG   = 'hr-ppc-pages';
const HR_PPC_ADMIN_PREFIX = 'ppc';

/**
 * Page IDs in /ppc/, in map order.
 *
 * @return int[]
 */
function hr_ppc_admin_ids()
{
	$map = hr_virtual_url_map();

	return $map[HR_PPC_ADMIN_PREFIX] ?? array();
}

/**
 * Pages in /ppc/ with the given status ('publish' or 'other'), ordered by title.
 *
 * @return WP_Post[]
 */
function hr_ppc_admin_pages($which = 'publish')
{
	$ids = hr_ppc_admin_ids();
	if (! $ids) {
		return array();
	}

	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post__in'       => $ids,
			'post_status'    => 'publish' === $which ? 'publish' : array('draft', 'pending', 'private', 'future', 'trash'),
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);

	return $pages;
}

/**
 * Template file that renders a page: the assigned page template, else the
 * page-{slug}.php file WordPress picks from the template hierarchy.
 */
function hr_ppc_admin_template_file($page)
{
	$assigned = (string) get_page_template_slug($page);
	if ('' !== $assigned && 'default' !== $assigned) {
		return $assigned;
	}

	$file = 'page-' . $page->post_name . '.php';

	return '' !== $page->post_name && file_exists(get_stylesheet_directory() . '/' . $file) ? $file : '';
}

/**
 * Link to edit a theme file in Appearance > Theme File Editor, or '' when the
 * user cannot edit theme files (or DISALLOW_FILE_EDIT is set).
 */
function hr_ppc_admin_file_edit_url($file)
{
	if ('' === $file || ! current_user_can('edit_themes')) {
		return '';
	}

	return add_query_arg(
		array(
			'file'  => rawurlencode($file),
			'theme' => rawurlencode(get_stylesheet()),
		),
		admin_url('theme-editor.php')
	);
}

// -----------------------------------------------------------------------------
// Menu
// -----------------------------------------------------------------------------

add_action('admin_menu', function () {
	$count = count(hr_ppc_admin_pages());
	$label = 'PPC Pages';
	if ($count) {
		$label .= sprintf(' <span class="hr-ppc-count" aria-label="%1$s">%2$s</span>', esc_attr(sprintf(_n('%d live page', '%d live pages', $count), $count)), number_format_i18n($count));
	}

	// Position 21: right below Pages.
	add_menu_page('PPC Pages', $label, 'edit_pages', HR_PPC_ADMIN_SLUG, 'hr_ppc_admin_render', 'dashicons-megaphone', 21);
});

add_action('admin_head', function () {
	// The count badge: same shape as the core update bubble, in a neutral colour.
	echo '<style>#adminmenu .hr-ppc-count{display:inline-block;min-width:18px;height:18px;margin:1px 0 -1px 2px;padding:0 5px;border-radius:9px;background:#3858e9;color:#fff;font-size:11px;line-height:1.6;text-align:center;box-sizing:border-box}</style>' . "\n";
});

// -----------------------------------------------------------------------------
// Add to / remove from /ppc/ (administrators only: it changes page URLs)
// -----------------------------------------------------------------------------

add_action('admin_post_hr_ppc_pages', function () {
	if (! current_user_can('manage_options')) {
		wp_die('Not allowed.');
	}
	check_admin_referer('hr_ppc_pages');

	$do      = sanitize_key($_POST['do'] ?? '');
	$page_id = absint($_POST['page_id'] ?? 0);
	$ids     = hr_ppc_admin_ids();
	$page    = $page_id ? get_post($page_id) : null;
	$notice  = array('error', 'Nothing changed.');

	if (! $page || 'page' !== $page->post_type) {
		$notice = array('error', 'Choose a page.');
	} elseif ('add' === $do) {
		if (in_array($page_id, $ids, true)) {
			$notice = array('warning', get_the_title($page) . ' is already in /ppc/.');
		} elseif ((int) $page->post_parent !== 0) {
			$notice = array('error', get_the_title($page) . ' has a parent page; only top-level pages can be served under /ppc/.');
		} elseif ('' === $page->post_name) {
			$notice = array('error', get_the_title($page) . ' has no slug yet.');
		} else {
			$clash = '';
			foreach ($ids as $other) {
				if (get_post_field('post_name', $other) === $page->post_name) {
					$clash = get_the_title($other);
				}
			}
			if ('' !== $clash) {
				$notice = array('error', sprintf('/ppc/%s/ is already used by %s. Change one of the slugs first.', $page->post_name, $clash));
			} else {
				$old_url = get_permalink($page);
				hr_virtual_url_set_pages(HR_PPC_ADMIN_PREFIX, array_merge($ids, array($page_id)));
				$notice = array('success', sprintf('%s is now at %s. Its old URL %s redirects there (301).', get_the_title($page), wp_make_link_relative(get_permalink($page)), wp_make_link_relative($old_url)));
			}
		}
	} elseif ('remove' === $do) {
		// A /ppc/ page may share its slug with a normal page; out of /ppc/ the two would collide.
		$twin = get_posts(array('post_type' => 'page', 'post_status' => 'publish', 'name' => $page->post_name, 'post_parent' => 0, 'post__not_in' => array($page_id), 'posts_per_page' => 1, 'fields' => 'ids', 'no_found_rows' => true));
		if (! in_array($page_id, $ids, true)) {
			$notice = array('warning', get_the_title($page) . ' is not in /ppc/.');
		} elseif ('publish' === $page->post_status && $twin) {
			$notice = array('error', sprintf('%s also uses the slug "%s". Change the slug of %s before taking it out of /ppc/.', get_the_title($twin[0]), $page->post_name, get_the_title($page)));
		} else {
			$old_url = get_permalink($page);
			hr_virtual_url_set_pages(HR_PPC_ADMIN_PREFIX, array_diff($ids, array($page_id)));
			$notice = array('success', sprintf('%s is back at %s; %s now returns 404. Update any ads that link to the old URL.', get_the_title($page), wp_make_link_relative(get_permalink($page)), wp_make_link_relative($old_url)));
		}
	}

	set_transient('hr_ppc_notice_' . get_current_user_id(), $notice, MINUTE_IN_SECONDS * 5);
	wp_safe_redirect(admin_url('admin.php?page=' . HR_PPC_ADMIN_SLUG));
	exit;
});

// -----------------------------------------------------------------------------
// Screen
// -----------------------------------------------------------------------------

function hr_ppc_admin_render()
{
	if (! current_user_can('edit_pages')) {
		return;
	}

	$notice = get_transient('hr_ppc_notice_' . get_current_user_id());
	delete_transient('hr_ppc_notice_' . get_current_user_id());

	$pages      = hr_ppc_admin_pages();
	$others     = hr_ppc_admin_pages('other');
	$count      = count($pages);
	$templates  = wp_get_theme()->get_page_templates(null, 'page');
	$can_files  = current_user_can('edit_themes');
	$can_manage = current_user_can('manage_options');
	$action     = esc_url(admin_url('admin-post.php'));
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline">PPC Pages</h1>
		<hr class="wp-header-end">

		<?php if (is_array($notice)) : ?>
			<div class="notice notice-<?php echo esc_attr($notice[0]); ?> is-dismissible"><p><?php echo esc_html($notice[1]); ?></p></div>
		<?php endif; ?>

		<p><strong><?php echo esc_html(sprintf(_n('%d PPC page is live.', '%d PPC pages are live.', $count), $count)); ?></strong>
			PPC pages are the published pages served under <code>/ppc/</code>.</p>
		<p>Their text and layout live in their template files, not in the page editor. Use <em>Edit template</em> for content changes, and <em>Page settings</em> for the title, slug and SEO.
			<?php if ($can_files) : ?>
				A template edit is live as soon as it is saved, and the next theme deploy from git replaces it: copy the change into the repository too.
			<?php endif; ?>
		</p>

		<?php if ($can_manage) : ?>
			<form method="post" action="<?php echo $action; ?>" style="margin:16px 0">
				<?php wp_nonce_field('hr_ppc_pages'); ?>
				<input type="hidden" name="action" value="hr_ppc_pages">
				<input type="hidden" name="do" value="add">
				<label for="hr-ppc-add"><strong>Add a page to /ppc/</strong></label><br>
				<?php
				wp_dropdown_pages(
					array(
						'id'                => 'hr-ppc-add',
						'name'              => 'page_id',
						'depth'             => 1,
						'exclude'           => implode(',', hr_ppc_admin_ids()),
						'post_status'       => 'publish',
						'show_option_none'  => '- Choose a published page -',
						'option_none_value' => '0',
						'sort_column'       => 'post_title',
					)
				);
				?>
				<button class="button button-primary" onclick="var s=document.getElementById('hr-ppc-add');if(s.value==='0'){alert('Choose a page.');return false;}return confirm('Move \u201C'+s.options[s.selectedIndex].text.trim()+'\u201D to /ppc/? Its current URL will 301 to the new /ppc/ URL.');">Add to /ppc/</button>
				<p class="description">The URL becomes <code>/ppc/{page slug}/</code>; the old URL redirects there with ad parameters (gclid, utm) kept. Set the slug you want before adding.</p>
			</form>
		<?php endif; ?>

		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th scope="col" style="width:28%">Page</th>
					<th scope="col" style="width:26%">URL</th>
					<th scope="col">Template</th>
					<th scope="col" style="width:12%">Last updated</th>
					<?php if ($can_manage) : ?><th scope="col" style="width:12%"></th><?php endif; ?>
				</tr>
			</thead>
			<tbody>
				<?php if (! $pages) : ?>
					<tr><td colspan="<?php echo $can_manage ? 5 : 4; ?>">No live PPC pages.</td></tr>
				<?php endif; ?>
				<?php foreach ($pages as $page) : ?>
					<?php
					$url      = get_permalink($page);
					$edit_url = get_edit_post_link($page->ID);
					$file     = hr_ppc_admin_template_file($page);
					$file_url = hr_ppc_admin_file_edit_url($file);
					$name     = $templates[$file] ?? '';
					?>
					<tr>
						<td>
							<strong><?php if ($edit_url) : ?><a class="row-title" href="<?php echo esc_url($edit_url); ?>"><?php echo esc_html(get_the_title($page)); ?></a><?php else : ?><?php echo esc_html(get_the_title($page)); ?><?php endif; ?></strong>
							<div class="row-actions visible">
								<span class="view"><a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener">View<span class="screen-reader-text"> (opens in a new tab)</span></a></span>
								<?php if ($edit_url) : ?>
									| <span class="edit"><a href="<?php echo esc_url($edit_url); ?>">Page settings</a></span>
								<?php endif; ?>
								<?php if ($file_url) : ?>
									| <span class="edit"><a href="<?php echo esc_url($file_url); ?>">Edit template</a></span>
								<?php endif; ?>
							</div>
						</td>
						<td><a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener"><code><?php echo esc_html(wp_make_link_relative($url)); ?></code></a></td>
						<td>
							<?php if ('' !== $name) : ?><?php echo esc_html($name); ?><br><?php endif; ?>
							<code><?php echo esc_html('' !== $file ? $file : 'page.php (default)'); ?></code>
						</td>
						<td><?php echo esc_html(get_the_modified_date(get_option('date_format'), $page)); ?></td>
						<?php if ($can_manage) : ?>
							<td>
								<form method="post" action="<?php echo $action; ?>">
									<?php wp_nonce_field('hr_ppc_pages'); ?>
									<input type="hidden" name="action" value="hr_ppc_pages">
									<input type="hidden" name="do" value="remove">
									<input type="hidden" name="page_id" value="<?php echo (int) $page->ID; ?>">
									<button class="button button-link-delete" onclick="return confirm('Take this page out of /ppc/? <?php echo esc_js(wp_make_link_relative($url)); ?> will return 404, so update any ads that link to it.')">Remove from /ppc/</button>
								</form>
							</td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php if ($others && $can_manage) : ?>
			<h2 style="margin-top:28px">In /ppc/ but not live</h2>
			<p>These pages are in the /ppc/ list but not published, so they are not served and not counted.</p>
			<ul>
				<?php foreach ($others as $page) : ?>
					<?php $status = get_post_status_object($page->post_status); ?>
					<li>
						<form method="post" action="<?php echo $action; ?>" style="display:inline">
							<?php wp_nonce_field('hr_ppc_pages'); ?>
							<input type="hidden" name="action" value="hr_ppc_pages">
							<input type="hidden" name="do" value="remove">
							<input type="hidden" name="page_id" value="<?php echo (int) $page->ID; ?>">
							<?php echo esc_html(get_the_title($page)); ?> (<?php echo esc_html($status ? $status->label : $page->post_status); ?>)
							<button class="button-link button-link-delete">Remove from list</button>
						</form>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
	<?php
}
