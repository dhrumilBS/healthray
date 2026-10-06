<?php
add_shortcode('blog_cta', 'blog_cta_shortcode');

function blog_cta_shortcode($atts) {
$atts = shortcode_atts(array(
	'badge'       => 'Medical Billing Software',
	'title_highlight' => 'Without the Guesswork',
	'title'       => 'Make the Right Billing Decision, Without the Guesswork',
	'description' => 'See how your medical billing process works in real scenarios before you invest. Explore workflows, reduce claim errors, and achieve faster reimbursements with the right system.',
	'btn_text'    => 'Start Your Free Journey Today',
	'btn_link'    => 'https://calendly.com/healthray/30min',
), $atts);

ob_start();

  // Safely prepare title
$title = esc_html($atts['title']);

 if (!empty($atts['title_highlight'])) {

    $highlight = esc_html($atts['title_highlight']);

    $title = str_replace(
        $highlight,
        '<span class="hr-cta-highlight">' . $highlight . '</span>',
        $title
    );
}


$trust_items = array(
    array(
        'label' => 'Trusted by 1,000+ clinics',
        'icon'  => '<path d="M7 1l1.5 3.2L12 4.8l-2.5 2.4.6 3.4L7 9l-3.1 1.6.6-3.4L2 4.8l3.5-.6L7 1z" fill="#f0c040"/>',
    ),
    array(
        'label' => 'No credit card required',
        'icon'  => '<path d="M2 7l3.5 3.5L12 3" stroke="#1d9e75" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
    ),
    array(
        'label' => 'HIPAA compliant',
        'icon'  => '<rect x="2" y="4" width="10" height="7" rx="1.5" stroke="#1e6fff" stroke-width="1.2"/><path d="M5 4V3a2 2 0 014 0v1" stroke="#1e6fff" stroke-width="1.2"/>',
    ),
);

?>


<section class="hr-blog-cta">
 <span class="hr-cta-badge" aria-hidden="true">
            <span class="hr-cta-badge-dot"></span>
            <?php echo esc_html($atts['badge']); ?>
        </span>

        <h2 class="hr-cta-title">
            <?php echo wp_kses_post($title); ?>
        </h2>

        <p class="hr-cta-desc">
            <?php echo esc_html($atts['description']); ?>
        </p>


 
<div class="hr-cta-trust" aria-label="Trust indicators">
<?php foreach ($trust_items as $item) : ?>
    <span class="hr-trust-item">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
            <?php echo $item['icon']; ?>
        </svg>
        <?php echo $item['label']; ?>
    </span>
<?php endforeach; ?>
</div>

<a href="<?php echo esc_url($atts['btn_link']); ?>"
   class="hr-cta-btn"
   target="_blank"
   rel="noopener noreferrer"
   aria-label="Start your free journey with Healthray medical billing software">
	<?php echo esc_html($atts['btn_text']); ?>
	<svg width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
		<path d="M2.5 7.5h10M9 3.5l4 4-4 4" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
	</svg>
</a>

<p class="hr-cta-subtext">Free demo available - no commitment needed</p>

</section>

<style>
.hr-blog-cta { background: #f0f5ff; border: 1px solid #d0e0ff; border-radius: 14px; padding: 2.25rem 2rem; display: flex; flex-direction: column; align-items: flex-start; gap: 1rem; position: relative; overflow: hidden; margin: 2.5rem 0 1rem; }
.hr-blog-cta::before { content: ''; position: absolute; right: -60px; top: -60px;	width: 220px; height: 220px; border-radius: 50%; border: 1.5px solid rgba(30,111,255,0.12); pointer-events: none; }
.hr-blog-cta::after { content: ''; position: absolute; right: -25px; top: -25px; width: 140px; height: 140px; border-radius: 50%; border: 1.5px solid rgba(30,111,255,0.08); pointer-events: none; }
.hr-cta-badge { display: inline-flex; align-items: center; gap: 6px; background: #dceaff; color: #1e50b3; font-size: 11px; font-weight: 600; padding: 3px 11px; border-radius: 20px; letter-spacing: 0.4px; }
.hr-cta-badge-dot { width: 6px; height: 6px; border-radius: 50%; background: #1e6fff; flex-shrink: 0; }
.hr-cta-title { color: #0b2545; font-size: 21px; font-weight: 700; margin: 0; line-height: 1.35; max-width: 650px; }
.hr-cta-highlight { color: #1e6fff; }
.hr-cta-desc { color: #3a5070; font-size: 15px; line-height: 1.72; margin: 0; max-width: 650px; }
.hr-cta-trust { display: flex; flex-wrap: wrap; align-items: center; gap: 14px; margin: 0; }
.hr-trust-item { display: flex; align-items: center; gap: 5px; color: #4a6080; font-size: 12px; font-weight: 500; }
.post-content a.hr-cta-btn { display: inline-flex; align-items: center; gap: 8px; background: #1e6fff; color: #ffffff; font-size: 15px; font-weight: 700; padding: 13px 26px; border-radius: 8px; text-decoration: none; position: relative; z-index: 1; transition: background 0.15s ease, transform 0.12s ease; }
.post-content a.hr-cta-btn:hover { background: #1558e0; color: #fff; transform: translateY(-1px); }
.post-content a.hr-cta-btn:focus-visible { outline: 3px solid #93b8ff; outline-offset: 3px; }
.hr-cta-subtext { font-size: 11px; color: #7a95b0; margin: -4px 0 0; }

@media (max-width: 600px) {
	.hr-blog-cta { padding: 1.75rem 1.25rem; }
	.hr-cta-title { font-size: 18px; }
	.hr-cta-btn { width: 100%; justify-content: center; }
}
</style>

<?php
return ob_get_clean();
}