<style>
	.ml-slider-set-container .custom-pagination { display: flex;justify-content: center;margin-top: 20px;position: absolute;bottom: 0;right: 0;z-index: 888;}
	.ml-slider-set-container .custom-pagination::before {width: 90%;content: '';position: absolute;left: 20px;top: 20px;height: 1px;display: block;border-top: 1px dashed #1e90ff;z-index: -1;}

	.ml-slider-set-container .custom-pagination .dot {width: 40px;height: 40px;border-radius: 50%;display: flex;align-items: center;justify-content: center;margin-right: 42px;color: #333;cursor: pointer;transition: all 0.3s;position: relative;background-color: #fff;filter: drop-shadow(0px 6px 4px #00000010);}
	.ml-slider-set-container .custom-pagination .dot:last-child {margin-right: 0;}
	.ml-slider-set-container .custom-pagination .dot.active {background-color: #1e90ff;color: white;border-color: #1e90ff;}

	.product-slider-style-1:not(.owl-loaded) { display: flex; flex-wrap: nowrap; overflow: hidden;}
	.product-slider-style-1:not(.owl-loaded)>* { width: 100%; flex-shrink: 0; }
	.product-slider-style-1 .slide .row {display: flex;position: relative;}
	.product-slider-style-1 .slide .column {position: relative;max-width: 50%;}
	.product-slider-style-1 .slide .right-column {position: relative;}
	.product-slider-style-1 .slide .right-column .big-number {color: #D1DEFF;font-size: 118px;font-weight: 700;z-index: -1;position: absolute;transition: all 300ms ease-in-out;}
	.product-slider-style-1 .slide .right-column .slider-content {padding-left: 72px;padding-bottom: 128px;position: relative;transition: all 500ms ease-in-out;}
	.owl-loaded.product-slider-style-1 .slide .right-column .slider-content{ opacity: 0; left: 150px;}
	.owl-loaded.product-slider-style-1 .active .right-column .slider-content {opacity: 1;left: 0;}
	.product-slider-style-1 .slide .right-column .slider-content .slide-number {font-size: 45px;font-weight: 700;color: var(--hr-secondary-color);}

	@media screen and (max-width: 991px) {
		.product-slider-style-1 .slide .right-column .slider-content { padding-left: 0;padding-right: 36px;padding-bottom: 36px;}
		.ml-slider-set-container .custom-pagination { position: relative; margin: 0 auto; }
	}
	
	@media screen and (max-width: 600px) {
		.product-slider-style-1 .slide .row {display: flex;flex-direction: column;}
		.product-slider-style-1 .slide .column{ max-width: 100%;}
		.product-slider-style-1 .slide .column .slide-img {max-width: 85%;margin: 0 auto; }
		.product-slider-style-1 .slide .right-column .big-number {display: none;}
		.product-slider-style-1 .slide .right-column .slider-content {padding: 0;}
	}
</style>

<div class="ml-slider-set-container">
	<div class="owl-carousel product-slider-style-1" <?= $this->get_render_attribute_string('slider'); ?>>
		<?php foreach ($settings['reapeter'] as $key => $value) {
			$key++;
		?>
			<div class="slide">
				<div class="row">
					<div class="column left-column">
						<?php if (!empty($value['image']['id'])) { ?>
							<div class="slide-img">
								<?= wp_get_attachment_image($value['image']['id'], ['auto', 'auto'], "", []); ?>
							</div>
						<?php } ?>
					</div>

					<div class="column right-column">
						<span class="big-number">0<?= $key; ?></span>
						<div class="slider-content">
							<span class="slide-number">0<?= $key; ?></span>
							<?php if (!empty($value['title_text'])) { ?> <h3 class="title-text"><?= esc_html($value['title_text']); ?></h3> <?php } ?>
							<?php if (!empty($value['description_text'])) { ?> <p class='text-style'><?= $value['description_text']; ?></p> <?php } ?>
							<?php if (!empty($value['description_content'])) { ?> <div class='content_style'><?= $value['description_content']; ?></div> <?php } ?>
						</div>

					</div>
				</div>
			</div>
		<?php } ?>
	</div>


	<div class="custom-pagination">
		<span class="dot" data-slide="0">1</span>
		<span class="dot" data-slide="1">2</span>
		<span class="dot" data-slide="2">3</span>
		<span class="dot" data-slide="3">4</span>
		<span class="dot" data-slide="4">5</span>
	</div>
</div>