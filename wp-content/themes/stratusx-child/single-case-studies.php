<?php
/**
 * Single case study.
 *
 * Design: case-studies/v3-vibrant-hospital.html. Fields:
 * lib/acf-case-studies.php. Styles: css/case-studies.css (enqueued by the CPT
 * registry). Every section is skipped when it has no content.
 *
 * @package stratusx-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cs_id         = get_the_ID();
$cs_title      = get_the_title();
$cs_short_name = trim( (string) get_field( 'short_name' ) );
$cs_short_name = '' !== $cs_short_name ? $cs_short_name : $cs_title;
$cs_hero_label = trim( (string) get_field( 'hero_label' ) );
$cs_hero_lede  = trim( (string) get_field( 'hero_lede' ) );
$cs_thumb      = get_post_thumbnail_id();
$cs_hero_alt   = hr_cs_image_alt( $cs_id );
$cs_metrics    = hr_cs_rows( get_field( 'metrics' ), 'title' );
$cs_facts      = hr_cs_rows( get_field( 'glance_facts' ), 'label' );
$cs_modules    = hr_cs_rows( get_field( 'modules_used' ), 'name' );
$cs_demo_url   = hr_cs_demo_url();

/**
 * Story sections in page order. Each has a label, heading and intro; the
 * section-specific parts are rendered in the switch below.
 */
$cs_sections = array();
foreach ( array( 'overview', 'problem', 'decision', 'solution', 'setup', 'results' ) as $cs_key ) {
	$cs_sections[ $cs_key ] = array(
		'label'   => trim( (string) get_field( $cs_key . '_label' ) ),
		'heading' => trim( (string) get_field( $cs_key . '_heading' ) ),
		'intro'   => 'overview' === $cs_key ? get_field( 'overview_content' ) : get_field( $cs_key . '_intro' ),
	);
}

$cs_problems     = hr_cs_rows( get_field( 'problems' ), 'title' );
$cs_problem_out  = get_field( 'problem_outro' );
$cs_quote        = trim( (string) get_field( 'quote_text' ) );
$cs_quote_name   = trim( (string) get_field( 'quote_name' ) );
$cs_quote_role   = trim( (string) get_field( 'quote_role' ) );
$cs_solutions    = hr_cs_rows( get_field( 'solutions' ), 'title' );
$cs_steps        = hr_cs_rows( get_field( 'steps' ), 'title' );
$cs_results      = hr_cs_rows( get_field( 'results' ), 'title' );
$cs_ba_rows      = hr_cs_rows( get_field( 'ba_rows' ), 'area' );
$cs_ba_title     = trim( (string) get_field( 'ba_title' ) );
$cs_ba_before    = trim( (string) get_field( 'ba_before_label' ) ) ?: 'Before Healthray';
$cs_ba_after     = trim( (string) get_field( 'ba_after_label' ) ) ?: 'With Healthray';
$cs_fit_heading  = trim( (string) get_field( 'fit_heading' ) );
$cs_fit_intro    = trim( (string) get_field( 'fit_intro' ) );
$cs_fit_points   = hr_cs_rows( get_field( 'fit_points' ), 'text' );
$cs_has_glance   = $cs_facts || $cs_modules;

// Section-specific parts that keep a section visible without a heading or intro.
$cs_has_parts = array(
	'overview' => false,
	'problem'  => $cs_problems || $cs_problem_out,
	'decision' => '' !== $cs_quote,
	'solution' => (bool) $cs_solutions,
	'setup'    => (bool) $cs_steps,
	'results'  => $cs_results || $cs_ba_rows,
);

$cs_related = get_posts(
	array(
		'post_type'      => 'case-studies',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'post__not_in'   => array( $cs_id ),
		'no_found_rows'  => true,
		'fields'         => 'ids',
	)
);

