<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script async src="https://www.googletagmanager.com/gtag/js?id=AW-10935864100"></script>
  <script async>
  (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-M85X2N9V');
  
    window.dataLayer = window.dataLayer || []; function gtag() { dataLayer.push(arguments); }gtag('js', new Date());gtag('config', 'AW-10935864100');gtag('event', 'conversion', {'send_to': 'AW-10935864100/Yc8NCInZxdYDEKSW0N4o'});
  </script>
  <link rel="stylesheet" href="https://healthray.com/wp-content/fonts/font.css"> 
   <?php wp_head(); ?>
   <?php
  if (has_post_thumbnail()) {
    $thumb_id = get_post_thumbnail_id(get_the_ID());
    $featured_img_src = wp_get_attachment_image_src($thumb_id, 'large')[0];
    $featured_img_srcset = wp_get_attachment_image_srcset($thumb_id);
    printf('<link rel="preload" as="image" importance="high" href="%s" srcset="%s" />', $featured_img_src, $featured_img_srcset);
  }
  ?>
  <meta name="google-site-verification" content="DMKDxRL2ftBjaytNBCW73YRefPDi2mokr06dov343G0" />
  <script src="https://analytics.ahrefs.com/analytics.js" data-key="k2duGxZiULE/wJIt0LuX/A" async></script>
</head>