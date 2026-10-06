<?php
/**
 * Events archive.
 *
 * Page 1 opens with every event that can still be attended (the spotlight).
 * It is built from hr_events_index(), so it is complete whatever page an event
 * would land on. The paginated main query lists the rest; on page 1 it skips
 * the events the spotlight already shows. When nothing is upcoming, the newest
 * event is presented as a wide featured card instead and a short notice says so.
 *
 * - Main query order (event date, newest first): lib/events-helpers.php.
 *   Posts per page, pagination and URLs are unchanged.
 * - Cards: template-parts/event-card.php. Styles: css/events.css.
 * - Format filter: js/events.js. It filters the cards on the current page and
 *   stays hidden without JavaScript, when every card is listed.
 * - Reused from the blog: .blog-hero.hero-section and .hr-blog-archive__search
 *   (css/custom.css), pagination_bar() with the shared .pagination styles.
 *
 * @package stratusx-child
 */

$is_first_page = ! is_paged();
$contact_url   = hr_events_contact_url();
$stats         = hr_events_stats();
$spotlight     = $is_first_page ? hr_events_upcoming( 4 ) : array();
$spotlight_ids = array_map( 'intval', wp_list_pluck( $spotlight, 'id' ) );

// Cards for the listing, minus anything the spotlight already shows.
$listing = array();
while ( have_posts() ) {
	the_post();

	if ( $is_first_page && in_array( get_the_ID(), $spotlight_ids, true ) ) {
		continue;
	}

	$event = hr_event_data();
	if ( $event ) {
		$listing[] = $event;
	}
}
rewind_posts();

$has_events    = $listing || $spotlight;
$has_pages     = $GLOBALS['wp_query']->max_num_pages > 1;
$listing_live  = (bool) array_filter( $listing, fn( $event ) => hr_event_is_live( $event['status'] ) );
$feature_first = $is_first_page && ! $spotlight && count( $listing ) > 2;

