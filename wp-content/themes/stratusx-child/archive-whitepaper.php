<?php
/**
 * Whitepaper archive: the Whitepaper Library, and whitepaper search results.
 *
 * Built from the components the blog and the Events showcase already use, so
 * the library reads as part of the same site:
 *   - hero    .blog-hero.hero-section + .hr-blog-archive__search (css/custom.css),
 *             with the blog single's white-to-tint hero gradient
 *   - paging  pagination_bar() with the shared .pagination styles (style.css)
 * Whitepaper pieces (stats, cards, results bar, empty state, CTA band) live in
 * css/whitepaper.css; the card is template-parts/whitepaper-card.php and the
 * data comes from lib/whitepaper-helpers.php.
 *
 * Page 1 of the library opens with the newest whitepaper as a wide featured
 * card. The search form in the hero submits ?s=…&post_type=whitepaper, which
 * WordPress also renders with this template; results are listed as a plain
 * grid with a count. Posts per page, ordering and URLs are unchanged.
 *
 * @package stratusx-child
 */

global $wp_query;

$is_search     = is_search();
$is_first_page = ! is_paged();
$search_query  = get_search_query();
$archive_url   = get_post_type_archive_link( 'whitepaper' );
$contact_url   = hr_whitepapers_contact_url();
$stats         = hr_whitepapers_stats();

hr_whitepapers_prime( $wp_query->posts );

$items = array();
while ( have_posts() ) {
	the_post();

	$item = hr_whitepaper_data();
	if ( $item ) {
		$items[] = $item;
	}
}
rewind_posts();
wp_reset_postdata();

$found    = (int) $wp_query->found_posts;
$per_page = max( 1, (int) $wp_query->get( 'posts_per_page' ) );
$paged    = max( 1, (int) get_query_var( 'paged' ) );
$first    = ( $paged - 1 ) * $per_page + 1;
$last     = $first + count( $items ) - 1;

// Lead with the newest whitepaper on the first library page only.
$featured = ( $is_first_page && ! $is_search && count( $items ) > 2 ) ? array_shift( $items ) : array();

