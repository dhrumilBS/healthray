<?php
/**
 * Event card.
 *
 * Shared by archive-events.php (spotlight and listing) and single-events.php
 * (more events). Pass the data from hr_event_data():
 *
 *   get_template_part( 'template-parts/event', 'card', array(
 *       'event'   => hr_event_data( $post ),
 *       'variant' => 'featured',    // Optional. '' = grid card, 'featured' = wide card.
 *       'label'   => 'Latest event', // Optional eyebrow above the title.
 *       'heading' => 'h3',           // Optional. h2, h3 or h4.
 *   ) );
 *
 * The title link is the only link in the card. Its ::after is stretched over
 * the whole card, so the card is clickable everywhere while keyboard and
 * screen reader users meet one link per card instead of image + title +
 * "read more". Styles: css/events.css (.ev-card).
 *
 * @package stratusx-child
 */

$event = isset( $args['event'] ) && is_array( $args['event'] ) ? $args['event'] : array();

if ( empty( $event['id'] ) ) {
	return;
}

$variant = ( isset( $args['variant'] ) && 'featured' === $args['variant'] ) ? 'featured' : '';
$label   = isset( $args['label'] ) ? trim( (string) $args['label'] ) : '';
$heading = ( isset( $args['heading'] ) && in_array( $args['heading'], array( 'h2', 'h3', 'h4' ), true ) ) ? $args['heading'] : 'h3';

$classes = array( 'ev-card', 'ev-card--' . $event['status'] );
if ( $variant ) {
	$classes[] = 'ev-card--' . $variant;
}

$sizes = $variant
	? '(max-width: 899px) 100vw, 640px'
	: '(max-width: 699px) 100vw, (max-width: 1199px) 50vw, 400px';
?>
<article class="<?= esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="ev-card__media">
		<?php if ( $event['image_id'] ) : ?>
			<?= wp_get_attachment_image(
				$event['image_id'],
				'medium_large',
				false,
				array(
					'class'    => 'ev-card__img',
					// The title right below says the same thing, so the image is decorative here.
					'alt'      => '',
					'loading'  => 'lazy',
					'decoding' => 'async',
					'sizes'    => $sizes,
				)
			); ?>
		<?php else : ?>
			<span class="ev-card__placeholder"><?= hr_event_icon( 'calendar-days', 44 ); ?></span>
		<?php endif; ?>
	</div>

	<div class="ev-card__body">
		<div class="ev-card__heading">
			<?php if ( $label ) : ?>
				<span class="ev-card__label"><?= esc_html( $label ); ?></span>
			<?php endif; ?>

			<?= hr_event_badges( $event ); ?>

			<<?= $heading; ?> class="ev-card__title">
				<a class="ev-card__link" href="<?= esc_url( $event['url'] ); ?>"><?= wp_kses_post( $event['title'] ); ?></a>
			</<?= $heading; ?>>
		</div>

		<?php if ( '' !== $event['excerpt'] ) : ?>
			<p class="ev-card__excerpt"><?= esc_html( $event['excerpt'] ); ?></p>
		<?php endif; ?>

		<ul class="ev-card__meta">
			<li>
				<?= hr_event_icon( 'calendar', 16 ); ?>
				<span>
					<span class="visually-hidden">Date: </span>
					<?php if ( $event['start_iso'] ) : ?>
						<time datetime="<?= esc_attr( $event['start_iso'] ); ?>"><?= esc_html( $event['date_label'] ); ?></time>
					<?php else : ?>
						To be announced
					<?php endif; ?>
				</span>
			</li>
			<?php if ( '' !== $event['location'] ) : ?>
				<li>
					<?= hr_event_icon( $event['is_online'] ? 'globe' : 'pin', 16 ); ?>
					<span><span class="visually-hidden">Location: </span><?= esc_html( $event['location'] ); ?></span>
				</li>
			<?php endif; ?>
		</ul>

		<div class="ev-card__footer">
			<span class="ev-card__more" aria-hidden="true">View event details <?= hr_event_icon( 'arrow-right', 16 ); ?></span>

			<?php
			// Same affordance the old archive gave admins through show_admin_edit_button(),
			// scoped to this card's event rather than the global post.
			$edit_link = current_user_can( 'edit_post', $event['id'] ) ? get_edit_post_link( $event['id'] ) : '';
			if ( $edit_link ) :
				?>
				<a class="ev-card__edit" href="<?= esc_url( $edit_link ); ?>">Edit<span class="visually-hidden"> <?= esc_html( $event['title_text'] ); ?></span></a>
			<?php endif; ?>
		</div>
	</div>
</article>