// Format filter options, counted from the cards on this page only.
$formats = array();
foreach ( $listing as $event ) {
	if ( '' === $event['type_key'] ) {
		continue;
	}
	if ( ! isset( $formats[ $event['type_key'] ] ) ) {
		$formats[ $event['type_key'] ] = array(
			'label' => $event['type'],
			'count' => 0,
		);
	}
	++$formats[ $event['type_key'] ]['count'];
}
uasort( $formats, fn( $a, $b ) => $b['count'] <=> $a['count'] );
?>
<main class="ev-page ev-archive">
	<section class="blog-hero hero-section ev-hero">
		<div class="container">
			<div class="heading text-center">
				<span class="ev-eyebrow">Meet Healthray</span>
				<h1>Healthray Events Showcase</h1>
				<p>Discover our healthcare innovation events, webinars, and conferences.</p>
			</div>

			<div class="hr-blog-archive__search ev-hero__search">
				<?php get_search_form(); ?>
			</div>

			<?php if ( $stats['total'] ) : ?>
				<ul class="ev-stats" aria-label="Events at a glance">
					<li>
						<?= hr_event_icon( 'calendar-days', 16 ); ?>
						<span><strong><?= (int) $stats['total']; ?></strong> <?= 1 === (int) $stats['total'] ? 'event' : 'events'; ?><?php if ( $stats['since'] ) : ?> since <?= (int) $stats['since']; ?><?php endif; ?></span>
					</li>
					<?php if ( $stats['upcoming'] ) : ?>
						<li class="ev-stats__live">
							<?= hr_event_icon( 'calendar-plus', 16 ); ?>
							<span><strong><?= (int) $stats['upcoming']; ?></strong> upcoming</span>
						</li>
					<?php endif; ?>
					<?php if ( $stats['types'] ) : ?>
						<li>
							<?= hr_event_icon( 'tag', 16 ); ?>
							<span><?= esc_html( implode( ' · ', array_slice( $stats['types'], 0, 4 ) ) ); ?></span>
						</li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>
		</div>
	</section>

	<?php if ( ! $has_events ) : ?>
		<section class="ev-section">
			<div class="container">
				<div class="ev-empty">
					<span class="ev-empty__icon"><?= hr_event_icon( 'calendar-days', 30 ); ?></span>
					<h2 class="ev-empty__title">No events to show yet</h2>
					<p class="ev-empty__text">We&rsquo;re lining up our next conferences, expos and webinars. In the meantime, see how Healthray can support your hospital, clinic, lab or pharmacy.</p>
					<div class="ev-empty__actions">
						<a class="ev-btn ev-btn--primary hr-cta-btn" href="<?= esc_url( $contact_url ); ?>">Book a Free Demo <?= hr_event_icon( 'arrow-right', 16 ); ?></a>
						<?php
						$blog_page_id = (int) get_option( 'page_for_posts' );
						if ( $blog_page_id && 'publish' === get_post_status( $blog_page_id ) ) :
							?>
							<a class="ev-btn ev-btn--ghost" href="<?= esc_url( get_permalink( $blog_page_id ) ); ?>">Read the Healthray Blog</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
	<?php else : ?>

		<?php if ( $spotlight ) : ?>
			<section class="ev-section ev-spotlight" aria-labelledby="ev-spotlight-title">
				<div class="container">
					<div class="ev-section-head">
						<div class="ev-section-head__text">
							<span class="ev-eyebrow">Next up</span>
							<h2 class="ev-section-title" id="ev-spotlight-title"><?= 1 === count( $spotlight ) ? 'Upcoming event' : 'Upcoming events'; ?></h2>
							<p class="ev-section-lead">Save the date, then book time with the Healthray team before you go.</p>
						</div>
					</div>

					<?php
					get_template_part(
						'template-parts/event',
						'card',
						array(
							'event'   => $spotlight[0],
							'variant' => 'featured',
							'label'   => 'ongoing' === $spotlight[0]['status'] ? 'Happening now' : 'Next up',
						)
					);
					?>

					<?php if ( count( $spotlight ) > 1 ) : ?>
						<ul class="ev-grid ev-grid--spotlight">
							<?php foreach ( array_slice( $spotlight, 1 ) as $event ) : ?>
								<li><?php get_template_part( 'template-parts/event', 'card', array( 'event' => $event ) ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $listing || $has_pages ) : ?>
		<section class="ev-section ev-listing"<?= $listing ? ' aria-labelledby="ev-listing-title"' : ' aria-label="More events"'; ?>>
			<div class="container">
				<?php if ( $is_first_page && ! $spotlight ) : ?>
					<div class="ev-notice">
						<span class="ev-notice__icon"><?= hr_event_icon( 'calendar-plus', 22 ); ?></span>
						<div class="ev-notice__body">
							<p class="ev-notice__title">No upcoming events announced yet</p>
							<p class="ev-notice__text">New conferences, expos and webinars are listed here first. Want to meet the Healthray team sooner?</p>
						</div>
						<a class="ev-btn ev-btn--ghost hr-cta-btn" href="<?= esc_url( $contact_url ); ?>">Book a Meeting</a>
					</div>
				<?php endif; ?>

				<?php if ( $listing ) : ?>
					<div class="ev-section-head">
						<div class="ev-section-head__text">
							<h2 class="ev-section-title" id="ev-listing-title"><?= $listing_live ? 'All events' : 'Past events'; ?></h2>
							<p class="ev-section-lead"><?= $listing_live ? 'Conferences, expos, workshops and webinars with Healthray, newest first.' : 'A look back at the conferences, expos, workshops and webinars Healthray has been part of.'; ?></p>
						</div>

						<?php if ( count( $formats ) > 1 ) : ?>
							<div class="ev-filter" data-ev-filter hidden>
								<span class="ev-filter__label" id="ev-filter-label">Format</span>
								<div class="ev-filter__options" role="group" aria-labelledby="ev-filter-label">
									<button type="button" class="ev-filter__btn" data-ev-type="all" data-ev-label="" aria-pressed="true">
										All <span class="ev-filter__count"><?= count( $listing ); ?></span>
									</button>
									<?php foreach ( $formats as $key => $format ) : ?>
										<button type="button" class="ev-filter__btn" data-ev-type="<?= esc_attr( $key ); ?>" data-ev-label="<?= esc_attr( $format['label'] ); ?>" aria-pressed="false">
											<?= esc_html( $format['label'] ); ?> <span class="ev-filter__count"><?= (int) $format['count']; ?></span>
										</button>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>
					</div>

					<p class="visually-hidden" role="status" data-ev-filter-status></p>

					<ul class="ev-grid" data-ev-grid>
						<?php foreach ( $listing as $index => $event ) : ?>
							<?php $is_featured = $feature_first && 0 === $index; ?>
							<li<?= $is_featured ? ' class="ev-grid__item--featured"' : ''; ?> data-ev-type="<?= esc_attr( $event['type_key'] ); ?>">
								<?php
								get_template_part(
									'template-parts/event',
									'card',
									array(
										'event'   => $event,
										'variant' => $is_featured ? 'featured' : '',
										'label'   => $is_featured ? 'Latest event' : '',
									)
								);
								?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php pagination_bar(); ?>
			</div>
		</section>
		<?php endif; ?>

		<section class="ev-section ev-section--cta" aria-labelledby="ev-cta-title">
			<div class="container">
				<div class="ev-cta">
					<div class="ev-cta__content">
						<span class="ev-eyebrow ev-eyebrow--on-dark">Can&rsquo;t wait for the next event?</span>
						<h2 class="ev-cta__title" id="ev-cta-title">See Healthray in action, on your schedule</h2>
						<p class="ev-cta__text">Book a free, personalised walkthrough of Healthray&rsquo;s AI-powered, ABDM-compliant hospital management system for hospitals, clinics, labs and pharmacies.</p>
						<ul class="ev-cta__points">
							<li>ABDM Compliant</li>
							<li>Fast Go-Live</li>
							<li>Dedicated Support</li>
						</ul>
					</div>
					<div class="ev-cta__actions">
						<a class="ev-btn ev-btn--light hr-cta-btn" href="<?= esc_url( $contact_url ); ?>">Book a Free Demo <?= hr_event_icon( 'arrow-right', 16 ); ?></a>
						<a class="ev-btn ev-btn--outline-light" href="<?= esc_url( $contact_url ); ?>">Contact Our Team</a>
					</div>
				</div>
			</div>
		</section>

	<?php endif; ?>
</main>
