<?php
/**
 * Site search results: blogs, alternatives, case studies, whitepapers, events and FAQs.
 *
 * Every search lands here, including the ones a section's own search box limits to
 * that section (?s=…&post_type=alternatives). The section filter switches between
 * sections without losing the query. Results, counts and the filter all come from the
 * single search lib/search.php runs per page view.
 *
 * Built from the blog's parts so it reads as the same site: the hero is .blog-hero with
 * .hr-blog-archive__search (css/custom.css) and paging is pagination_bar(). The filter,
 * result cards and empty states are css/search.css; each card is
 * template-parts/search-card.php.
 *
 * @package stratusx-child
 */

// lib/search.php is loaded from functions.php. Without it (say, mid-deploy) fall back
// to the old blog-style results rather than a fatal error.
if (! function_exists('hr_search_state')) {
	include get_stylesheet_directory() . '/index.php';
	return;
}

// The same footer whichever post happens to be the first result.
add_filter('hr_footer_type', fn() => 'landing');

global $wp_query;

$state     = hr_search_state();
$types     = hr_search_types();
$query     = $state['query'];
$has_query = '' !== trim($query);
$scope     = isset($types[$state['scope']]) ? $state['scope'] : '';
$scope_tag = $scope ? $types[$scope]['plural'] : '';
$total     = $state['total'];
$counts    = $state['counts'];
$found     = (int) $wp_query->found_posts;
$items     = $wp_query->posts ? hr_search_items($wp_query->posts) : array();

$per_page = max(1, (int) $wp_query->get('posts_per_page'));
$paged    = max(1, (int) get_query_var('paged'));
$first    = ($paged - 1) * $per_page + 1;
$last     = $first + count($items) - 1;

$results_word = function ($count) {
	return 1 === (int) $count ? 'result' : 'results';
};

// Filter: every section with results, plus the one being viewed even when it has none.
$filters = array();
foreach ($types as $type => $labels) {
	if (! empty($counts[$type]) || $type === $scope) {
		$filters[$type] = array(
			'label' => $labels['plural'],
			'count' => (int) ($counts[$type] ?? 0),
		);
	}
}

$section_names = array_column($types, 'plural');
$section_list  = count($section_names) > 1
	? implode(', ', array_slice($section_names, 0, -1)) . ' and ' . end($section_names)
	: implode('', $section_names);

if (! $has_query) {
	$lead = 'One search across ' . $section_list . '.';
} elseif (! $total) {
	$lead = 'Nothing matched your search. Check the spelling or try a broader keyword.';
} elseif ($scope && $found) {
	$lead = sprintf('%s %s in %s, out of %s across the site.', number_format_i18n($found), $results_word($found), $scope_tag, number_format_i18n($total));
} elseif ($scope) {
	$lead = sprintf('No %s match, but %s %s turned up in other sections.', $scope_tag, number_format_i18n($total), $results_word($total));
} else {
	$with_results = count(array_filter($counts));
	$lead = sprintf('%s %s across %d %s.', number_format_i18n($total), $results_word($total), $with_results, 1 === $with_results ? 'section' : 'sections');
}

