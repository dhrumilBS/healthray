<style>
</style>
<div <?= post_class(); ?>>
	<div class="container">
		<div class="entry-content-wrapper py-4">
			<div class="row">
				<div class="flex_column col-12 col-md-6 mb-4">		 
					<div class="spds-hero-left-content" style="font-size:18px; color:#ffbd2c;">
						<h1><?= get_the_title(); ?></h1>
					</div>

					<div class="hero-left-content text-content">
						<?php if(!empty(get_field('experience'))) { ?><p><strong>Experience</strong>: <?= get_field('experience'); ?> </p> <?php } ?>
						<?php if(!empty(get_field('position'))) { ?><p><strong>Position</strong>: <?= get_field('position'); ?></p><?php } ?>
						<?php if(!empty(get_field('education'))) { ?><p><strong>Education</strong>: <?= get_field('education'); ?></p><?php } ?>
						<?php if(!empty(get_field('location'))) { ?><p><strong>Location</strong>: <?= get_field('location'); ?> </p><?php } ?>
						<hr>
						<div class="job-content">
							<?= get_the_content(); ?>						
						</div>
					</div> 
				</div>
				<div class="flex_column col-12 col-md-6 mb-4">
					<div class="form-heading mb-3">
						<h3 style="text-align: center;">Apply for this position</h3>
						<p class="sub-heading" style="text-align: center;">Join with Healthray family</p>
					</div>
					<div class="form-wrapper">
						<?= do_shortcode('[contact-form-7 id="0ca04cf" title="Career Form - Careers"]'); ?>
					</div> 
				</div>
			</div>
		</div>
	</div>
</div>