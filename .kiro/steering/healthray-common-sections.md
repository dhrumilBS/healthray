---
inclusion: always
---

# Common section patterns (product-landing family)

Copy these and change only the text. `temp-ehr-software.php` and
`temp-abdm-landing.php` are full working examples.

## Top of the template

```php
<?php
/**
 * Template Name: {X} Landing
 * Template Post Type: page
 *
 * Page:       /{slug}/ (page ID)
 * Body class: landing-page {x}-landing
 * CSS:        css/common-landing.css + css/{x}-software.css (inlined in wp_head)
 * JS:         none | js/{x}-software.js
 * FAQ:        healthray_{x}_faq_items() in functions.php (page + FAQPage schema)
 */
$hr_media = 'https://healthray.com/wp-content/uploads';
$hr_x_faq = function_exists('healthray_{x}_faq_items') ? healthray_{x}_faq_items() : array();
$hr_check = '<svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>';
?>
<main>
```

## Breadcrumbs

```php
<nav class="crumbs" aria-label="Breadcrumb">
	<div class="wrap"><ol>
		<li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
		<li><span aria-current="page">{Page}</span></li>
	</ol></div>
</nav>
```

## Hero with lead form

```php
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
	<div class="wrap hero-grid">
		<div>
			<p class="eyebrow">Trust line · trust line · trust line</p>
			<h1 id="hero-title">Keyword-led heading
				<span class="pulse-word">highlight
					<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none"><path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/></svg>
				</span>
			</h1>
			<p class="hero-sub">...</p>
			<ul class="hero-points">
				<li><?php echo $hr_check; ?> point</li>
			</ul>
			<p class="hero-proof"><strong>2,500+ hospitals</strong> <span class="dot"></span> <strong>5M+ patient records</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
		</div>
		<div class="lead-card" id="demo-form"> <!-- id only on the hero instance -->
			<h2>Book a free {x} demo</h2>
			<p>... Our team replies within one business day.</p>
			<?php echo do_shortcode('[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]'); ?>
		</div>
	</div>
</section>
```

## Compliance badges

`<section class="compliance">` → `.comp-grid` → `.comp-item reveal` with an
img + `<span>`. Badge images (uploads): `2026/03/abdm-healthray-150x150.webp`,
`2026/03/abha-healthray-150x150.webp`, `2025/07/NHA.webp`,
`2026/07/NABH-ehr-certified-150x150.webp` (wrap in a link to
`https://nabh.co/software/healthray/`), `2026/03/Hipaa-healthray-150x150.webp`,
`2026/03/iso-27001-healthray-150x150.webp`, `2026/03/hl7-healthray-150x150.webp`,
`2025/07/FHIR.webp`.

## Ready-made blocks (shortcodes)

```php
<?php echo do_shortcode('[hr_stats]'); ?>
<?php echo do_shortcode('[hr_clients eyebrow="Trusted across India" title="..."]'); ?>
<?php echo do_shortcode('[hr_testimonials eyebrow="..." title="..."]'); ?>
```

Don't paste client lists or stat numbers into a template; change them in
`shortcodes/hr_clients.php` / `hr-new-stats.php` so every page stays the same.

## Other building blocks in common-landing.css

- `.define` section (tinted) + `.define-grid` (copy left, image right)
- `.grid-3` of `.card` (with optional `.icon` SVG); a whole card can be an `<a>`
- `.versus-table` (+ `<caption class="hp">`) and `.versus-cta` paragraph
- `.arch-grid` of `.arch` boxes with an `h3` icon and a tick list
- `.t-grid` / `.t-card` testimonials + `.ratings` row

## FAQ

See `healthray-pages.md` section 5: `healthray_{x}_faq_items()` + the `hr_head_faqs`
filter; the template loops the same array into `<details>` inside
`<section id="faq">` → `.faq-list`.

## Final CTA

```php
<section aria-labelledby="cta-title" style="padding-top:0">
	<div class="wrap"><div class="cta">
		<div class="cta-grid">
			<div class="cta-copy">
				<h2 id="cta-title">...</h2>
				<p>...</p>
				<ul class="cta-points"><li><?php echo $hr_check; ?> Free demo - no credit card required</li></ul>
			</div>
			<div class="lead-card">
				<h2>Book a free {x} demo</h2>
				<p>...</p>
				<?php echo do_shortcode('[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]'); ?>
			</div>
		</div>
		<svg class="ecg-bg" viewBox="0 0 1200 70" preserveAspectRatio="none" aria-hidden="true"><path d="M0 35 H200 l15-18 20 36 15-30 12 12 H480 l14-22 18 40 14-26 10 8 H800 l15-18 20 36 15-30 12 12 H1200"/></svg>
	</div></div>
</section>
</main>
```
