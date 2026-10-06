<?php
/**
 * Whitepaper card.
 *
 * Shared by archive-whitepaper.php (featured card and grid) and
 * single-whitepaper.php (more whitepapers). Pass the data from
 * hr_whitepaper_data():
 *
 *   get_template_part( 'template-parts/whitepaper', 'card', array(
 *       'item'    => hr_whitepaper_data( $post ),
 *       'variant' => 'featured',       // Optional. '' = grid card, 'featured' = wide card.
 *       'label'   => 'Latest release', // Optional eyebrow above the title.
 *       'heading' => 'h3',             // Optional. h2, h3 or h4.
 *   ) );
 *
 * Same pattern as template-parts/event-card.php: the title link is the only
 * link in the card and its ::after is stretched over the whole card, so the
 * card is clickable everywhere while keyboard and screen reader users meet one
 * link per card. Styles: css/whitepaper.css (.wpaper-card).
 *
 * @package stratusx-child
 */

$item = isset( $args['item'] ) && is_array( $args['item'] ) ? $args['item'] : array();

if ( empty( $item['id'] ) ) {
	return;
}

$variant = ( isset( $args['variant'] ) && 'featured' === $args['variant'] ) ? 'featured' : '';
$label   = isset( $args['label'] ) ? trim( (string) $args['label'] ) : '';
$heading = ( isset( $args['heading'] ) && in_array( $args['heading'], array( 'h2', 'h3', 'h4' ), true ) ) ? $args['heading'] : 'h3';
$pdf     = $item['pdf'];

// The wide card has room for a fuller summary; CSS still clamps it to 4 lines.
$excerpt = $variant ? hr_whitepaper_excerpt( $item['id'], 45 ) : $item['excerpt'];

$classes = array( 'wpaper-card' );
if ( $variant ) {
	$classes[] = 'wpaper-card--' . $variant;
}

$sizes = $variant
	? '(max-width: 899px) 100vw, 680px'
	: '(max-width: 699px) 100vw, (max-width: 1199px) 50vw, 400px';
?>
<article class="<?= esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="wpaper-card__media">
		<?php if ( $item['image_id'] ) : ?>
			<?= wp_get_attachment_image(
				$item['image_id'],
				'medium_large',
				false,
				array(
					'class'    => 'wpaper-card__img',
					// The cover repeats the title printed right below it.
					'alt'      => '',
					'loading'  => $variant ? 'eager' : 'lazy',
					'decoding' => 'async',
					'sizes'    => $sizes,
				)
			); ?>
		<?php else : ?>
			<span class="wpaper-card__placeholder"><?= hr_whitepaper_icon( 'file-text', 44 ); ?></span>
		<?php endif; ?>

		<?php if ( $pdf ) : ?>
			<span class="wpaper-card__format">
				<?= hr_whitepaper_icon( 'file-text', 14 ); ?>
				PDF<?php if ( $pdf['size_label'] ) : ?> <span aria-hidden="true">&middot;</span> <?= esc_html( $pdf['size_label'] ); ?><?php endif; ?>
			</span>
		<?php endif; ?>
	</div>

	<div class="wpaper-card__body">
		<?php if ( $label ) : ?>
			<span class="wpaper-card__label"><?= esc_html( $label ); ?></span>
		<?php endif; ?>

		<?php if ( $item['topic'] ) : ?>
			<span class="wpaper-card__topic"><?= esc_html( $item['topic'] ); ?></span>
		<?php endif; ?>

		<<?= $heading; ?> class="wpaper-card__title">
			<a class="wpaper-card__link" href="<?= esc_url( $item['url'] ); ?>"><?= wp_kses_post( $item['title'] ); ?></a>
		</<?= $heading; ?>>

		<?php if ( '' !== $excerpt ) : ?>
			<p class="wpaper-card__excerpt"><?= esc_html( $excerpt ); ?></p>
		<?php endif; ?>

		<div class="wpaper-card__footer">
			<span class="wpaper-card__date">
				<?= hr_whitepaper_icon( 'calendar', 15 ); ?>
				<time datetime="<?= esc_attr( $item['date_iso'] ); ?>"><?= esc_html( $item['date_label'] ); ?></time>
			</span>

			<span class="wpaper-card__more" aria-hidden="true"><?= $variant ? 'Read &amp; Download' : 'View Whitepaper'; ?> <?= hr_whitepaper_icon( 'arrow-right', 16 ); ?></span>
		</div>

		<?php
		// Same affordance the old archive gave admins through show_admin_edit_button(),
		// scoped to this card's whitepaper rather than the global post.
		$edit_link = current_user_can( 'edit_post', $item['id'] ) ? get_edit_post_link( $item['id'] ) : '';
		if ( $edit_link ) :
			?>
			<a class="wpaper-card__edit" href="<?= esc_url( $edit_link ); ?>">Edit<span class="visually-hidden"> <?= esc_html( $item['title_text'] ); ?></span></a>
		<?php endif; ?>
	</div>
</article>