// Where to send visitors when there is nothing to list: other sections with results, or every section.
$links = array();
if ($has_query && $scope && $total) {
	foreach ($filters as $type => $filter) {
		if ($type !== $scope && $filter['count']) {
			$links[] = array(
				'label' => $filter['label'],
				'count' => $filter['count'],
				'url'   => hr_search_url($type),
			);
		}
	}
} else {
	foreach (hr_search_sections() as $section) {
		$links[] = array(
			'label' => $section['label'],
			'count' => 0,
			'url'   => $section['url'],
		);
	}
}
?>
<main class="hr-search">
	<section class="blog-hero hero-section hr-search-hero">
		<div class="container">
			<div class="heading text-center">
				<span class="hr-search-hero__eyebrow">Search</span>

				<?php if ($has_query) : ?>
					<h1>Results for <span class="hr-search-hero__query">&ldquo;<?= esc_html($query); ?>&rdquo;</span></h1>
				<?php else : ?>
					<h1>Search Healthray</h1>
				<?php endif; ?>

				<p><?= esc_html($lead); ?></p>
			</div>

			<div class="hr-blog-archive__search hr-search-hero__form">
				<?php get_search_form(); ?>
			</div>

			<?php if ($has_query && $total) : ?>
				<nav class="hr-search-filters" aria-label="Filter results by section">
					<ul>
						<li>
							<a class="hr-search-filter<?= $scope ? '' : ' is-active'; ?>" href="<?= esc_url(hr_search_url()); ?>" <?= $scope ? '' : 'aria-current="page"'; ?>>
								All <span class="hr-search-filter__count"><?= esc_html(number_format_i18n($total)); ?><span class="visually-hidden"> <?= esc_html($results_word($total)); ?></span></span>
							</a>
						</li>
						<?php foreach ($filters as $type => $filter) : ?>
							<li>
								<a class="hr-search-filter<?= $type === $scope ? ' is-active' : ''; ?>" href="<?= esc_url(hr_search_url($type)); ?>" <?= $type === $scope ? 'aria-current="page"' : ''; ?>>
									<?= esc_html($filter['label']); ?> <span class="hr-search-filter__count"><?= esc_html(number_format_i18n($filter['count'])); ?><span class="visually-hidden"> <?= esc_html($results_word($filter['count'])); ?></span></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>
		</div>
	</section>

	<section class="hr-search-results" aria-labelledby="hr-search-results-title">
		<div class="container">
			<?php if ($items) : ?>
				<div class="hr-search-toolbar">
					<h2 class="hr-search-toolbar__title" id="hr-search-results-title">
						Showing <strong><?= esc_html(number_format_i18n($first)); ?>&ndash;<?= esc_html(number_format_i18n($last)); ?></strong>
						of <strong><?= esc_html(number_format_i18n($found)); ?></strong> <?= esc_html($results_word($found)); ?><?= $scope ? ' in ' . esc_html($scope_tag) : ''; ?>
					</h2>

					<?php if ($scope) : ?>
						<a class="hr-search-toolbar__clear" href="<?= esc_url(hr_search_url()); ?>">
							<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true" focusable="false">
								<path d="M18 6 6 18M6 6l12 12" />
							</svg>
							Search all sections
						</a>
					<?php endif; ?>
				</div>

				<ul class="hr-search-grid" role="list">
					<?php foreach ($items as $item) : ?>
						<li><?php get_template_part('template-parts/search', 'card', array('item' => $item)); ?></li>
					<?php endforeach; ?>
				</ul>

				<?php pagination_bar(); ?>
			<?php else : ?>
				<div class="hr-search-empty">
					<span class="hr-search-empty__icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false">
							<circle cx="11" cy="11" r="7" />
							<path d="m20 20-3.5-3.5" />
						</svg>
					</span>

					<?php if (! $has_query) : ?>
						<h2 class="hr-search-empty__title" id="hr-search-results-title">What are you looking for?</h2>
						<p class="hr-search-empty__text">Type a keyword above, or browse a section.</p>
					<?php elseif ($scope && $total) : ?>
						<h2 class="hr-search-empty__title" id="hr-search-results-title">No <?= esc_html($scope_tag); ?> match &ldquo;<?= esc_html($query); ?>&rdquo;</h2>
						<p class="hr-search-empty__text">These sections have results for it:</p>
					<?php else : ?>
						<h2 class="hr-search-empty__title" id="hr-search-results-title">Nothing matched &ldquo;<?= esc_html($query); ?>&rdquo;</h2>
						<p class="hr-search-empty__text">Try a broader keyword, such as EMR, billing or lab, or browse a section.</p>
					<?php endif; ?>

					<?php if ($links) : ?>
						<ul class="hr-search-links">
							<?php foreach ($links as $link) : ?>
								<li>
									<a class="hr-search-filter" href="<?= esc_url($link['url']); ?>">
										<?= esc_html($link['label']); ?>
										<?php if ($link['count']) : ?>
											<span class="hr-search-filter__count"><?= esc_html(number_format_i18n($link['count'])); ?><span class="visually-hidden"> <?= esc_html($results_word($link['count'])); ?></span></span>
										<?php endif; ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>
