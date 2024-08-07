<?php
/*
Template Name: EMR State City
*/
// the_content();

global $post;
$city_name = get_field('best_software_statecity_name');
$state_img = get_field('best_software_statecity_image');

if (has_post_thumbnail($post->ID) && get_post_thumbnail_id() != $state_img  ) {
	set_post_thumbnail($post->ID, $state_img);
}  else { 
	has_post_thumbnail($post->ID);
}
?>

<div class="best-in best-in-state best-emr-in-state">

	<!-- Hero -->
	<div class="section section-best-in">
		<div class="container">
			<div class="th-d-flex th-align-items-center">
				<div class="half-width">
					<div class="heading">
						<div class="headline-color">
							<h1 class="elementor-heading-title elementor-size-default">Best <span>EMR</span> Software in <span><?= $city_name; ?></span></h1>
						</div>
						<div class="description">
							<p> Want to explore digital medical solutions? Then, we are here with the best EMR software in <?= $city_name; ?> that completely remolds healthcare workflow and heightened efficiency. Schedule a demo now!</p>
						</div>
					</div>

					<div class="form-wrapper">
						<?= do_shortcode(' [contact-form-7 id="5803552" title="Hero Section CTA"]'); ?>
					</div>
				</div>
				<div class="half-width">
					<div class="image">
						<img width="500" height="397" src="https://healthray.com/wp-content/uploads/2024/07/Best-EMR-Software-in-india-Healthray.webp" class="attachment-full size-full wp-image-39234" alt="Best Emr Software In India Healthray">
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Healthcare System -->
	<div class="section section-healthcare">
		<div class="container">
			<div class="th-d-flex th-align-items-center">
				<div class="half-width">
					<div class="heading sec-heading">
						<div class="headline-color">
							<h2 class="elementor-heading-title elementor-size-default"><?= get_field('free_title'); ?> </h2>
						</div>
						<p>Digitize your workflow procedure by installing the best EMR software in <?= $city_name; ?>. Moreover, it is a portable software, which means to be adapted in a flexible environment and overcome various clinical challenges. Therefore, it aids to modernize the healthcare service. </p>
					</div>
				</div>
				<div class="half-width">  
					<div class="image"> <?= wp_get_attachment_image(get_field('free_image'), 'full'); ?> </div>
				</div>
			</div>
		</div>
	</div>

	<!-- EMR functionalities -->
	<div class="section section-functionaly">
		<div class="container">
			<div class="heading sec-heading centered">
				<div class="headline-color">
					<h2 class="elementor-heading-title elementor-size-default"> <?= get_field('leading_functions_title'); ?> </h2>
				</div>
			</div>

			<?php if (have_rows('leading_functions')) { ?>
			<div class="icon-box-container">
				<?php while (have_rows('leading_functions')) {
	the_row(); ?>
				<div class="icon-box-wrapper left-align">
					<div class="icon-box-icon">
						<span class="icon"> <?= wp_get_attachment_image(get_sub_field('icon'), 'full'); ?></span>
					</div>
					<div class="icon-box-content">
						<h3 class="icon-box-title"> <?= get_sub_field('title'); ?></h3>
						<p class="icon-box-description"><?= get_sub_field('text'); ?></p>
					</div>
				</div>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
	</div>

	<!-- EMR Software -->
	<div class="section section-benefits">
		<div class="container">
			<div class="heading sec-heading centered">
				<div class="headline-color">
					<h2 class="elementor-heading-title elementor-size-default"> <?= get_field('advantages_title'); ?> </h2>
				</div>
			</div>

			<?php if (have_rows('advantages')) { ?>
			<div class="icon-box-container">
				<?php while (have_rows('advantages')) {
	the_row(); ?>
				<div class="icon-box-wrapper">
					<div class="icon-box-icon">
						<span class="icon"> <?= wp_get_attachment_image(get_sub_field('icon'), 'full'); ?></span>
					</div>
					<div class="icon-box-content">
						<h3 class="icon-box-title"> <?= get_sub_field('title'); ?></h3>
						<p class="icon-box-description"><?= get_sub_field('text'); ?></p>
					</div>
				</div>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
	</div>

	<!-- Our Client Precence -->
	<div class="section section-client-precence section-template">
		<div class="container-full">
			<?= do_shortcode('[elementor-template id="40574"]'); ?>
		</div>
	</div>

	<!-- Healthray’s Products -->
	<div class="section section-procuct">
		<div class="container">
			<div class="heading sec-heading centered">
				<div class="headline-color">
					<h2 class="elementor-heading-title elementor-size-default"> Healthray’s <span>Products</span></h2>
				</div>
			</div>


			<?php if (have_rows('healthray_products')) { ?>
			<div class="swiper healthray-products-slider">
				<div class="swiper-wrapper">
					<?php while (have_rows('healthray_products')) {
	the_row(); ?>

					<div class="swiper-slide">
						<div class="product-slider">
							<div class="half-width">
								<div class="product-content">
									<h2> <?= get_sub_field('title'); ?></h2>
									<p><?= get_sub_field('text'); ?></p>
									<?= get_sub_field('extra_text'); ?>
								</div>
							</div>
							<div class="half-width">
								<div class="product-image image">
									<?= wp_get_attachment_image( get_sub_field('image'), 'full'); ?>
								</div>
							</div>
						</div>
					</div>

					<?php } ?>
				</div>

				<div class="flex">
					<div class="swiper-pagination"></div> 
				</div>
			</div>
			<?php } ?>
		</div>
	</div>

	<!-- All India -->
	<div class="section section-all-india">
		<div class="container">

			<div class="heading sec-heading centered">
				<div class="headline-color">
					<h2 class="elementor-heading-title elementor-size-default">Best EMR Management Software In <span><?= $city_name; ?></span></h2>
				</div>
			</div>
			<div class="image">
				<?= wp_get_attachment_image($state_img, 'full') ?>
			</div>
			<style>
				.list-widget { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 20px; }
				.list-widget .list-widget-text { display: flex; }
				.list-widget .list-widget-text a {width: 100%; background-color: #FFFFFF; border: 1px solid var(--e-global-color-secondary); border-radius: 8px; padding: 8px 12px; }
			</style>
			<div class="list-widget width-auto">
				<div class="list-widget-text">
					<a href="https://healthray.com/best-emr-software-india/" target="_blank"> Best EMR Software In India </a>
				</div> 
				<?php $state_name = get_field('state_name'); $state_link = get_field('state_name_link'); ?>
				<div class="list-widget-text">
					<a href="<?= get_permalink($state_link->ID); ?>" target="_blank"> Best EMR Software In <?= $state_name; ?> </a>
				</div> 

			</div>
		</div>
	</div>

	<style>
		.section-doctor{ background-image: url(<?= get_field('how_to_bg'); ?>);}
	</style>
	<div class="section section-doctor">
		<div class="container">
			<div class="heading sec-heading centered">
				<div class="headline-color">
					<h2 class="elementor-heading-title elementor-size-default"> <?= get_field('how_to_title'); ?> </h2>
				</div>
			</div>

			<div class="th-d-flex">
				<div class="left-content content-box half-width">
					<h3>Unified patient records with the EMR software</h3>
					<p>Each and every patient information record in the electronic platform. Furthermore, it stores patient demographic details along with their healthcare records. Consequently, it aids in combining data and getting immediate medical information. Following are the advantages of the unified patient records : </p>
					<ul><li>Enhancing coordination with internal medical segments and makes it easier to share patient documents to other ones.</li><li>EMR software facilitates insightful patient information that aids to make prompt health actions.</li><li>Improve patient care through remote appointments system, accessible medical data, and interlinked with medical care segments.</li><li>Helps to advance patient outcomes through proper analytics of medical records and advanced diagnostic tools.</li><li>Ease to forecast any critical disease. Therefore, it saves patients money and aids them to be aware of healthcare conditions.</li><li>EMR software eliminates the challenge of pulling information from various sources. Therefore, simplifying consolidated information.</li><li>Patients can store healthcare documents from any region.</li></ul>
				</div>
				<div class="width-auto image">
					<img decoding="async" width="436" height="560" src="https://healthray.com/wp-content/uploads/2024/07/Doctor-Healthray.webp" class="attachment-large size-large wp-image-39208" alt="Doctor Healthray">
				</div>

				<div class="right-content content-box half-width">
					<h3>Modernize diagnostic practice with EMR software</h3>
					<p>If you want to advance your diagnostic practice and make quicker decisions. Therefore, it is crucial for you to acquire the best EMR software that is embedded with AI functionalities. This software is affordable in costs and can be purchased by anyone: </p>
					<ul><li>The electronic medical record software has included advanced tools that support speedy and accurate patient diagnosis.</li><li>Facilitating visual reports for ease to analyze patient conditions and can understand medical history in a concise manner.</li><li>Eliminating paper records is the foremost advantage of EMR software that aids to reduce medical mistakes.</li><li>Removing diagnostic hurdles through exact patient records in the best format.Ultimately, it enhances patient care.</li><li>Radiology images in the digital format helps to enhance image quality and alleviates workload</li></ul>
				</div>
			</div>
		</div>
	</div>



	<!-- Technical Challenges -->
	<div class="section section-tech-challange">
		<div class="container">
			<div class="th-d-flex th-align-items-center">
				<div class="half-width">
					<?php 
					$html= '';
					if (have_rows('technical_challenges')) { ?>
					<div class="icon-box-container technical-challenges-icon">
						<?php while (have_rows('technical_challenges')) { 
						the_row(); 
						$i = get_row_index(); 
						$html .= '<div class="icon-box-wrapper">
							<div class="icon-box-icon">
								<span class="icon">'.wp_get_attachment_image(get_sub_field("icon"), "full").'</span>
							</div>
							<div class="icon-box-content">
								<h3 class="icon-box-title"> '. get_sub_field('title') .'</h3>
								<p class="icon-box-description">'. get_sub_field('text') .'</p>
							</div>
						</div>';

						if($i % 2 == 0){  ?>
						<div class="icon-box-wrapper-1">
							<?php
							echo $html;
							$html = '';
							?>
						</div>
						<?php } ?>
						<?php } ?>
					</div>
					<?php } ?>
				</div>
				<div class="half-width">
					<div class="heading sec-heading">
						<div class="headline-color">
							<h2 class="elementor-heading-title elementor-size-default"> Advancing clinical experience with the <span>EMR software</span> </h2>
						</div>
						<div class="texh-content"> 
							<p> The electronic medical record system software easily captures healthcare records with low efforts and less time period. Moreover, deploying this software helps employees to add on advanced technical skills and increases their productivity in the same hours. </p>
							<p> This platform automated the whole front-desk tasks from equipment management to invoice management process. Moreover, it includes tailor-made forms that aids to create any required template in few minutes.</p>
							<p>After implementing electronic medical record software,many hospitals noticed that patient retention rate has increased without any extra efforts, improved staff retention rate without any additional expenses, and physicians can practice independently without requiring any experienced one.</p>
							<p>Remodeling healthcare services with an automated software that contributes in advance patient care. Furthermore, it makes disorganized data into a structured and easy format that can be interpreted by anyone.</p>
							<p>Are you someone who is searching for reliable software? If yes, Healthray is the best EMR software in <?= $city_name; ?>.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>



	<?php
	$arrow = '<span class="elementor-toggle-icon elementor-toggle-icon-left" aria-hidden="true"> <span class="elementor-toggle-icon-closed"><svg class="e-font-icon-svg e-fas-caret-right" viewBox="0 0 192 512" xmlns="http://www.w3.org/2000/svg"><path d="M0 384.662V127.338c0-17.818 21.543-26.741 34.142-14.142l128.662 128.662c7.81 7.81 7.81 20.474 0 28.284L34.142 398.804C21.543 411.404 0 402.48 0 384.662z"></path></svg></span>	<span class="elementor-toggle-icon-opened"><svg class="elementor-toggle-icon-opened e-font-icon-svg e-fas-caret-up" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg"><path d="M288.662 352H31.338c-17.818 0-26.741-21.543-14.142-34.142l128.662-128.662c7.81-7.81 20.474-7.81 28.284 0l128.662 128.662c12.6 12.599 3.676 34.142-14.142 34.142z"></path></svg></span></span>';
	?>
	
	
	<div class="section section-faq">
		<div class="container">
			
			<div class="heading sec-heading centered">
				<div class="pre-title">
					<p class="elementor-heading-title elementor-size-default">Get Answer</p>
				</div>
				<div class="headline-color">
					<h2 class="elementor-heading-title elementor-size-default">Frequently Asked Questions</h2>
				</div>
			</div>

			<div class="faq-content">
				<?php if (have_rows('blog_faqs')) {
					$json = [
						'@context' => 'https://schema.org',
						'@type' => 'FAQPage',
						'mainEntity' => [],
					];
				?>
					<div class="elementor-toggle accordion-list">
						<?php while (have_rows('blog_faqs')) {
							the_row();
							$json['mainEntity'][] = [
								'@type' => 'Question',
								'name' => wp_strip_all_tags(get_sub_field('question')),
								'acceptedAnswer' => [
									'@type' => 'Answer',
									'text' =>  esc_html(get_sub_field('answer')),
								],
							];
						?>
							<div class="elementor-toggle-item">
								<div id="elementor-tab-title-<?= get_row_index(); ?>" class="elementor-tab-title" data-tab="<?= get_row_index(); ?>" role="button" aria-controls="elementor-tab-content-<?= get_row_index(); ?>" aria-expanded="false">
									<span class="elementor-toggle-icon elementor-toggle-icon-left" aria-hidden="true">
										<span class="elementor-toggle-icon-closed">
											<svg class="e-font-icon-svg e-fas-caret-right" viewBox="0 0 192 512" xmlns="http://www.w3.org/2000/svg">
												<path d="M0 384.662V127.338c0-17.818 21.543-26.741 34.142-14.142l128.662 128.662c7.81 7.81 7.81 20.474 0 28.284L34.142 398.804C21.543 411.404 0 402.48 0 384.662z"></path>
											</svg>
										</span>
										<span class="elementor-toggle-icon-opened">
											<svg class="elementor-toggle-icon-opened e-font-icon-svg e-fas-caret-up" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg">
												<path d="M288.662 352H31.338c-17.818 0-26.741-21.543-14.142-34.142l128.662-128.662c7.81-7.81 20.474-7.81 28.284 0l128.662 128.662c12.6 12.599 3.676 34.142-14.142 34.142z"></path>
											</svg>
										</span>
									</span>
									<a class="elementor-toggle-title" tabindex="0"><?= get_sub_field('question'); ?></a>
								</div>

								<div id="elementor-tab-content-<?= get_row_index(); ?>" class="elementor-tab-content elementor-clearfix answer" data-tab="<?= get_row_index(); ?>" role="region" aria-labelledby="elementor-tab-title-<?= get_row_index(); ?>">
									<?= get_sub_field('answer'); ?>
								</div>
							</div>
					<?php }
					} ?>
					</div>
					<script type="application/ld+json">
						<?= wp_json_encode($json); ?>
					</script>
			</div>
		</div>
	</div>


	<div class="section section-footer-cta section-template">
		<div class="container-full">
			<?= do_shortcode('[elementor-template id="26869"]'); ?>
		</div>
	</div>
</div>