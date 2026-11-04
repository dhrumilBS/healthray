<?php

/**
 * Admin: "PPC Pages" menu.
 *
 * One screen that lists every live (published) PPC landing page, with its
 * URL, the template file that renders it and links to view the page, edit its
 * settings (title, URL, SEO) and edit the template file, where PPC page
 * content lives. The menu label shows how many PPC pages are live.
 *
 * A page counts as a PPC page when the template file that renders it matches
 * hr_ppc_admin_template_patterns(). New PPC templates that follow the
 * temp-ppc-*.php naming (the one hr_ppc_page_key() relies on) show up here
 * automatically; other names can be added with the
 * hr_ppc_admin_template_patterns filter.
 *
 * Read-only: this screen never creates, publishes or changes pages.
 *
 * @package stratusx-child
 */

if (! defined('ABSPATH')) {
	exit;
}

const HR_PPC_ADMIN_SLUG  = 'hr-ppc-pages';
const HR_PPC_ADMIN_CACHE = 'hr_ppc_admin_pages';

/**
 * Template file names (fnmatch patterns, theme root) that make a page a PPC page.
 *
 * @return string[]
 */
function hr_ppc_admin_template_patterns()
{
	return (array) apply_filters(
		'hr_ppc_admin_template_patterns',
		array(
			'temp-ppc-*.php',    // temp-ppc-hims-google.php, temp-ppc-ng.php, ...
			'temp-*-ppc.php',    // temp-emr-software-ppc.php, temp-hims-ppc.php
			'page-pharma-*.php', // pharmacy PPC pages, picked by slug (pharma-google, ...)
		)
	);
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

function hr_ppc_admin_is_ppc_template($file)
{
	if ('' === $file) {
		return false;
	}
	foreach (hr_ppc_admin_template_patterns() as $pattern) {
		if (fnmatch($pattern, $file)) {
			return true;
		}
	}

	return false;
}

/**
 * Live PPC pages, ordered by title: array of array{id:int, file:string}.
 * Cached; the cache is cleared whenever a page is saved, changes status or is
 * deleted, so the menu count stays correct.
 *
 * @return array<int,array{id:int,file:string}>
 */
function hr_ppc_admin_pages()
{
	$cached = get_transient(HR_PPC_ADMIN_CACHE);
	if (is_array($cached)) {
		return $cached;
	}

	$pages = array();
	$query = new WP_Query(
		array(
			'post_type'              => 'page',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => 'title',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		)
	);
	foreach ($query->posts as $page) {
		$file = hr_ppc_admin_template_file($page);
		if (hr_ppc_admin_is_ppc_template($file)) {
			$pages[] = array('id' => (int) $page->ID, 'file' => $file);
		}
	}

	set_transient(HR_PPC_ADMIN_CACHE, $pages, 12 * HOUR_IN_SECONDS);

	return $pages;
}

function hr_ppc_admin_flush()
{
	delete_transient(HR_PPC_ADMIN_CACHE);
}

add_action('save_post_page', 'hr_ppc_admin_flush');
add_action('deleted_post', 'hr_ppc_admin_flush');
add_action('switch_theme', 'hr_ppc_admin_flush');
add_action('transition_post_status', function ($new, $old, $post) {
	if ($new !== $old && $post instanceof WP_Post && 'page' === $post->post_type) {
		hr_ppc_admin_flush();
	}
}, 10, 3);
// A template switch only changes post meta.
add_action('updated_post_meta', function ($meta_id, $post_id, $key) {
	if ('_wp_page_template' === $key) {
		hr_ppc_admin_flush();
	}
}, 10, 3);

/**
 * Link to edit a theme file in Appearance > Theme File Editor, or '' when the
 * user cannot edit theme files (or DISALLOW_FILE_EDIT is set).
 */
function hr_ppc_admin_file_edit_url($file)
{
	if (! current_user_can('edit_themes')) {
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
// Screen
// -----------------------------------------------------------------------------

function hr_ppc_admin_render()
{
	if (! current_user_can('edit_pages')) {
		return;
	}

	// Always build the screen from fresh data.
	hr_ppc_admin_flush();
	$pages     = hr_ppc_admin_pages();
	$count     = count($pages);
	$templates = wp_get_theme()->get_page_templates(null, 'page');
	$can_files = current_user_can('edit_themes');
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline">PPC Pages</h1>
		<hr class="wp-header-end">

		<p><strong><?php echo esc_html(sprintf(_n('%d PPC page is live.', '%d PPC pages are live.', $count), $count)); ?></strong>
			Only published pages are listed; drafts and other pages are not shown.</p>
		<p>The text and layout of these pages live in their template files, not in the page editor. Use <em>Edit template</em> for content changes, and <em>Page settings</em> for the title, URL and SEO.
			<?php if ($can_files) : ?>
				A template edit is live as soon as it is saved, and the next theme deploy from git replaces it: copy the change into the repository too.
			<?php endif; ?>
		</p>

		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th scope="col" style="width:30%">Page</th>
					<th scope="col" style="width:26%">URL</th>
					<th scope="col">Template</th>
					<th scope="col" style="width:16%">Last updated</th>
				</tr>
			</thead>
			<tbody>
				<?php if (! $pages) : ?>
					<tr><td colspan="4">No live PPC pages found.</td></tr>
				<?php endif; ?>
				<?php foreach ($pages as $row) : ?>
					<?php
					$post = get_post($row['id']);
					if (! $post) {
						continue;
					}
					$url      = get_permalink($post);
					$edit_url = get_edit_post_link($post->ID);
					$file_url = hr_ppc_admin_file_edit_url($row['file']);
					$name     = $templates[$row['file']] ?? '';
					?>
					<tr>
						<td>
							<strong><?php if ($edit_url) : ?><a class="row-title" href="<?php echo esc_url($edit_url); ?>"><?php echo esc_html(get_the_title($post)); ?></a><?php else : ?><?php echo esc_html(get_the_title($post)); ?><?php endif; ?></strong>
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
							<code><?php echo esc_html($row['file']); ?></code>
						</td>
						<td>
							<?php echo esc_html(get_the_modified_date(get_option('date_format'), $post)); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}
