<?php
/**
 * Case study listing card.
 *
 * Used by archive-case-studies.php (featured card and grid) and by the
 * "More case studies" list in single-case-studies.php.
 *
 * Args (get_template_part):
 *   post_id  int    Case study to render. Defaults to the current post.
 *   featured bool   Render the large featured card (an <article>) instead of
 *                   a grid card (an <li>, so wrap calls in <ul class="cards">).
 *
 * The whole card is clickable through the heading link's ::after overlay.
 *
 * @package stratusx-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cs_id       = ! empty( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();
$cs_featured = ! empty( $args['featured'] );
$cs_title    = trim( (string) get_field( 'card_title', $cs_id ) );
$cs_title    = '' !== $cs_title ? $cs_title : get_the_title( $cs_id );
$cs_location = trim( (string) get_field( 'card_location', $cs_id ) );
$cs_terms    = hr_cs_terms( $cs_id );
$cs_metrics  = hr_cs_card_metrics( $cs_id, $cs_featured ? 3 : 2 );
$cs_summary  = $cs_featured ? trim( (string) get_post_field( 'post_excerpt', $cs_id ) ) : '';
$cs_thumb    = get_post_thumbnail_id( $cs_id );
$cs_cats     = implode( ' ', wp_list_pluck( $cs_terms, 'slug' ) );
$cs_tag      = $cs_featured ? 'article' : 'li';

// Grid cards repeat the heading as their link text, so their photo is
// decorative. The featured photo is described, as in the design.
$cs_img_attr = array(
	'alt'      => '',
	'decoding' => 'async',
);
if ( $cs_featured ) {
	$cs_img_attr['alt']           = hr_cs_image_alt( $cs_id );
	$cs_img_attr['fetchpriority'] = 'high';
} else {
	$cs_img_attr['loading'] = 'lazy';
}
?>
<<?php echo $cs_tag; // phpcs:ignore WordPress.Security.EscapeOutput -- fixed value. ?> class="<?php echo $cs_featured ? 'featured' : 'card'; ?>" data-cat="<?php echo esc_attr( $cs_cats ); ?>">
	<div class="card-img">
		<?php
		if ( $cs_thumb ) {
			echo wp_get_attachment_image( $cs_thumb, $cs_featured ? 'full' : 'large', false, $cs_img_attr );
		}
		?>
	</div>
	<div class="card-body">
		<div class="card-meta">
			<?php if ( $cs_featured ) : ?>
				<span class="badge">Featured</span>
			<?php endif; ?>
			<?php foreach ( $cs_terms as $cs_term ) : ?>
				<span class="tag"><?php echo esc_html( $cs_term->name ); ?></span>
			<?php endforeach; ?>
			<?php if ( $cs_location ) : ?>
				<span class="loc"><?php echo esc_html( $cs_location ); ?></span>
			<?php endif; ?>
		</div>
		<?php if ( $cs_featured ) : ?>
			<h2><a href="<?php echo esc_url( get_permalink( $cs_id ) ); ?>"><?php echo esc_html( $cs_title ); ?></a></h2>
			<?php if ( $cs_summary ) : ?>
				<p class="sum"><?php echo esc_html( $cs_summary ); ?></p>
			<?php endif; ?>
		<?php else : ?>
			<h3><a href="<?php echo esc_url( get_permalink( $cs_id ) ); ?>"><?php echo esc_html( $cs_title ); ?></a></h3>
		<?php endif; ?>
		<?php if ( $cs_metrics ) : ?>
			<div class="metrics">
				<?php foreach ( $cs_metrics as $cs_metric ) : ?>
					<div><span class="m-num"><?php echo esc_html( $cs_metric['title'] ); ?></span><span class="m-lbl"><?php echo esc_html( $cs_metric['text'] ?? '' ); ?></span></div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<span class="card-link">Read case study <span aria-hidden="true">→</span></span>
	</div>
</<?php echo $cs_tag; // phpcs:ignore WordPress.Security.EscapeOutput -- fixed value. ?>>