if ( $is_search ) {
	$list_title = 'Search Results';
} elseif ( $is_first_page ) {
	$list_title = 'Explore the Library';
} else {
	$list_title = 'All Whitepapers';
}
?>
<main class="wpaper-page wpaper-archive">
	<section class="blog-hero hero-section wpaper-hero">
		<div class="container">
			<div class="heading text-center">
				<span class="wpaper-eyebrow"><?= hr_whitepaper_icon( 'file-text', 15 ); ?> Research &amp; Insights</span>
				<h1>Whitepaper Library</h1>
				<p>Want to explore advanced healthcare technologies? And, eager to know how it evolves the dynamics of the hospital environment? If yes, you will keep reading our analytical whitepaper information.</p>
			</div>

			<div class="hr-blog-archive__search wpaper-hero__search">
				<?php get_search_form(); ?>
			</div>

			<?php if ( $stats['total'] ) : ?>
				<ul class="wpaper-stats" aria-label="The library at a glance">
					<li>
						<?= hr_whitepaper_icon( 'layers', 16 ); ?>
						<span><strong><?= (int) $stats['total']; ?></strong> <?= 1 === (int) $stats['total'] ? 'free whitepaper' : 'free whitepapers'; ?></span>
					</li>
					<?php if ( $stats['latest'] ) : ?>
						<li>
							<?= hr_whitepaper_icon( 'calendar', 16 ); ?>
							<span>Latest: <strong><?= esc_html( $stats['latest'] ); ?></strong></span>
						</li>
					<?php endif; ?>
					<li>
						<?= hr_whitepaper_icon( 'download', 16 ); ?>
						<span>Instant PDF download</span>
					</li>
				</ul>
			<?php endif; ?>
		</div>
	</section>

	<section class="wpaper-section wpaper-listing" aria-labelledby="wpaper-listing-title">
		<div class="container">
			<?php if ( ! $items && ! $featured ) : ?>
				<h2 class="visually-hidden" id="wpaper-listing-title"><?= esc_html( $list_title ); ?></h2>

				<div class="wpaper-empty">
					<span class="wpaper-empty__icon"><?= hr_whitepaper_icon( $is_search ? 'search' : 'file-text', 30 ); ?></span>

					<?php if ( $is_search ) : ?>
						<h3 class="wpaper-empty__title">No whitepapers match &ldquo;<?= esc_html( $search_query ); ?>&rdquo;</h3>
						<p class="wpaper-empty__text">Try a broader term such as &ldquo;EMR&rdquo;, &ldquo;ROI&rdquo; or &ldquo;compliance&rdquo;, or browse the full library.</p>
						<div class="wpaper-empty__actions">
							<a class="wpaper-btn wpaper-btn--primary" href="<?= esc_url( $archive_url ); ?>">Browse All Whitepapers <?= hr_whitepaper_icon( 'arrow-right', 16 ); ?></a>
						</div>
					<?php else : ?>
						<h3 class="wpaper-empty__title">New whitepapers are on their way</h3>
						<p class="wpaper-empty__text">Our research team is preparing the next reports. In the meantime, see how Healthray supports hospitals, clinics, labs and pharmacies.</p>
						<div class="wpaper-empty__actions">
							<a class="wpaper-btn wpaper-btn--primary hr-cta-btn" href="<?= esc_url( $contact_url ); ?>">Book a Free Demo <?= hr_whitepaper_icon( 'arrow-right', 16 ); ?></a>
						</div>
					<?php endif; ?>
				</div>
			<?php else : ?>

				<?php if ( $featured ) : ?>
					<div class="wpaper-featured">
						<?php
						get_template_part(
							'template-parts/whitepaper',
							'card',
							array(
								'item'    => $featured,
								'variant' => 'featured',
								'label'   => 'Latest release',
								'heading' => 'h2',
							)
						);
						?>
					</div>
				<?php endif; ?>

				<?php if ( $items ) : ?>
					<div class="wpaper-section-head">
						<div class="wpaper-section-head__text">
							<h2 class="wpaper-section-title" id="wpaper-listing-title"><?= esc_html( $list_title ); ?></h2>
							<?php if ( $is_search ) : ?>
								<p class="wpaper-section-lead" role="status">
									<?= (int) $found; ?> <?= 1 === $found ? 'whitepaper matches' : 'whitepapers match'; ?> &ldquo;<?= esc_html( $search_query ); ?>&rdquo;.
								</p>
							<?php else : ?>
								<p class="wpaper-section-lead">Research reports and guides from the Healthray team, newest first.</p>
							<?php endif; ?>
						</div>

						<?php if ( $is_search ) : ?>
							<a class="wpaper-clear" href="<?= esc_url( $archive_url ); ?>"><?= hr_whitepaper_icon( 'x', 15 ); ?> Clear search</a>
						<?php elseif ( $found > 0 ) : ?>
							<p class="wpaper-count">Showing <strong><?= (int) $first; ?>&ndash;<?= (int) $last; ?></strong> of <strong><?= (int) $found; ?></strong></p>
						<?php endif; ?>
					</div>

					<ul class="wpaper-grid">
						<?php foreach ( $items as $item ) : ?>
							<li><?php get_template_part( 'template-parts/whitepaper', 'card', array( 'item' => $item ) ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<h2 class="visually-hidden" id="wpaper-listing-title"><?= esc_html( $list_title ); ?></h2>
				<?php endif; ?>

				<?php pagination_bar(); ?>
			<?php endif; ?>
		</div>
	</section>

	<section class="wpaper-section wpaper-section--cta" aria-labelledby="wpaper-cta-title">
		<div class="container">
			<div class="wpaper-cta">
				<div class="wpaper-cta__content">
					<span class="wpaper-eyebrow wpaper-eyebrow--on-dark">From insight to action</span>
					<h2 class="wpaper-cta__title" id="wpaper-cta-title">Put these insights to work in your hospital</h2>
					<p class="wpaper-cta__text">Book a free, personalised walkthrough of Healthray&rsquo;s AI-powered, ABDM-compliant hospital management system for hospitals, clinics, labs and pharmacies.</p>
					<ul class="wpaper-cta__points">
						<li>ABDM Compliant</li>
						<li>Fast Go-Live</li>
						<li>Dedicated Support</li>
					</ul>
				</div>
				<div class="wpaper-cta__actions">
					<a class="wpaper-btn wpaper-btn--light hr-cta-btn" href="<?= esc_url( $contact_url ); ?>">Book a Free Demo <?= hr_whitepaper_icon( 'arrow-right', 16 ); ?></a>
					<a class="wpaper-btn wpaper-btn--outline-light" href="<?= esc_url( $contact_url ); ?>">Contact Our Team</a>
				</div>
			</div>
		</div>
	</section>
</main>