?>
<main class="hrcs">

	<section class="hero">
		<div class="wrap">
			<div class="hero-grid<?php echo $cs_thumb ? '' : ' hero-grid--solo'; ?>">
				<div>
					<nav class="crumbs" aria-label="Breadcrumb">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a><span aria-hidden="true">/</span>
						<a href="<?php echo esc_url( get_post_type_archive_link( 'case-studies' ) ); ?>">Case studies</a><span aria-hidden="true">/</span>
						<span aria-current="page"><?php echo esc_html( $cs_short_name ); ?></span>
					</nav>
					<?php if ( $cs_hero_label ) : ?>
						<p class="label"><?php echo esc_html( $cs_hero_label ); ?></p>
					<?php endif; ?>
					<h1><?php echo esc_html( $cs_title ); ?></h1>
					<?php if ( $cs_hero_lede ) : ?>
						<p class="lede"><?php echo esc_html( $cs_hero_lede ); ?></p>
					<?php endif; ?>
					<div class="btns">
						<a class="btn" href="<?php echo esc_url( $cs_demo_url ); ?>">Book a free demo</a>
					</div>
				</div>
				<?php if ( $cs_thumb ) : ?>
					<figure class="hero-photo">
						<?php
						// The photo is hidden below 768px (css/case-studies.css). The blank
						// source stops phones downloading it; keep the two widths in step.
						?>
						<picture>
							<source media="(max-width: 767px)" srcset="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7">
						<?php
						echo wp_get_attachment_image(
							$cs_thumb,
							'full',
							false,
							array(
								'alt'           => $cs_hero_alt,
								'loading'       => 'eager',
								'fetchpriority' => 'high',
								'decoding'      => 'async',
							)
						);
						?>
						</picture>
					</figure>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<?php if ( $cs_metrics ) : ?>
		<section class="wrap strip-wrap" aria-label="Key results">
			<ul class="strip<?php echo 3 === count( $cs_metrics ) ? ' strip--3' : ''; ?>">
				<?php foreach ( $cs_metrics as $cs_metric ) : ?>
					<li><strong class="strip-num"><?php echo esc_html( $cs_metric['title'] ); ?></strong><span class="strip-lbl"><?php echo esc_html( $cs_metric['text'] ?? '' ); ?></span></li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endif; ?>

	<div class="wrap layout<?php echo $cs_has_glance ? '' : ' layout--solo'; ?>">

		<?php if ( $cs_has_glance ) : ?>
			<aside class="glance" aria-labelledby="cs-glance-h">
				<div class="glance-card">
					<h2 class="glance-h" id="cs-glance-h">At a glance</h2>
					<?php if ( $cs_facts ) : ?>
						<dl class="facts">
							<?php foreach ( $cs_facts as $cs_fact ) : ?>
								<div>
									<dt><?php echo esc_html( $cs_fact['label'] ); ?></dt>
									<dd>
										<?php if ( ! empty( $cs_fact['url'] ) ) : ?>
											<a href="<?php echo esc_url( $cs_fact['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $cs_fact['value'] . ' (opens in a new tab)' ); ?>"><?php echo esc_html( $cs_fact['value'] ); ?></a>
										<?php else : ?>
											<?php echo esc_html( $cs_fact['value'] ?? '' ); ?>
										<?php endif; ?>
									</dd>
								</div>
							<?php endforeach; ?>
						</dl>
					<?php endif; ?>
					<?php if ( $cs_modules ) : ?>
						<p class="glance-sub">Modules used</p>
						<ul class="chips">
							<?php foreach ( $cs_modules as $cs_module ) : ?>
								<li><?php echo esc_html( $cs_module['name'] ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<a class="btn btn-block" href="<?php echo esc_url( $cs_demo_url ); ?>">Book a free demo</a>
				</div>
			</aside>
		<?php endif; ?>

		<article class="story" id="story">

			<?php
			foreach ( $cs_sections as $cs_key => $cs_sec ) :
				if ( '' === $cs_sec['heading'] && ! trim( wp_strip_all_tags( (string) $cs_sec['intro'] ) ) && ! $cs_has_parts[ $cs_key ] ) {
					continue;
				}

				$cs_h_id = 'cs-' . $cs_key;
				?>
				<section class="sec"<?php echo $cs_sec['heading'] ? ' aria-labelledby="' . esc_attr( $cs_h_id ) . '"' : ''; ?>>
					<?php if ( $cs_sec['label'] ) : ?>
						<p class="label"><?php echo esc_html( $cs_sec['label'] ); ?></p>
					<?php endif; ?>
					<?php if ( $cs_sec['heading'] ) : ?>
						<h2 id="<?php echo esc_attr( $cs_h_id ); ?>"><?php echo esc_html( $cs_sec['heading'] ); ?></h2>
					<?php endif; ?>
					<?php echo wp_kses_post( (string) $cs_sec['intro'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- wp_kses_post. ?>

					<?php
					switch ( $cs_key ) :
						case 'problem':
							if ( $cs_problems ) :
								?>
								<ul class="problems">
									<?php foreach ( $cs_problems as $cs_i => $cs_item ) : ?>
										<li>
											<h3><?php echo esc_html( hr_cs_numbered( $cs_i, $cs_item['title'] ) ); ?></h3>
											<?php if ( ! empty( $cs_item['text'] ) ) : ?>
												<p><?php echo esc_html( $cs_item['text'] ); ?></p>
											<?php endif; ?>
										</li>
									<?php endforeach; ?>
								</ul>
								<?php
							endif;
							if ( $cs_problem_out ) {
								// Mark the closing paragraph(s) so they get the design's top spacing.
								echo str_replace( '<p>', '<p class="sec-outro">', wp_kses_post( (string) $cs_problem_out ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- wp_kses_post.
							}
							break;

						case 'decision':
							if ( '' !== $cs_quote ) :
								$cs_initials = hr_cs_initials( $cs_quote_name );
								?>
								<figure class="quote">
									<blockquote>
										<p><?php echo esc_html( $cs_quote ); ?></p>
									</blockquote>
									<?php if ( $cs_quote_name || $cs_quote_role ) : ?>
										<figcaption>
											<?php if ( $cs_initials ) : ?>
												<span class="avatar" aria-hidden="true"><?php echo esc_html( $cs_initials ); ?></span>
											<?php endif; ?>
											<span><?php if ( $cs_quote_name ) : ?><strong><?php echo esc_html( $cs_quote_name ); ?></strong><?php endif; ?><?php echo esc_html( $cs_quote_role ); ?></span>
										</figcaption>
									<?php endif; ?>
								</figure>
								<?php
							endif;
							break;

						case 'solution':
							if ( $cs_solutions ) :
								?>
								<ul class="modules">
									<?php foreach ( $cs_solutions as $cs_i => $cs_item ) : ?>
										<li>
											<span class="m-icon" aria-hidden="true"><?php echo hr_cs_module_icon( $cs_item['icon'] ?? 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></span>
											<div>
												<h3><?php echo esc_html( hr_cs_numbered( $cs_i, $cs_item['title'] ) ); ?></h3>
												<?php if ( ! empty( $cs_item['text'] ) ) : ?>
													<p><?php echo esc_html( $cs_item['text'] ); ?></p>
												<?php endif; ?>
											</div>
										</li>
									<?php endforeach; ?>
								</ul>
								<?php
							endif;
							break;

						case 'setup':
							if ( $cs_steps ) :
								?>
								<ol class="steps<?php echo 3 === count( $cs_steps ) ? ' steps--3' : ''; ?>">
									<?php foreach ( $cs_steps as $cs_i => $cs_item ) : ?>
										<li>
											<span class="step-n" aria-hidden="true"><?php echo (int) $cs_i + 1; ?></span>
											<?php if ( ! empty( $cs_item['when'] ) ) : ?>
												<span class="step-when"><?php echo esc_html( $cs_item['when'] ); ?></span>
											<?php endif; ?>
											<h3><?php echo esc_html( $cs_item['title'] ); ?></h3>
											<?php if ( ! empty( $cs_item['text'] ) ) : ?>
												<p><?php echo esc_html( $cs_item['text'] ); ?></p>
											<?php endif; ?>
										</li>
									<?php endforeach; ?>
								</ol>
								<?php
							endif;
							break;

						case 'results':
							if ( $cs_results ) :
								?>
								<ul class="results">
									<?php foreach ( $cs_results as $cs_item ) : ?>
										<li>
											<span class="tick" aria-hidden="true"><?php echo hr_cs_glyph( 'tick' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></span>
											<div>
												<h3><?php if ( ! empty( $cs_item['number'] ) ) : ?><span class="r-num"><?php echo esc_html( $cs_item['number'] ); ?></span> <?php endif; ?><?php echo esc_html( $cs_item['title'] ); ?></h3>
												<?php if ( ! empty( $cs_item['text'] ) ) : ?>
													<p><?php echo esc_html( $cs_item['text'] ); ?></p>
												<?php endif; ?>
											</div>
										</li>
									<?php endforeach; ?>
								</ul>
								<?php
							endif;
							if ( $cs_ba_rows ) :
								?>
								<?php if ( $cs_ba_title ) : ?>
									<h3 class="ba-title"><?php echo esc_html( $cs_ba_title ); ?></h3>
								<?php endif; ?>
								<table class="ba">
									<thead>
										<tr>
											<th scope="col">Area</th>
											<th scope="col"><?php echo esc_html( $cs_ba_before ); ?></th>
											<th scope="col"><?php echo esc_html( $cs_ba_after ); ?></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ( $cs_ba_rows as $cs_row ) : ?>
											<tr>
												<th scope="row"><?php echo esc_html( $cs_row['area'] ); ?></th>
												<td data-label="<?php echo esc_attr( $cs_ba_before ); ?>"><span class="cell"><?php echo hr_cs_glyph( 'cross' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?><?php echo esc_html( $cs_row['before'] ?? '' ); ?></span></td>
												<td data-label="<?php echo esc_attr( $cs_ba_after ); ?>"><span class="cell"><?php echo hr_cs_glyph( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?><?php echo esc_html( $cs_row['after'] ?? '' ); ?></span></td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
								<?php
							endif;
							break;
					endswitch;
					?>
				</section>
			<?php endforeach; ?>

			<?php if ( $cs_fit_points ) : ?>
				<section class="sec"<?php echo $cs_fit_heading ? ' aria-labelledby="cs-fit"' : ''; ?>>
					<div class="fit">
						<?php if ( $cs_fit_heading ) : ?>
							<h2 id="cs-fit"><?php echo esc_html( $cs_fit_heading ); ?></h2>
						<?php endif; ?>
						<?php if ( $cs_fit_intro ) : ?>
							<p><?php echo esc_html( $cs_fit_intro ); ?></p>
						<?php endif; ?>
						<ul class="checks">
							<?php foreach ( $cs_fit_points as $cs_point ) : ?>
								<li><?php echo hr_cs_glyph( 'fit' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?><?php echo esc_html( $cs_point['text'] ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</section>
			<?php endif; ?>

		</article>
	</div>

	<?php
	hr_cs_cta_box(
		trim( (string) get_field( 'cta_heading' ) ),
		trim( (string) get_field( 'cta_text' ) )
	);
	?>

	<?php if ( $cs_related ) : ?>
		<section class="wrap" aria-labelledby="cs-more">
			<div class="sec-head">
				<h2 id="cs-more">More case studies</h2>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'case-studies' ) ); ?>">See all case studies <span aria-hidden="true">→</span></a>
			</div>
			<ul class="cards">
				<?php
				foreach ( $cs_related as $cs_related_id ) {
					get_template_part( 'template-parts/case-study-card', null, array( 'post_id' => $cs_related_id ) );
				}
				?>
			</ul>
		</section>
	<?php endif; ?>

	<?php hr_cs_mobile_cta(); ?>
</main>
