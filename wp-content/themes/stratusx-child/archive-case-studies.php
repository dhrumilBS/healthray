<?php
/**
 * Case studies hub.
 *
 * Design: case-studies/v3-case-studies-hub.html. Lists every published case
 * study on one page (hr_cs_archive_query() lifts the per-page limit): the
 * featured one first, then the rest as cards. The software tabs filter the
 * list client-side (js/case-studies.js) and only appear once there is more
 * than one case study to filter.
 *
 * @package stratusx-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_query;

$cs_ids = array_map( 'intval', wp_list_pluck( (array) $wp_query->posts, 'ID' ) );

// Featured: the first one flagged in the editor, otherwise the newest.
$cs_featured_id = 0;
foreach ( $cs_ids as $cs_id ) {
	if ( get_field( 'featured', $cs_id ) ) {
		$cs_featured_id = $cs_id;
		break;
	}
}
if ( ! $cs_featured_id && $cs_ids ) {
	$cs_featured_id = $cs_ids[0];
}
$cs_grid_ids = array_values( array_diff( $cs_ids, array( $cs_featured_id ) ) );

// Filter tabs: software terms in use, in the order the terms were created.
$cs_tabs = array();
foreach ( $cs_ids as $cs_id ) {
	foreach ( hr_cs_terms( $cs_id ) as $cs_term ) {
		if ( ! isset( $cs_tabs[ $cs_term->slug ] ) ) {
			$cs_tabs[ $cs_term->slug ] = array(
				'name'  => $cs_term->name,
				'order' => (int) $cs_term->term_id,
				'count' => 0,
			);
		}
		++$cs_tabs[ $cs_term->slug ]['count'];
	}
}
uasort( $cs_tabs, function ( $a, $b ) {
	return $a['order'] <=> $b['order'];
} );

$cs_total     = count( $cs_ids );
$cs_noun      = 1 === $cs_total ? 'case study' : 'case studies';
$cs_show_tabs = $cs_total > 1 && $cs_tabs;
?>
<main class="hrcs">

	<section class="hero hero--hub">
		<div class="wrap">
			<nav class="crumbs" aria-label="Breadcrumb">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a><span aria-hidden="true">/</span>
				<span aria-current="page">Case studies</span>
			</nav>
			<p class="label">Case studies</p>
			<h1>Real results from hospitals using Healthray</h1>
			<p class="lede">See how hospitals, clinics and labs across India save money, bill faster and make fewer mistakes with Healthray.</p>
			<ul class="trust">
				<li><?php echo hr_cs_glyph( 'trust' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?><span><b>2,500+</b> hospitals use Healthray</span></li>
				<li><?php echo hr_cs_glyph( 'trust' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?><span><b>5,000+</b> doctors</span></li>
				<li><?php echo hr_cs_glyph( 'trust' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?><span><b>ABDM</b> compliant</span></li>
			</ul>
		</div>
	</section>

	<section class="wrap hub-list" id="list" aria-label="Case studies">

		<?php if ( ! $cs_ids ) : ?>
			<p class="empty">No case studies yet. <a href="<?php echo esc_url( hr_cs_demo_url() ); ?>">Ask us about a hospital like yours.</a></p>
		<?php else : ?>

			<?php if ( $cs_show_tabs ) : ?>
				<div class="toolbar" data-cs-filter data-total="<?php echo (int) $cs_total; ?>">
					<div class="tabs" role="group" aria-label="Filter by software">
						<button type="button" data-filter="" aria-pressed="true">All<span class="n"><?php echo (int) $cs_total; ?></span></button>
						<?php foreach ( $cs_tabs as $cs_slug => $cs_tab ) : ?>
							<button type="button" data-filter="<?php echo esc_attr( $cs_slug ); ?>" data-name="<?php echo esc_attr( mb_strtolower( $cs_tab['name'] ) ); ?>" aria-pressed="false"><?php echo esc_html( $cs_tab['name'] ); ?><span class="n"><?php echo (int) $cs_tab['count']; ?></span></button>
						<?php endforeach; ?>
					</div>
					<p class="count" id="cs-count" role="status" aria-live="polite">Showing all <?php echo (int) $cs_total . ' ' . esc_html( $cs_noun ); ?></p>
				</div>
			<?php endif; ?>

			<?php get_template_part( 'template-parts/case-study-card', null, array( 'post_id' => $cs_featured_id, 'featured' => true ) ); ?>

			<?php if ( $cs_grid_ids ) : ?>
				<ul class="cards">
					<?php
					foreach ( $cs_grid_ids as $cs_id ) {
						get_template_part( 'template-parts/case-study-card', null, array( 'post_id' => $cs_id ) );
					}
					?>
				</ul>
			<?php endif; ?>

			<?php if ( $cs_show_tabs ) : ?>
				<p class="empty" id="cs-empty" hidden>No case study for this product yet. <a href="<?php echo esc_url( hr_cs_demo_url() ); ?>">Ask us about a hospital like yours.</a></p>
			<?php endif; ?>

		<?php endif; ?>
	</section>

	<?php
	hr_cs_cta_box(
		'Want results like these for your hospital?',
		'Book a free demo. We will show you how Healthray works and answer your questions.'
	);
	?>

	<?php hr_cs_mobile_cta(); ?>
</main>
