<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <?php
  // Mulish is the only font the site displays (font-family.css forces it everywhere; PPC pages set it in their own CSS).
  // Cloudflare Fonts serves it from this path. Preloading the Latin file lets headings paint in Mulish sooner.
  // If Cloudflare ever changes the Mulish version in this path, update it (check DevTools > Network > Font).
  ?>
  <link rel="preload" as="font" type="font/woff2" crossorigin href="https://healthray.com/cf-fonts/v/mulish/5.2.8/latin/wght/normal.woff2">

  <script async src="https://www.googletagmanager.com/gtag/js?id=AW-10935864100"></script>
  <script>
    (function(w, d, s, l, i) {
      w[l] = w[l] || [];
      w[l].push({
        'gtm.start': new Date().getTime(),
        event: 'gtm.js'
      });
      var f = d.getElementsByTagName(s)[0],
        j = d.createElement(s),
        dl = l != 'dataLayer' ? '&l=' + l : '';
      j.async = true;
      j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
      f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-M85X2N9V');

    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'AW-10935864100');
    <?php
    // Google Ads lead conversion: only on the thank-you pages the forms redirect to after a successful submission.
    // (It used to fire on every page view, so every ad click counted as a conversion.)
    // Thank-you pages: /thank-you/ 28110, /thank-you-fb/ 60060, /thank-you-linkedin/ 60090, /thank-you-openai/ 81370.
    if (is_page(array(28110, 60060, 60090, 81370))) :
    ?>
      gtag('event', 'conversion', {
        'send_to': 'AW-10935864100/Yc8NCInZxdYDEKSW0N4o'
      });
    <?php endif; ?>
  </script>

  <?php
  // font.css (Inter + Poppins) removed: no page uses either font, and it was a render-blocking stylesheet on every page.
  ?>

  <?php wp_head(); ?>

  <?php
  // Preload the hero image only where it is shown at the top of the page: single blog posts.
  // imagesrcset/imagesizes match the hero <img> (sizes "(max-width: 991px) 100vw, 600px"), so phones preload
  // the same file the image uses. Other pages (landing, PPC, contact) don't show their featured image, which
  // only exists for social sharing - preloading it there was a wasted download.
  if (is_singular('post') && has_post_thumbnail()) {
    $hr_thumb_id = get_post_thumbnail_id();
    $hr_src      = wp_get_attachment_image_src($hr_thumb_id, 'large');
    $hr_srcset   = wp_get_attachment_image_srcset($hr_thumb_id, 'large');
    if ($hr_src) {
      printf(
        '<link rel="preload" as="image" fetchpriority="high" href="%s"%s>' . "\n",
        esc_url($hr_src[0]),
        $hr_srcset ? ' imagesrcset="' . esc_attr($hr_srcset) . '" imagesizes="(max-width: 991px) 100vw, 600px"' : ''
      );
    }
  }
  ?>

  <meta name="google-site-verification" content="DMKDxRL2ftBjaytNBCW73YRefPDi2mokr06dov343G0" />

  <script src="https://analytics.ahrefs.com/analytics.js" data-key="k2duGxZiULE/wJIt0LuX/A" async></script>

</head>