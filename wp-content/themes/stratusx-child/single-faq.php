<section class="sec-padded hero-section blog-hero">
	<div class="container">
		<div class="heading text-center">
			<h1> <?php the_title(); ?> FAQ </h1>
			<?php if (get_the_content()) {
				echo "<p>";
				the_content();
				echo "</p>";
			} ?>
		</div>
	</div>
</section>

<section class="sec-padded faq-section ">
	<div class="container">
		<?php
		$faq_json = get_field('faqs_object');
		$faqs = json_decode($faq_json, true);
		$faq_columns = array_chunk($faqs, ceil(count($faqs) / 2));
		$faq_uid = get_the_ID() ? get_the_ID() : uniqid();
		?>
		<div class="faq-intro text-center mb-5">
			<h2 class="section-title text-primary"> <?= get_field('faq_title'); ?> </h2>
			<p class="faq-description"> <?= get_field('faq_text'); ?> </p>
		</div>

		<div class="accordion-faq-wrapper active faq-wrapper">
			<?php
			foreach ($faq_columns as $col_index => $column) {
				?>
				<div class="elementor-toggle accordion-list" style="flex: 1; min-width: 320px;">
						<?php
						foreach ($column as $item_index => $faq) {
							$question   = esc_html($faq['question']);
							$answer     = wp_kses_post($faq['answer']);
							$item_num   = $item_index + 1; // 1-based, computed before use
							$is_first   = ($item_num === 1);
							$tab_id     = $faq_uid . '-' . $col_index . '-' . $item_num;
							?>
						<div class="elementor-toggle-item<?= $is_first ? ' active' : ''; ?>">
							<div id="elementor-tab-title-<?= $tab_id; ?>" class="elementor-tab-title" data-tab="1" role="button" tabindex="0" aria-controls="elementor-tab-content-<?= $tab_id; ?>" aria-expanded="<?= $is_first ? 'true' : 'false'; ?>">
								<span class="elementor-toggle-icon elementor-toggle-icon-left" aria-hidden="true">
									<span class="elementor-toggle-icon-closed">
										<svg class="e-font-icon-svg e-fas-caret-down" width="14" height="14" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg">
											<path d="M31.3 192h257.3c17.8 0 26.7 21.5 14.1 34.1L174.1 354.8c-7.8 7.8-20.5 7.8-28.3 0L17.2 226.1C4.6 213.5 13.5 192 31.3 192z"></path>
										</svg>
									</span>
									<span class="elementor-toggle-icon-opened">
										<svg class="elementor-toggle-icon-opened e-font-icon-svg e-fas-caret-up" width="14" height="14" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg">
											<path d="M288.662 352H31.338c-17.818 0-26.741-21.543-14.142-34.142l128.662-128.662c7.81-7.81 20.474-7.81 28.284 0l128.662 128.662c12.6 12.599 3.676 34.142-14.142 34.142z"></path>
										</svg>
									</span>
								</span>
								<div class="elementor-toggle-title" tabindex="0"><?= $question; ?></div>
							</div>

							<div id="elementor-tab-content-<?= $tab_id; ?>" class="elementor-tab-content" data-tab="1" role="region" aria-labelledby="elementor-tab-title-<?= $tab_id; ?>" style="<?= $is_first ? '' : 'display:none;'; ?>">
										<?= $answer; ?>
							</div>
						</div>
						<?php } ?>
				</div>
			<?php } ?>
		</div>
	</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var wrapper = document.querySelector('.accordion-faq-wrapper');
	if (!wrapper) return;

	wrapper.addEventListener('click', function (e) {
		var title = e.target.closest('.elementor-tab-title');
		if (!title || !wrapper.contains(title)) return;
		var item = title.closest('.elementor-toggle-item');
		var content = document.getElementById(title.getAttribute('aria-controls'));
		var isOpen = title.getAttribute('aria-expanded') === 'true';
		var list = title.closest('.elementor-toggle');
		list.querySelectorAll('.elementor-tab-title').forEach(function (t) {
			if (t !== title) {
				t.setAttribute('aria-expanded', 'false');
				t.closest('.elementor-toggle-item').classList.remove('active');
				var c = document.getElementById(t.getAttribute('aria-controls'));
				if (c) c.style.display = 'none';
			}
		});

		title.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
		item.classList.toggle('active', !isOpen);
		if (content) content.style.display = isOpen ? 'none' : 'block';
	});

	wrapper.addEventListener('keydown', function (e) {
		if (e.key !== 'Enter' && e.key !== ' ') return;
		var title = e.target.closest('.elementor-tab-title');
		if (!title) return;
		e.preventDefault();
		title.click();
	});
});
</script>

<?php
$current_id = get_the_ID();
$terms = wp_get_post_terms($current_id, 'faq_category', ['fields' => 'ids']);

$related_query = new WP_Query([
	'post_type' => 'faq', // ← your CPT slug
	'posts_per_page' => 4,
	'post_status' => 'publish',
]);
?>


<?php if ($related_query->have_posts()): ?>
	<style>
		.sfaq-related { max-width: 860px; margin: 0 auto; padding: 40px 24px; }
		.bf-related-faq-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; }
		.bf-related-faq-header h2 { font-size: 22px; font-weight: 700; }
		.bf-related-faq-header a { font-size: 14px; color: #4f46e5; text-decoration: none; }
		.sfaq-masonry { display: grid; grid-template-columns: repeat(4, 1fr); column-gap: 16px; }
		.sfaq-masonry .sfaq { break-inside: avoid; background: #fff; border: 1px solid #e2e2ec; border-radius: 14px; margin-bottom: 16px; overflow: hidden; transition: .25s; }
		.sfaq-masonry .sfaq:hover { border-color: #c7d2fe; transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0, 0, 0, .08); }
		.sfaq-masonry .sfaq-head { padding: 18px; display: flex; align-items: center; gap: 10px; }
		.sfaq-masonry .sfaq-num { width: 28px; height: 28px; border-radius: 6px; background: #f2f2f7; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; }
		.sfaq-masonry .sfaq-head .sfaq-q { flex: 1; font-size: 15px; margin: 0; }
		.sfaq-masonry .sfaq-count { font-size: 11px; background: #f2f2f7; border: 1px solid #e2e2ec; padding: 2px 8px; border-radius: 100px; }
		.sfaq-masonry .sfaq-body { padding: 0 18px 18px; font-size: 14px; color: #4a4a65; line-height: 1.6; }
		.sfaq-masonry .sfaq-link { display: inline-block; font-size: 13px; color: #4f46e5; text-decoration: none; }
		@media screen and (max-width: 1200px) {
			.sfaq-masonry { grid-template-columns: repeat(2, 1fr); }		
		}

		@media screen and (max-width: 768px) {
			.sfaq-masonry { grid-template-columns: repeat(1, 1fr); }		
		}
	</style>
	<section class="sec-padded">
		<div class="container">
			<div class="bf-related-faq-header">
				<h2>Related FAQs</h2>
				<a href="/faqs/">View all FAQs</a>
			</div>

			<div class="sfaq-masonry">
					<?php while ($related_query->have_posts()):
						$related_query->the_post(); ?>
					<div class="sfaq">
						<div class="sfaq-head">
							<h3 class="sfaq-q"><?php the_title(); ?></h3>
						</div>
						<div class="sfaq-body"> <?= wp_trim_words(get_the_excerpt(), 20); ?> <a class="sfaq-link" href="<?php the_permalink(); ?>"> View Q&A </a> </div>
					</div>
					<?php endwhile; ?>
			</div>
		</div>
	</section>
		<?php wp_reset_postdata();
endif; ?>