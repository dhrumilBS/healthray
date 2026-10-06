<?php
/**
 * Single event.
 *
 * Built from the blog single's components so an event reads as part of the
 * same site (css/single.css loads on every single view): the hero is
 * .hr-hero-section with .hr-breadcrumb, .hr-blog-title and .hr-hero-visual,
 * the write-up sits in .hr-post-content, and the sidebar uses .hr-sidebar-card
 * and the navy .hr-sidebar-cta. The event-specific pieces (facts, calendar,
 * share, value grid, more events) live in css/events.css.
 *
 * Event data: hr_event_data() in lib/events-helpers.php. The "Book" buttons
 * carry .hr-cta-btn, which js/script.js turns into the site-wide lead popup;
 * the contact page URL is the no-JavaScript fallback.
 *
 * @package stratusx-child
 */

while ( have_posts() ) :
	the_post();

	$event         = hr_event_data();
	$is_live       = hr_event_is_live( $event['status'] );
	$calendar      = $is_live ? hr_event_calendar_links( $event ) : array();
	$share_links   = hr_event_share_links( $event );
	$related       = hr_events_related( $event['id'], 3 );
	$contact_url   = hr_events_contact_url();
	$archive_url   = get_post_type_archive_link( 'events' );
	$archive_obj   = get_post_type_object( 'events' );
	$archive_label = $archive_obj ? $archive_obj->labels->name : 'Events';
	$has_visual    = has_post_thumbnail();
	$has_content   = '' !== trim( wp_strip_all_tags( get_the_content() ) );
	$place_label   = $event['is_online'] ? 'Online' : 'Venue';
	$place_icon    = $event['is_online'] ? 'globe' : 'pin';
	$cta_label     = $is_live ? 'Book a Meeting' : 'Book a Free Demo';
	?>
	<main class="ev-page ev-single hr-single-wrapper" id="ev-main">
		<article <?php post_class( 'ev-single__article' ); ?>>

			<header class="hr-hero-section ev-single__hero">
				<div class="container">
					<div class="hr-hero-grid<?= $has_visual ? '' : ' hr-hero-grid--no-visual'; ?>">
						<div class="hr-hero-content">
							<nav class="hr-breadcrumb" aria-label="Breadcrumb">
								<ol>
									<?php if ( $archive_url ) : ?>
										<li><a href="<?= esc_url( $archive_url ); ?>"><?= esc_html( $archive_label ); ?></a></li>
									<?php endif; ?>
									<li><span class="hr-breadcrumb-current" aria-current="page"><?= wp_kses_post( $event['title'] ); ?></span></li>
								</ol>
							</nav>

							<?= hr_event_badges( $event, true ); ?>

							<h1 class="hr-blog-title ev-single__title"><?php the_title(); ?></h1>

							<?php if ( has_excerpt() ) : // Hand-written excerpt only, as on blog posts. ?>
								<p class="hr-hero-lead"><?= esc_html( get_the_excerpt() ); ?></p>
							<?php endif; ?>

							<ul class="ev-facts">
								<li class="ev-fact">
									<span class="ev-fact__icon"><?= hr_event_icon( 'calendar', 20 ); ?></span>
									<span class="ev-fact__text">
										<span class="ev-fact__label">Date</span>
										<span class="ev-fact__value">
											<?php if ( $event['start_iso'] ) : ?>
												<time datetime="<?= esc_attr( $event['start_iso'] ); ?>"><?= esc_html( $event['date_label'] ); ?></time>
											<?php else : ?>
												To be announced
											<?php endif; ?>
										</span>
									</span>
								</li>
								<?php if ( '' !== $event['location'] ) : ?>
									<li class="ev-fact">
										<span class="ev-fact__icon"><?= hr_event_icon( $place_icon, 20 ); ?></span>
										<span class="ev-fact__text">
											<span class="ev-fact__label"><?= esc_html( $place_label ); ?></span>
											<span class="ev-fact__value"><?= esc_html( $event['location'] ); ?></span>
										</span>
									</li>
								<?php endif; ?>
							</ul>

							<div class="ev-actions">
								<a class="ev-btn ev-btn--primary hr-cta-btn" href="<?= esc_url( $contact_url ); ?>"><?= esc_html( $cta_label ); ?> <?= hr_event_icon( 'arrow-right', 16 ); ?></a>

								<?php if ( $calendar ) : ?>
									<details class="ev-cal" data-ev-cal>
										<summary class="ev-btn ev-btn--ghost">
											<?= hr_event_icon( 'calendar-plus', 18 ); ?>
											Add to Calendar
											<span class="ev-cal__chevron"><?= hr_event_icon( 'chevron-down', 16 ); ?></span>
										</summary>
										<ul class="ev-cal__menu">
											<li>
												<a class="ev-cal__item" href="<?= esc_url( $calendar['google'] ); ?>" target="_blank" rel="noopener noreferrer">
													<?= hr_event_icon( 'calendar', 18 ); ?>
													<span>Google Calendar<span class="visually-hidden"> (opens in a new tab)</span></span>
												</a>
											</li>
											<li>
												<a class="ev-cal__item" href="<?= esc_attr( $calendar['ics'] ); ?>" download="<?= esc_attr( $calendar['ics_filename'] ); ?>">
													<?= hr_event_icon( 'download', 18 ); ?>
													<span>Apple or Outlook (.ics)</span>
												</a>
											</li>
										</ul>
									</details>
								<?php elseif ( $archive_url ) : ?>
									<a class="ev-btn ev-btn--ghost" href="<?= esc_url( $archive_url ); ?>">Browse All Events</a>
								<?php endif; ?>
							</div>
						</div>

						<?php if ( $has_visual ) : ?>
							<div class="hr-hero-visual">
								<div class="hr-hero-visual-frame">
									<?php
									the_post_thumbnail(
										'large',
										array(
											'class'         => 'hr-hero-img',
											'loading'       => 'eager',
											'fetchpriority' => 'high',
											'decoding'      => 'async',
											'sizes'         => '(max-width: 991px) 100vw, 600px',
										)
									);
									?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</header>

			<div class="container">
				<div class="ev-single__layout">
					<div class="ev-single__main">

						<?php if ( $has_content ) : ?>
							<section class="ev-block" aria-labelledby="ev-about-title">
								<h2 class="ev-block__title" id="ev-about-title">About the Event</h2>
								<div class="hr-post-content entry-content">
									<?php the_content(); ?>
								</div>
							</section>
						<?php endif; ?>

						<section class="ev-block ev-value" aria-labelledby="ev-value-title">
							<span class="ev-eyebrow">Meet Healthray</span>
							<h2 class="ev-block__title" id="ev-value-title">What Our Team Can Walk You Through</h2>
							<p class="ev-block__lead">Healthray is an AI-powered, ABDM-compliant hospital management system for India&rsquo;s hospitals, clinics, labs and pharmacies.</p>
							<ul class="ev-value__grid">
								<li class="ev-value__item">
									<span class="ev-value__icon"><?= hr_event_icon( 'activity', 22 ); ?></span>
									<h3 class="ev-value__title">One Connected Platform</h3>
									<p class="ev-value__text">Registration, OPD and IPD, EMR, pharmacy, lab and billing working from the same real-time data.</p>
								</li>
								<li class="ev-value__item">
									<span class="ev-value__icon"><?= hr_event_icon( 'shield-check', 22 ); ?></span>
									<h3 class="ev-value__title">ABDM Compliant</h3>
									<p class="ev-value__text">Create and verify ABHA IDs at registration and link health records to the ABDM ecosystem.</p>
								</li>
								<li class="ev-value__item">
									<span class="ev-value__icon"><?= hr_event_icon( 'zap', 22 ); ?></span>
									<h3 class="ev-value__title">Fast Go-Live</h3>
									<p class="ev-value__text">Most clinics go live within days; mid-size hospitals typically within one to three weeks.</p>
								</li>
								<li class="ev-value__item">
									<span class="ev-value__icon"><?= hr_event_icon( 'headset', 22 ); ?></span>
									<h3 class="ev-value__title">Dedicated Onboarding</h3>
									<p class="ev-value__text">A dedicated team handles data migration, workflow setup and staff training.</p>
								</li>
							</ul>
						</section>
					</div>

					<aside class="ev-single__aside" aria-label="Event details">
						<div class="ev-single__sticky">
							<div class="hr-sidebar-card ev-details" id="ev-details">
								<p class="hr-sidebar-widget-title"><?= hr_event_icon( 'calendar-days', 18 ); ?> Event Details</p>

								<dl class="ev-details__list">
									<div class="ev-details__row">
										<dt><span class="ev-details__icon"><?= hr_event_icon( 'calendar', 18 ); ?></span>Date</dt>
										<dd>
											<?php if ( $event['start_iso'] ) : ?>
												<time datetime="<?= esc_attr( $event['start_iso'] ); ?>"><?= esc_html( $event['date_label'] ); ?></time>
												<?php if ( $event['days'] > 1 ) : ?>
													<span class="ev-details__sub"><?= (int) $event['days']; ?> days</span>
												<?php endif; ?>
											<?php else : ?>
												To be announced
											<?php endif; ?>
										</dd>
									</div>

									<?php if ( '' !== $event['location'] ) : ?>
										<div class="ev-details__row">
											<dt><span class="ev-details__icon"><?= hr_event_icon( $place_icon, 18 ); ?></span><?= esc_html( $place_label ); ?></dt>
											<dd><?= esc_html( $event['location'] ); ?></dd>
										</div>
									<?php endif; ?>

									<?php if ( '' !== $event['type'] ) : ?>
										<div class="ev-details__row">
											<dt><span class="ev-details__icon"><?= hr_event_icon( 'tag', 18 ); ?></span>Format</dt>
											<dd><?= esc_html( $event['type'] ); ?></dd>
										</div>
									<?php endif; ?>

									<?php if ( $event['terms'] ) : ?>
										<div class="ev-details__row">
											<dt><span class="ev-details__icon"><?= hr_event_icon( 'flag', 18 ); ?></span>Category</dt>
											<dd><?= esc_html( implode( ', ', $event['terms'] ) ); ?></dd>
										</div>
									<?php endif; ?>

									<div class="ev-details__row">
										<dt><span class="ev-details__icon"><?= hr_event_icon( 'clock', 18 ); ?></span>Status</dt>
										<dd><span class="ev-status ev-status--<?= esc_attr( $event['status'] ); ?>"><?= esc_html( $event['status_label'] ); ?></span></dd>
									</div>
								</dl>

								<div class="ev-share">
									<p class="ev-share__label" id="ev-share-label">Share this event</p>
									<ul class="ev-share__list" aria-labelledby="ev-share-label">
										<?php foreach ( $share_links as $network => $link ) : ?>
											<li>
												<a class="ev-share__btn ev-share__btn--<?= esc_attr( $network ); ?>" href="<?= esc_url( $link['url'] ); ?>" target="_blank" rel="noopener noreferrer">
													<?= hr_event_icon( $network, 18 ); ?>
													<span class="visually-hidden">Share on <?= esc_html( $link['label'] ); ?> (opens in a new tab)</span>
												</a>
											</li>
										<?php endforeach; ?>
										<li hidden>
											<button type="button" class="ev-share__btn" data-ev-copy="<?= esc_url( $event['url'] ); ?>">
												<span class="ev-share__icon-idle"><?= hr_event_icon( 'link', 18 ); ?></span>
												<span class="ev-share__icon-done"><?= hr_event_icon( 'check', 18 ); ?></span>
												<span class="visually-hidden">Copy link</span>
											</button>
										</li>
									</ul>
									<p class="ev-share__status" role="status" data-ev-copy-status></p>
								</div>
							</div>

							<div class="hr-sidebar-cta ev-single__cta">
								<p class="hr-sidebar-cta__heading"><?= $is_live ? 'Planning to attend? Meet the Healthray team' : 'Missed this event? See Healthray in action'; ?></p>
								<ul class="hr-sidebar-cta__list">
									<li>ABDM Compliant</li>
									<li>Fast Go-Live</li>
									<li>Dedicated Support</li>
								</ul>
								<a class="hr-sidebar-cta__btn hr-cta-btn" href="<?= esc_url( $contact_url ); ?>"><?= esc_html( $cta_label ); ?></a>
							</div>
						</div>
					</aside>
				</div>
			</div>
		</article>

		<?php if ( $related ) : ?>
			<section class="ev-section ev-related" aria-labelledby="ev-related-title">
				<div class="container">
					<div class="ev-section-head">
						<div class="ev-section-head__text">
							<span class="ev-eyebrow">Keep exploring</span>
							<h2 class="ev-section-title" id="ev-related-title">More Healthray Events</h2>
						</div>
						<?php if ( $archive_url ) : ?>
							<a class="ev-link" href="<?= esc_url( $archive_url ); ?>">View all events <?= hr_event_icon( 'arrow-right', 16 ); ?></a>
						<?php endif; ?>
					</div>

					<ul class="ev-grid">
						<?php foreach ( $related as $related_event ) : ?>
							<li><?php get_template_part( 'template-parts/event', 'card', array( 'event' => $related_event ) ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</section>
		<?php endif; ?>
	</main>
	<?php
endwhile;
