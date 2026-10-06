<?php
$testimonials = [];
while ( have_rows( 'testimonial', 'option' ) ) : the_row();
	$testimonials[] = [
		'name'        => get_sub_field( 'name' ),
		'quote'       => get_sub_field( 'quote' ),
		'image'       => get_sub_field( 'image' ),
		'hospital'    => get_sub_field( 'hospital' ),
		'position'    => get_sub_field( 'position' ),
		'rating'      => get_sub_field( 'rating' ), // 1–5
		'video'       => get_sub_field( 'video' ),       // NEW: YouTube/Vimeo/mp4 URL, optional
		'video_thumb' => get_sub_field( 'video_thumb' ), // NEW: image field, optional
	];
endwhile;

$ratingSum   = 0;
$ratingCount = 0;
foreach ( $testimonials as $t ) {
	if ( ! empty( $t['rating'] ) ) {
		$ratingSum += floatval( $t['rating'] );
		$ratingCount++;
	}
}
$avgRating = $ratingCount ? round( $ratingSum / $ratingCount, 1 ) : 0;
if ( ! function_exists( 'healthray_video_embed_meta' ) ) {
	function healthray_video_embed_meta( $url ) {
		$url = trim( (string) $url );
		if ( empty( $url ) ) {
			return null;
		}

		$is_youtube = ( strpos( $url, 'youtube.com' ) !== false || strpos( $url, 'youtu.be' ) !== false );

		if ( $is_youtube ) {
			$video_id = '';

			if ( preg_match( '#youtu\.be/([A-Za-z0-9_\-]{6,})#', $url, $m ) ) {
				$video_id = $m[1];
			} elseif ( preg_match( '#[?&]v=([A-Za-z0-9_\-]{6,})#', $url, $m ) ) {
				$video_id = $m[1];
			} elseif ( preg_match( '#youtube\.com/(?:embed|shorts|live)/([A-Za-z0-9_\-]{6,})#', $url, $m ) ) {
				$video_id = $m[1];
			}

			if ( $video_id ) {
				$yt_params = 'autoplay=1&playsinline=1&rel=0&controls=0&modestbranding=1&iv_load_policy=3&fs=0&disablekb=1&enablejsapi=1&cc_load_policy=0';
				return [
					'type'  => 'youtube',
					'src'   => 'https://www.youtube.com/embed/' . $video_id . '?' . $yt_params,
					'thumb' => 'https://i.ytimg.com/vi/' . $video_id . '/hqdefault.jpg',
				];
			}

			return [ 'type' => 'youtube', 'src' => esc_url_raw( $url ), 'thumb' => '' ];
		}

		if ( preg_match( '#vimeo\.com/(\d+)#', $url, $m ) ) {
			$vm_params = 'autoplay=1&playsinline=1&controls=0&title=0&byline=0&portrait=0';
			return [ 'type' => 'vimeo', 'src' => 'https://player.vimeo.com/video/' . $m[1] . '?' . $vm_params, 'thumb' => '' ];
		}
		return [ 'type' => 'file', 'src' => esc_url_raw( $url ), 'thumb' => '' ];
	}
}

$quoteSVG = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" height="20" width="20"><g fill="currentColor"><path d="m23.859026 16.8767757c-11.798316 0-21.359026 9.5607052-21.359026 21.3590298 0 11.1880608 8.5436096 20.1385117 19.3248348 21.1556053-2.2376099 4.0683899-6.1025782 8.3401871-13.0188351 12.408577-1.8307738 1.0170975-3.0512896 3.0512848-3.0512896 5.2888947 0 4.2718048 4.4752254 7.3230972 8.3401899 5.4923248 11.5949039-5.2889023 30.919733-17.9008942 30.919733-44.3454018.0000002-12.0017414-9.3572844-21.3590298-21.155607-21.3590298z"/><path d="m76.3412094 16.8767757c-11.798317 0-21.3590202 9.5607052-21.3590202 21.3590298 0 11.1880608 8.5436096 20.1385117 19.3248329 21.1556053-2.2376099 4.0683899-6.1025772 8.3401871-13.0188408 12.408577-1.8307762 1.0170975-3.0512848 3.0512848-3.0512848 5.2888947 0 4.2718048 4.4752197 7.3230972 8.3401909 5.4923248 11.5948944-5.2889023 30.9197235-17.9008942 30.9197235-44.3454018.2034302-12.0017414-9.3572845-21.3590298-21.1556015-21.3590298z"/></g></svg>';

$starSVG = '<svg class="star-icon" fill="currentColor" width="16" height="16" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';

if ( ! function_exists( 'healthray_resolve_image_src' ) ) {
	function healthray_resolve_image_src( $value, $size = 'large' ) {
		if ( empty( $value ) ) {
			return '';
		}

		if ( is_array( $value ) ) {
			if ( ! empty( $value['sizes'][ $size ] ) ) {
				return $value['sizes'][ $size ];
			}
			if ( ! empty( $value['url'] ) ) {
				return $value['url'];
			}
			if ( ! empty( $value['ID'] ) ) {
				$src = wp_get_attachment_image_src( (int) $value['ID'], $size );
				return $src ? $src[0] : '';
			}
			return '';
		}

		if ( is_numeric( $value ) ) {
			$src = wp_get_attachment_image_src( (int) $value, $size );
			return $src ? $src[0] : '';
		}

		if ( is_string( $value ) ) {
			return esc_url_raw( $value );
		}

		return '';
	}
}

if ( ! function_exists( 'healthray_render_stars' ) ) {
	function healthray_render_stars( $rating, $starSVG, $maxStars = 5 ) {
		$rating = floatval( $rating );
		$out    = '';
		for ( $i = 1; $i <= $maxStars; $i++ ) {
			if ( $rating >= $i ) {
				$out .= '<span class="star full">' . $starSVG . '</span>';
			} elseif ( $rating > $i - 1 && $rating < $i ) {
				$percent = ( $rating - ( $i - 1 ) ) * 100;
				$out .= '<span class="star partial"><span class="star-fill" style="width:' . $percent . '%">' . $starSVG . '</span><span class="star-empty">' . $starSVG . '</span></span>';
			} else {
				$out .= '<span class="star empty">' . $starSVG . '</span>';
			}
		}
		return $out;
	}
}
?>

<style>
:root {
	--hr-brand: #132d7c;
	--hr-brand-light: #3daded;
	--hr-ink: #132d7c;
	--hr-muted: #6b7280;
	--hr-border: #e5e7eb;
	--hr-bg-soft: #f7f8fb;
}

.testimonials-wall .heading .eyebrow,
.wall-of-love .heading .eyebrow { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; margin-bottom: 10px; }
.testimonials-wall { position: relative; overflow: hidden; background: linear-gradient(135deg, #f7f8fb 0%, #eef3fb 100%); }
.testimonials-wall .hero-content { text-align: left; }
/* FIX: reset default browser heading margins now that eyebrow/title use h1/h2 tags */
.testimonials-wall .heading h1,
.testimonials-wall .heading h2 { margin: 0; }
.testimonials-wall .heading .eyebrow { color: var(--hr-brand-light); margin-bottom: 14px; }
.testimonials-wall .heading h2 { font-size: clamp(30px, 4vw, 42px); color: var(--hr-ink); }
.testimonials-wall .heading p { color: var(--hr-muted); font-size: 17px; max-width: 560px; margin: 0 0 20px; }

.testimonials-wall .btn-primary:hover { box-shadow: 0 10px 24px rgba(19,45,124,.25); background: var(--hr-brand); color: #fff; }
.testimonials-wall .btn-primary.hr-cta-btn { border: none; cursor: pointer; font: inherit; }

.testimonials-wall .hero-visual { position: relative; }
.testimonials-wall .hero-visual::before { content: ""; position: absolute; inset: -30px -30px auto auto; width: 240px; height: 240px; background: radial-gradient(circle, rgba(61,173,237,.18) 0%, rgba(61,173,237,0) 70%); z-index: 0; border-radius: 50%; }
.testimonials-wall .hero-visual::after { content: ""; position: absolute; left: -20px; bottom: -20px; width: 180px; height: 180px; background: radial-gradient(circle, rgba(19,45,124,.12) 0%, rgba(19,45,124,0) 70%); z-index: 0; border-radius: 50%; }
.testimonials-wall .hero-image-wrap { position: relative; z-index: 1; }
.testimonials-wall .hero-image-wrap img { width: 100%; height: auto; display: block; }

.wall-of-love .heading .eyebrow { color: #e2585f; }
.wall-of-love .heading .heart { color: #e2585f; }
.wall-of-love .heading h2 { color: var(--hr-ink); }

.stars { display: inline-flex; align-items: center; gap: 2px; line-height: 1; vertical-align: middle; }
.star { position: relative; display: inline-block; width: 16px; height: 16px; line-height: 0; }
.star-icon { width: 16px; height: 16px; display: block; }
.star.full .star-icon { color: #FFB800; }
.star.empty .star-icon { color: #f3f4f6; }
.star.partial .star-empty { color: #f3f4f6; display: block; }
.star.partial .star-fill { position: absolute; top: 0; left: 0; overflow: hidden; white-space: nowrap; color: #FFB800; height: 100%; }
.masonry-grid .stars { margin-bottom: 14px; }

.masonry-grid { column-count: 3; column-gap: 20px; margin-top: 40px; }
.masonry-grid .testimonial-card { break-inside: avoid; -webkit-column-break-inside: avoid; margin-bottom: 20px; background: #fff; color: var(--hr-ink); padding: 26px; border: 1px solid var(--hr-border); border-radius: 16px; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.02); transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease; display: block; }
.masonry-grid .testimonial-card:hover { box-shadow: 0 14px 30px rgba(19, 45, 124, 0.1); transform: translateY(-2px); }
.masonry-grid .testimonial-card.card-tall { padding-bottom: 30px; }

.masonry-grid .quote-sign { color: var(--hr-brand-light); margin-bottom: 12px; }
.masonry-grid .quote { font-style: italic; font-size: 16px; line-height: 1.55; margin-bottom: 16px; }

.masonry-grid .doctor-meta { display: flex; align-items: center; gap: 12px; }
.masonry-grid .doctor-meta img { width: 46px; height: 46px; border-radius: 50%; object-fit: cover; border: 1px solid var(--hr-border); flex-shrink: 0; }
.masonry-grid .doctor-detail .name { font-size: 15px; font-weight: 700; color: var(--hr-ink); line-height: 1.3; margin: 0; }
.masonry-grid .doctor-detail .hospital { font-size: 13px; color: var(--hr-muted); margin: 2px 0 0; }
.masonry-grid .doctor-detail .position { font-size: 12px; color: var(--hr-brand-light); margin: 1px 0 0; }

.masonry-grid .video-card { position: relative; padding: 0; overflow: hidden; aspect-ratio: 4 / 5; background: linear-gradient(160deg, #16337f 0%, #0a1638 100%); }
.masonry-grid .video-card .video-thumb { position: absolute; inset: 0; background: linear-gradient(160deg, #16337f 0%, #0a1638 100%); overflow: hidden; display: flex; flex-direction: column; justify-content: flex-end; }
.masonry-grid .video-card .video-thumb img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .35s ease; }
.masonry-grid .video-card:hover .video-thumb img { transform: scale(1.05); }
.masonry-grid .video-card .video-trigger { position: absolute; inset: 0; width: 100%; height: 100%; padding: 0; margin: 0; border: 0; background: transparent; cursor: pointer; appearance: none; -webkit-appearance: none; z-index: 2; }
.masonry-grid .video-card .video-trigger:focus-visible { outline: 3px solid #fff; outline-offset: -3px; }
.masonry-grid .video-card .play-btn { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 56px; height: 56px; background: #fff; color: var(--hr-brand); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(0,0,0,.25); z-index: 1; transition: transform .25s ease; pointer-events: none; }
.masonry-grid .video-card:hover .play-btn { transform: translate(-50%, -50%) scale(1.08); }
.masonry-grid .video-card .video-meta { position: absolute; left: 0; right: 0; bottom: 0; z-index: 1; padding: 56px 14px 16px; color: #fff; background: linear-gradient(180deg, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.78) 100%); }
.masonry-grid .video-card .video-meta .name { font-weight: 700; }
.masonry-grid .video-card .video-meta .position { font-size: 12px; }
.masonry-grid .video-card .video-meta .stars { margin-bottom: 0; }
.masonry-grid .video-card .video-meta .star.full .star-icon,
.masonry-grid .video-card .video-meta .star.partial .star-fill { color: #ffd166; }
.masonry-grid .video-card .video-meta .star.empty .star-icon,
.masonry-grid .video-card .video-meta .star.partial .star-empty { color: rgba(255,255,255,.35); }

.masonry-grid .video-card .video-player { display: none; position: absolute; inset: 0; z-index: 3; background: #000; }
.masonry-grid .video-card .video-player iframe,
.masonry-grid .video-card .video-player video { width: 100%; height: 100%; display: block; border: 0; }
.masonry-grid .video-card.is-playing .video-thumb { opacity: 0; z-index: -1; }
.masonry-grid .video-card.is-playing .video-player { display: block; }
.masonry-grid .video-card .video-close { position: absolute; top: 12px; right: 12px; z-index: 5; width: 34px; height: 34px; padding: 0; margin: 0; border-radius: 50%; background: rgba(0,0,0,.6); -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px); color: #fff; border: 1px solid rgba(255,255,255,.18); box-shadow: 0 4px 12px rgba(0,0,0,.25); cursor: pointer; display: inline-flex; align-items: center; justify-content: center; line-height: 0; font-size: 0; appearance: none; -webkit-appearance: none; transition: background .2s ease, transform .2s ease, border-color .2s ease; }
.masonry-grid .video-card .video-close:hover { background: rgba(0,0,0,.85); border-color: rgba(255,255,255,.35); transform: scale(1.06); }
.masonry-grid .video-card .video-close:focus-visible { outline: 2px solid #fff; outline-offset: 2px; }
.masonry-grid .video-card .video-close svg { width: 14px; height: 14px; display: block; pointer-events: none; }

.rating-strip { margin: 56px auto 0; max-width: 900px; background: #fff; border: 1px solid var(--hr-border); border-radius: 16px; padding: 28px 30px; text-align: center; box-shadow: 0 6px 20px rgba(0,0,0,.03); }
.rating-strip-label { display: block; font-size: 13px; letter-spacing: .08em; text-transform: uppercase; color: var(--hr-muted); margin-bottom: 28px; }
.rating-strip-items { display: flex; justify-content: center; align-items: stretch; gap: 0; flex-wrap: wrap; }
.rating-strip-item { flex: 1 1 220px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; padding: 4px 24px; position: relative; }
.rating-strip-item + .rating-strip-item::before { content: ""; position: absolute; left: 0; top: 15%; bottom: 15%; width: 1px; background: var(--hr-border); }
.rating-strip-item .src-logo { display: inline-flex; align-items: center; justify-content: center; height: 32px; }
.rating-strip-item .src-logo img,
.rating-strip-item .src-logo svg { max-height: 32px; width: auto; display: block; }
.rating-strip-item .src-name { font-size: 12px; color: var(--hr-muted); letter-spacing: .04em; text-transform: uppercase; font-weight: 600; }
.rating-strip-item .score { display: inline-flex; align-items: center; gap: 8px; font-weight: 700; color: var(--hr-ink); font-size: 17px; }
.rating-strip-item .updated-note { font-size: 10px; color: var(--hr-muted); opacity: .7; }

@media screen and (max-width: 1100px) {
	.masonry-grid { column-count: 3; }
}
@media screen and (max-width: 992px) {
	.testimonials-wall .hero-grid { grid-template-columns: 1fr; gap: 40px; text-align: center; }
	.testimonials-wall .hero-content, .testimonials-wall .heading.text-left { text-align: center; }
	.testimonials-wall .heading p { margin-left: auto; margin-right: auto; }
	.testimonials-wall .hero-image-wrap { max-width: 560px; margin: 0 auto; }
}
@media screen and (max-width: 900px) {
	.masonry-grid { column-count: 2; }
	.rating-strip-items { gap: 8px; }
		.rating-strip-item + .rating-strip-item::before { display: none; }
	.rating-strip-item { padding: 14px; }
}
@media screen and (max-width: 767px) {
	.masonry-grid { column-count: 1; }
	.masonry-grid .testimonial-card { padding: 20px; }
	.masonry-grid .quote { font-size: 15px; }
	.testimonials-wall .hero-visual::before,
	.testimonials-wall .hero-visual::after { display: none; }
}

.customer-stories { background: linear-gradient(180deg, #fff 0%, var(--hr-bg-soft) 100%); }
.customer-stories .heading .eyebrow { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; color: var(--hr-brand-light); margin-bottom: 10px; }
.customer-stories .heading h2 { color: var(--hr-ink); }
.customer-stories .heading p { color: var(--hr-muted); max-width: 640px; margin-left: auto; margin-right: auto; }

.stories-slider { position: relative; margin-top: 40px; }
.stories-track { display: flex; gap: 0; overflow-x: auto; scroll-snap-type: x mandatory; padding: 4px; scrollbar-width: none; -ms-overflow-style: none; scroll-behavior: smooth; }
.stories-track::-webkit-scrollbar { display: none; }
.stories-track > * + * { margin-left: 24px; }

.story-card { scroll-snap-align: start; flex: 0 0 100%; display: grid; grid-template-columns: 1.05fr 1fr; overflow: hidden; background: #fff; border: 1px solid var(--hr-border); border-radius: 24px; box-shadow: 0 8px 32px rgba(19,45,124,.06); min-height: 440px; }
.story-card__content { padding: clamp(20px, 4vw, 30px); display: flex; flex-direction: column; justify-content: center; }
.story-card__media { position: relative; background: linear-gradient(160deg, #eef3fb 0%, #d8e3f2 100%); overflow: hidden; }
.story-card__media img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; }

.story-card .story-tag { display: inline-flex; align-self: flex-start; background: rgba(61,173,237,.12); color: var(--hr-brand); font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; padding: 6px 14px; border-radius: 999px; margin-bottom: 20px; }

.demo-cta-grid .demo-cta-copy p,
.story-card .story-title { font-size: clamp(20px, 2.4vw, 28px); font-weight: 700; color: var(--hr-ink); line-height: 1.5; margin-bottom: 14px; }
.story-card .story-desc { margin-bottom: 20px; }
.story-card .story-stats { list-style: none; padding: 0; margin: 0 0 20px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
.story-card .story-stats li { padding: 14px; background: var(--hr-bg-soft); border: 1px solid var(--hr-border); border-radius: 12px; display: flex; flex-direction: column; gap: 4px; min-width: 0; }
.story-card .story-stats .stat-num { font-size: 22px; font-weight: 800; color: var(--hr-brand); line-height: 1; letter-spacing: -0.02em; }
.story-card .story-stats .stat-label { font-size: 11px; }
.story-card .story-cta { display: inline-flex; align-items: center; gap: 10px; background: var(--hr-brand); color: #fff; padding: 16px; border-radius: 10px; font-size: 14px; font-weight: 700; text-decoration: none; align-self: flex-start; transition: background .2s ease, transform .2s ease, box-shadow .2s ease; }
.story-card .story-cta:hover { background: #0d2160; color: #fff; transform: translateY(-1px); box-shadow: 0 10px 24px rgba(19,45,124,.25); }
.story-card .story-cta svg { transition: transform .2s ease; }
.story-card .story-cta:hover svg { transform: translateX(4px); }

.stories-controls { display: flex; align-items: center; justify-content: center; gap: 16px; margin-top: 24px; }
.stories-controls .story-btn { width: 44px; height: 44px; border-radius: 50%; border: 1px solid var(--hr-border); background: #fff; color: var(--hr-brand); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background .2s ease, color .2s ease, border-color .2s ease, transform .2s ease; padding: 0; }
.stories-controls .story-btn:hover { background: var(--hr-brand); color: #fff; border-color: var(--hr-brand); transform: translateY(-1px); }
.stories-controls .story-dots { display: flex; align-items: center; gap: 8px; }
.stories-controls .story-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--hr-border); border: none; padding: 0; cursor: pointer; transition: background .2s ease, width .2s ease; }
.stories-controls .story-dot.is-active { background: var(--hr-brand); width: 24px; border-radius: 4px; }

@media screen and (max-width: 900px) {
	.story-card { grid-template-columns: 1fr; min-height: auto; }
	.story-card__media { min-height: 260px; aspect-ratio: 16 / 10; order: -1; }
}
@media screen and (max-width: 560px) {
	.story-card__content { padding: 24px; }
	.story-card .story-desc { font-size: 15px; }
	.story-card .story-stats { grid-template-columns: 1fr; gap: 8px; }
	.story-card .story-stats li { flex-direction: row; align-items: baseline; justify-content: space-between; }
	.story-card .story-stats .stat-num { font-size: 20px; }
	.story-card .story-stats .stat-label { text-align: right; flex: 1; padding-left: 12px; }
}

.hr-lgstrip { position: relative; z-index: 1; margin-top: 48px; padding: 32px 24px 0; width: 100%; }
.hr-lgstrip-inner { max-width: 1180px; margin: 0 auto; width: 100%; }
.hr-lgstrip-label { display: block; text-align: center; font-size: 13px; letter-spacing: 0.8px; text-transform: uppercase; color: var(--hr-brand); font-weight: 600; margin-bottom: 28px; }
.hr-lgstrip-viewport { position: relative; overflow: hidden; width: 100%; -webkit-mask-image: linear-gradient(90deg, transparent 0, #000 64px, #000 calc(100% - 64px), transparent 100%); mask-image: linear-gradient(90deg, transparent 0, #000 64px, #000 calc(100% - 64px), transparent 100%); }
.hr-lgstrip-track { display: flex; align-items: center; width: max-content; animation: hr-lgstrip-scroll 32s linear infinite; }
.hr-lgstrip-viewport:hover .hr-lgstrip-track { animation-play-state: paused; }
.hr-lgstrip-set { display: flex; align-items: center; flex-shrink: 0; }
.hr-lgstrip-set img { display: block; height: 45px; width: auto; margin: 0 40px; filter: grayscale(1); opacity: .75; transition: filter .25s ease, opacity .25s ease, transform .25s ease; }
.hr-lgstrip-set img:hover { filter: grayscale(0); opacity: 1; }
@keyframes hr-lgstrip-scroll {
    from { transform: translateX(0); }
    to { transform: translateX(-50%); }
}

@media (prefers-reduced-motion: reduce) {
    .hr-lgstrip-track { animation: none; overflow-x: auto; }
}

@media screen and (max-width: 767px) {
    .hr-lgstrip { padding: 24px 16px; }
    .hr-lgstrip-track { animation-duration: 22s; }
    .hr-lgstrip-set img { height: 34px; margin: 0 20px; }
}

/* ---------- Security & Compliance ---------- */
.security-compliance { background: #fff; }
.security-compliance .heading.text-center { max-width: 640px; margin: 0 auto; margin-bottom: 36px; text-align: center; }
.security-compliance .heading .eyebrow { color: var(--hr-brand-light); }
.security-compliance .heading .eyebrow svg { flex-shrink: 0; }
.security-compliance .heading h2 { color: var(--hr-ink); }
.security-compliance .heading p { color: var(--hr-muted); margin: 0; }

.security-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: stretch; }

.cert-panel,
.feature-panel { display: flex; flex-direction: column; background: var(--hr-bg-soft); border: 1px solid var(--hr-border); border-radius: 20px; padding: 28px; }
.feature-panel { background: #fff; }

.cert-panel-label { display: block; text-align: center; font-size: 12px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--hr-muted); margin-bottom: 20px; }

.cert-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px; }
.cert-item { background: #fff; border: 1px solid var(--hr-border); border-radius: 14px; padding: 16px 10px; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 6px; }
.cert-item .cert-icon { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; color: var(--hr-brand); }
.cert-item .cert-name { font-size: 13px; font-weight: 700; color: var(--hr-ink); line-height: 1.3; }
.cert-item .cert-desc { font-size: 11px; color: var(--hr-muted); line-height: 1.3; }

.security-note { display: flex; align-items: flex-start; gap: 10px; background: #eef3fb; border: 1px solid rgba(19,45,124,.12); border-radius: 12px; padding: 14px 16px; margin-top: auto; }
.security-note .note-icon { flex-shrink: 0; width: 20px; height: 20px; color: var(--hr-brand); margin-top: 2px; }
.security-note p { margin: 0; font-size: 13px; color: var(--hr-ink); line-height: 1.5; }

.feature-list { display: flex; flex-direction: column; gap: 18px; margin-bottom: 20px; }
.feature-item { display: flex; gap: 14px; align-items: flex-start; }
.feature-item .feature-icon { flex-shrink: 0; width: 40px; height: 40px; border-radius: 10px; background: var(--hr-brand); color: #fff; display: flex; align-items: center; justify-content: center; }
.feature-item .feature-copy h3 { font-size: 15px; margin: 0 0 4px; color: var(--hr-ink); }
.feature-item .feature-copy p { font-size: 13px; margin: 0; color: var(--hr-muted); line-height: 1.5; }

@media screen and (max-width: 992px) {
    .security-grid { grid-template-columns: 1fr; }
}
@media screen and (max-width: 560px) {
    .cert-grid { grid-template-columns: repeat(2, 1fr); }
    .cert-panel, .feature-panel { padding: 20px; }
}

/* ---------- Book A Free Demo CTA ---------- */
.demo-cta { position: relative; overflow: hidden; background: linear-gradient(135deg, #eef1fb 0%, #eaf2fc 100%); }
.demo-cta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 140px; align-items: center; }
.demo-cta-copy { position: relative; z-index: 1; }
.demo-cta::before { content: ""; position: absolute; left: -60px; top: -70px; width: 260px; height: 260px; border-radius: 50%; background: radial-gradient(circle, rgba(61,173,237,.18) 0%, rgba(61,173,237,0) 70%); z-index: 0; }
.demo-cta::after { content: ""; position: absolute; left: 6%; bottom: -90px; width: 200px; height: 200px; border-radius: 50%; background: radial-gradient(circle, rgba(19,45,124,.1) 0%, rgba(19,45,124,0) 70%); z-index: 0; }
.demo-cta .lead-card { position: relative; z-index: 1; background: #fff; border: 1px solid var(--hr-border); border-radius: 20px; padding: 32px; box-shadow: 0 10px 34px rgba(19,45,124,.08); }
/* FIX: title paragraph needs its own class now that both title and description are <p> tags,
   otherwise the ".lead-card > p" rule below overrides the title styling and both look identical */
.demo-cta .lead-card .lead-card-title { font-size: 22px; color: var(--hr-ink); margin: 0 0 6px; font-weight: 700; }
.demo-cta .lead-card > p:not(.lead-card-title) { font-size: 14px; color: var(--hr-muted); margin: 0 0 22px; }

@media screen and (max-width: 900px) {
    .demo-cta-grid { grid-template-columns: 1fr; gap: 32px; }
    .demo-cta-copy { text-align: center; }
}
@media screen and (max-width: 560px) {
    .demo-cta .lead-card { padding: 24px; }
}

</style>

<?php
$hr_slider_media = 'https://healthray.com/wp-content/uploads';
$hr_slider_logos = array(
	array( 'Universal Multispeciality Hospital logo', $hr_slider_media . '/2024/04/Universal-Multispecialty-Hospital.webp' ),
	array( 'Sri Manakula Vinayagar Hospital logo', $hr_slider_media . '/2024/11/Sri-Manakula-Vinayagar-Hospital.webp' ),
	array( 'Shushrusha Hospital logo', $hr_slider_media . '/2024/06/Shushrusha-Hospital.webp' ),
	array( 'Tanvir Hospital logo', $hr_slider_media . '/2024/10/Tanvir-Hospital.webp' ),
	array( 'Gastron Super Speciality Hospital logo', $hr_slider_media . '/2024/06/Gastron.webp' ),
	array( 'GM Hospital logo', $hr_slider_media . '/2024/09/GM-Hospital.webp' ),
	array( 'Oriental Lily Hospital logo', $hr_slider_media . '/2024/04/Oriental-Lily-Hospital.webp' ),
	array( 'Lilavati Hospital logo', $hr_slider_media . '/2025/08/Lilavati-Hospital.webp' ),
	array( 'Shraddha Arogya Mandir logo', $hr_slider_media . '/2025/08/Shraddha-Arogya-Mandir.webp' ),
	array( 'Aatmaj Healthcare logo', $hr_slider_media . '/2024/07/Jupiter-Hospital.webp' ),
);
?>
<section class="sec-padded hero-section testimonials-wall">
	<div class="container">
		<div class="hero-grid">
			<div class="hero-content">
				<div class="heading text-left">
					<h1 class="eyebrow">Healthray Reviews</h1>
					<h2>See What Doctors & Hospitals Say About Healthray</h2>
					<?php if ( get_the_content() ) : ?>
						<?php the_content(); ?>
					<?php else : ?>
						<p>Hear directly from doctors, hospital leaders, and healthcare teams about their experience using Healthray in their daily operations.</p>
					<?php endif; ?>
				</div>

				<button type="button" class="btn-primary hr-cta-btn">Book A Demo</button>
			</div>

			<div class="hero-visual">
				<div class="hero-image-wrap">
					<img src="https://healthray.com/wp-content/uploads/2026/08/Healthray-Reviews-hero-image.webp" alt="Doctors love Healthray and we love them back" loading="eager" decoding="async" width="800" height="auto">
				</div>
			</div>
		</div>
	</div>

	<div class="hr-lgstrip" aria-label="Hospitals using Healthray">
		<div class="hr-lgstrip-inner">
			<span class="hr-lgstrip-label">Trusted by 2,500+ hospitals &amp; clinics across India</span>
			<div class="hr-lgstrip-viewport">
				<div class="hr-lgstrip-track">
					<div class="hr-lgstrip-set">
						<?php foreach ( $hr_slider_logos as $hr_logo ) : ?>
							<img src="<?php echo esc_url( $hr_logo[1] ); ?>" alt="<?php echo esc_attr( $hr_logo[0] ); ?>" loading="lazy" decoding="async" width="120" height="54">
						<?php endforeach; ?>
					</div>
					<div class="hr-lgstrip-set" aria-hidden="true">
						<?php foreach ( $hr_slider_logos as $hr_logo ) : ?>
							<img src="<?php echo esc_url( $hr_logo[1] ); ?>" alt="" loading="lazy" decoding="async" width="120" height="54">
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="testimonials-cards sec-padded-40 wall-of-love">
	<div class="container">
		<div class="heading text-center">
			<span class="eyebrow"><span class="heart">&#10084;</span> Wall of Love</span>
			<h2>What Doctors and Hospital Teams Say About Healthray</h2>
			<!--<p>Real experiences from doctors, specialists, hospital leaders, and IT teams using Healthray in their day-to-day work.</p>-->
		</div>

		<div class="masonry-grid">
			<?php foreach ( $testimonials as $testimonial ) :
				$name        = $testimonial['name'];
				$quote       = $testimonial['quote'];
				$image       = $testimonial['image'];
				$hospital    = $testimonial['hospital'];
				$position    = $testimonial['position'];
				$rating      = $testimonial['rating'];
				$video_url   = $testimonial['video'];
				$video_thumb = $testimonial['video_thumb'];

				if ( empty( $name ) && empty( $quote ) && empty( $video_url ) ) {
					continue;
				}

				$is_video   = ! empty( $video_url );
				$embed_meta = $is_video ? healthray_video_embed_meta( $video_url ) : null;
				$card_class = $is_video ? 'testimonial-card video-card' : 'testimonial-card';
				if ( ! $is_video && ! empty( $quote ) && strlen( $quote ) > 180 ) {
					$card_class .= ' card-tall';
				}

				$video_aria_label = $name ? 'Play testimonial video from ' . $name : 'Play testimonial video';
				?>

				<div class="<?php echo esc_attr( $card_class ); ?>"
					<?php if ( $is_video && $embed_meta ) : ?>
					data-video-type="<?php echo esc_attr( $embed_meta['type'] ); ?>"
					data-video-src="<?php echo esc_attr( $embed_meta['src'] ); ?>"
					<?php endif; ?>
				>
					<?php if ( $is_video ) : ?>
						<div class="video-thumb">
							<?php
							$video_thumb_src = healthray_resolve_image_src( $video_thumb, 'large' );
							$card_image_src  = healthray_resolve_image_src( $image, 'large' );

							if ( $video_thumb_src ) {
								printf(
									'<img src="%s" alt="" loading="lazy" decoding="async" class="thumb-img">',
									esc_url( $video_thumb_src )
								);
							} elseif ( $card_image_src ) {
								printf(
									'<img src="%s" alt="" loading="lazy" decoding="async" class="thumb-img">',
									esc_url( $card_image_src )
								);
							} elseif ( ! empty( $embed_meta['thumb'] ) ) {
								printf(
									'<img src="%s" alt="" loading="lazy" decoding="async" class="thumb-img">',
									esc_url( $embed_meta['thumb'] )
								);
							}
							?>
							<span class="play-btn" aria-hidden="true">
								<svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
							</span>
							<div class="video-meta">
								<p class="name"><?php echo esc_html( $name ); ?></p>
								<p class="position"><?php echo esc_html( $position ); ?><?php echo $hospital ? ', ' . esc_html( $hospital ) : ''; ?></p>
								<?php if ( ! empty( $rating ) ) : ?>
								<div class="stars"><?php echo healthray_render_stars( $rating, $starSVG ); ?></div>
								<?php endif; ?>
							</div>
							<button type="button" class="video-trigger" aria-label="<?php echo esc_attr( $video_aria_label ); ?>"></button>
						</div>
						<div class="video-player"></div>
					<?php else : ?>
						<?php if ( ! empty( $quote ) ) : ?>
						<div class="quote-sign"><?php echo $quoteSVG; ?></div>
						<p class="quote"><?php echo esc_html( $quote ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $rating ) ) : ?>
						<div class="stars"><?php echo healthray_render_stars( $rating, $starSVG ); ?></div>
						<?php endif; ?>

						<div class="doctor-meta">
							<?php
							$doctor_photo_src = healthray_resolve_image_src( $image, 'thumbnail' );
							if ( $doctor_photo_src ) :
								printf(
									'<img src="%s" alt="%s" loading="lazy" decoding="async">',
									esc_url( $doctor_photo_src ),
									esc_attr( ! empty( $name ) ? $name : 'Healthray customer' )
								);
							endif;
							?>
							<div class="doctor-detail">
								<p class="name"><?php echo esc_html( $name ); ?></p>
								<p class="hospital"><?php echo esc_html( $hospital ); ?></p>
								<p class="position"><?php echo esc_html( $position ); ?></p>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ($avgRating > 0) : ?>
			<?php
			$google_rating   = function_exists('get_field') ? get_field('google_reviews_rating', 'option') : '';
			$facebook_rating = function_exists('get_field') ? get_field('facebook_reviews_rating', 'option') : '';
			$ratings_updated = function_exists('get_field') ? get_field('external_ratings_updated_date', 'option') : '';
			$google_rating   = ($google_rating !== '' && $google_rating !== null) ? floatval($google_rating) : 4.8;
			$facebook_rating = ($facebook_rating !== '' && $facebook_rating !== null) ? floatval($facebook_rating) : 4.6;
			?>
			<div class="rating-strip">
				<span class="rating-strip-label">Healthray Ratings Across Review Platforms</span>
				<div class="rating-strip-items">
					<div class="rating-strip-item">
						<span class="src-logo" aria-label="TechJockey">
							<img src="https://healthray.com/wp-content/uploads/2026/08/TechJockey.svg" alt="TechJockey" width="120" height="32" loading="lazy" decoding="async">
						</span>
						<span class="src-name">TechJockey</span>
						<span class="score">4.7<span class="stars"><?php echo healthray_render_stars( 4.7, $starSVG ); ?></span></span>
					</div>
					<div class="rating-strip-item">
						<span class="src-logo" aria-label="Google Reviews">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="30" height="30" aria-hidden="true">
								<path fill="#4285F4" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z" />
								<path fill="#34A853" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z" />
								<path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z" />
								<path fill="#EA4335" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z" />
							</svg>
						</span>
						<span class="src-name">Google</span>
						<span class="score"><?php echo esc_html(number_format($google_rating, 1)); ?><span class="stars"><?php echo healthray_render_stars($google_rating, $starSVG); ?></span></span>
					</div>
					<div class="rating-strip-item">
						<span class="src-logo" aria-label="Facebook">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="30" height="30" fill="#1877F2" aria-hidden="true">
								<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
							</svg>
						</span>
						<span class="src-name">Facebook</span>
						<span class="score"><?php echo esc_html(number_format($facebook_rating, 1)); ?><span class="stars"><?php echo healthray_render_stars($facebook_rating, $starSVG); ?></span></span>
					</div>
				</div>
				<?php if ($ratings_updated) : ?>
					<span class="updated-note">Google/Facebook ratings last verified <?php echo esc_html($ratings_updated); ?></span>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<script>
(function () {
	function playInCard(card) {
		var type = card.getAttribute('data-video-type');
		var src = card.getAttribute('data-video-src');
		var player = card.querySelector('.video-player');
		if (!type || !src || !player) return;

		player.innerHTML = '';

		if (type === 'file') {
			var video = document.createElement('video');
			video.controls = true;
			video.playsInline = true;
			video.setAttribute('playsinline', '');
			video.preload = 'metadata';

			var source = document.createElement('source');
			source.src = src;
			source.type = 'video/mp4';
			video.appendChild(source);

			video.addEventListener('error', function () {
				console.error('Healthray video testimonial failed to play. This is almost always a codec issue - the file is likely encoded as HEVC/H.265 (the iPhone camera default), which only Safari can decode. Re-export/re-encode the file as H.264 (AAC audio) MP4 and re-upload. Source URL:', src);
				player.innerHTML = '<div style="color:#fff;padding:20px;font-size:13px;line-height:1.5;">' +
					'This video could not be played in this browser. It may need to be re-encoded to H.264 MP4.<br><a href="' + src + '" target="_blank" rel="noopener" style="color:#8ecbff;">Open the video file directly</a>' +
					'</div>';
			});

			player.appendChild(video);
			var attempt = video.play();
			if (attempt && typeof attempt.catch === 'function') {
				attempt.catch(function () {
					video.muted = true;
					video.play().catch(function () {
					});
				});
			}
		} else {
			var iframe = document.createElement('iframe');
			iframe.setAttribute('allow', 'autoplay; encrypted-media; picture-in-picture');
			iframe.setAttribute('allowfullscreen', '');
			iframe.setAttribute('frameborder', '0');
			iframe.src = src
				.replace('controls=0', 'controls=1')
				.replace('disablekb=1', 'disablekb=0')
				.replace('cc_load_policy=0', 'cc_load_policy=1');
			player.appendChild(iframe);
		}

		var closeBtn = document.createElement('button');
		closeBtn.type = 'button';
		closeBtn.className = 'video-close';
		closeBtn.setAttribute('aria-label', 'Stop video');
		closeBtn.innerHTML = '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M6 18L18 6"/></svg>';
		player.appendChild(closeBtn);

		card.classList.add('is-playing');
		closeBtn.focus();
	}

	function stopInCard(card) {
		var player = card.querySelector('.video-player');
		if (player) player.innerHTML = '';
		card.classList.remove('is-playing');
		var trigger = card.querySelector('.video-trigger');
		if (trigger) trigger.focus();
	}

	function init() {
		document.addEventListener('click', function (e) {
			var closeBtn = e.target.closest('.video-close');
			if (closeBtn) {
				var playingCard = closeBtn.closest('.video-card');
				if (playingCard) stopInCard(playingCard);
				return;
			}

			var trigger = e.target.closest('.video-trigger');
			if (trigger) {
				var card = trigger.closest('.video-card');
				if (card && !card.classList.contains('is-playing')) {
					document.querySelectorAll('.video-card.is-playing').forEach(function (openCard) {
						if (openCard !== card) stopInCard(openCard);
					});
					playInCard(card);
				}
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
</script>

<?php if ( $ratingCount > 0 ) : ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "name": "Healthray",
    "applicationCategory": "HealthApplication",
    "operatingSystem": "Web, iOS, Android",
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "<?php echo esc_js( $avgRating ); ?>",
        "reviewCount": "<?php echo esc_js( $ratingCount ); ?>",
        "bestRating": "5"
    },
    "review": [
        <?php
        $reviewItems = [];
        foreach ( $testimonials as $t ) {
        	if ( empty( $t['quote'] ) || empty( $t['rating'] ) ) continue;
        	$reviewItems[] = json_encode( [
        		"@type"        => "Review",
        		"author"       => [ "@type" => "Person", "name" => $t['name'] ],
        		"reviewRating" => [
        			"@type"      => "Rating",
        			"ratingValue" => strval( floatval( $t['rating'] ) ),
        			"bestRating" => "5"
        		],
        		"reviewBody"   => wp_strip_all_tags( $t['quote'] )
        	] );
        }
        echo implode( ",\n        ", $reviewItems );
        ?>
    ]
}
</script>

<?php
$videoItems = [];
foreach ( $testimonials as $t ) {
	if ( empty( $t['video'] ) ) continue;
	$meta = healthray_video_embed_meta( $t['video'] );
	if ( ! $meta ) continue;
	$schema_thumb = healthray_resolve_image_src( $t['video_thumb'], 'large' );
	if ( ! $schema_thumb ) {
		$schema_thumb = healthray_resolve_image_src( $t['image'], 'large' );
	}
	if ( ! $schema_thumb && ! empty( $meta['thumb'] ) ) {
		$schema_thumb = $meta['thumb'];
	}
	$videoItem = [
		"@context"    => "https://schema.org",
		"@type"       => "VideoObject",
		"name"        => trim( ( $t['name'] ? $t['name'] . ' ' : '' ) . 'testimonial for Healthray' ),
		"description" => trim( ( $t['position'] ? $t['position'] . ', ' : '' ) . ( $t['hospital'] ?: '' ) ) ?: 'Doctor testimonial for Healthray',
		"uploadDate"  => get_the_modified_date( 'c' ),
		"embedUrl"    => $meta['src'],
	];
	if ( $schema_thumb ) {
		$videoItem['thumbnailUrl'] = $schema_thumb;
	}
	$videoItems[] = $videoItem;
}
if ( ! empty( $videoItems ) ) :
	foreach ( $videoItems as $videoItem ) : ?>
	
<script type="application/ld+json">
<?php echo wp_json_encode( $videoItem ); ?>
</script>
	<?php endforeach;
endif;
endif; ?>

<?php
$stories_query = new WP_Query( [
	'post_type'      => 'case-studies',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'no_found_rows'  => true,
] );

if ( $stories_query->have_posts() ) : ?>

<section class="sec-padded-40 customer-stories">
	<div class="container">
		<div class="heading text-center">
			<span class="eyebrow">Customer Stories</span>
			<h2>See the Results Hospitals Achieve With Healthray</h2>
			<p>Verified outcomes from hospitals and diagnostic centres across India cost savings, faster workflows, happier patients.</p>
		</div>

		<div class="stories-slider" data-slider>
			<div class="stories-track" data-track>
				<?php while ( $stories_query->have_posts() ) : $stories_query->the_post();
					$story_id = get_the_ID();
					$story_metrics = [];
					if ( have_rows( 'metrics', $story_id ) ) {
						while ( have_rows( 'metrics', $story_id ) ) {
							the_row();
							$story_metrics[] = [
								'num'   => get_sub_field( 'title' ),
								'label' => get_sub_field( 'text' ),
							];
							if ( count( $story_metrics ) >= 3 ) {
								break;
							}
						}
					}
					$story_tag   = '';
					$story_terms = get_the_category( $story_id );
					if ( ! empty( $story_terms ) && ! is_wp_error( $story_terms ) ) {
						$story_tag = $story_terms[0]->name;
					}
					if ( has_post_thumbnail( $story_id ) ) {
						$story_image_html = get_the_post_thumbnail( $story_id, 'large', [
							'alt'      => get_the_title(),
							'loading'  => 'lazy',
							'decoding' => 'async',
						] );
					} else {
						$story_image_html = wp_get_attachment_image( 62392, 'large', false, [
							'alt'      => get_the_title(),
							'loading'  => 'lazy',
							'decoding' => 'async',
						] );
					}
					?>
					<article class="story-card">
						<div class="story-card__content">
							<?php if ( $story_tag ) : ?>
								<span class="story-tag"><?php echo esc_html( $story_tag ); ?></span>
							<?php endif; ?>
							<p class="story-title"><?php the_title(); ?></p>
							<p class="story-desc"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 30, '…' ) ); ?></p>
							<?php if ( ! empty( $story_metrics ) ) : ?>
								<ul class="story-stats">
									<?php foreach ( $story_metrics as $metric ) : ?>
										<li>
											<span class="stat-num"><?php echo esc_html( $metric['num'] ); ?></span>
											<span class="stat-label"><?php echo esc_html( $metric['label'] ); ?></span>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
							<a href="<?php echo esc_url( get_permalink() ); ?>" class="story-cta">
								Read case study
								<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="M13 6l6 6-6 6"/></svg>
							</a>
						</div>
						<div class="story-card__media">
							<?php echo $story_image_html; // already-escaped WP image HTML ?>
						</div>
					</article>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>

			<div class="stories-controls">
				<button type="button" class="story-btn story-prev" aria-label="Previous stories">
					<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5"/><path d="M11 6l-6 6 6 6"/></svg>
				</button>
				<div class="story-dots" data-dots></div>
				<button type="button" class="story-btn story-next" aria-label="Next stories">
					<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="M13 6l6 6-6 6"/></svg>
				</button>
			</div>
		</div>
	</div>
</section>

<?php endif; // stories_query->have_posts() ?>

<script>
(function () {
	var AUTOPLAY_MS = 3000;
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function initSlider(slider) {
		var track    = slider.querySelector('[data-track]');
		var prev     = slider.querySelector('.story-prev');
		var next     = slider.querySelector('.story-next');
		var dotsWrap = slider.querySelector('[data-dots]');
		if (!track || !prev || !next || !dotsWrap) return;

		var cards = track.querySelectorAll('.story-card');
		if (!cards.length) return;

		var autoplayTimer = null;

		function cardStep() {
			var second = cards[1];
			var gap = second ? (second.offsetLeft - (cards[0].offsetLeft + cards[0].offsetWidth)) : 0;
			return cards[0].offsetWidth + Math.max(0, gap);
		}

		function currentIndex() {
			return Math.round(track.scrollLeft / cardStep());
		}

		function goTo(index) {
			var total = cards.length;
			if (index < 0) index = total - 1;
			if (index >= total) index = 0;
			track.scrollTo({ left: cardStep() * index, behavior: 'smooth' });
		}

		function buildDots() {
			dotsWrap.innerHTML = '';
			for (var i = 0; i < cards.length; i++) {
				var dot = document.createElement('button');
				dot.type = 'button';
				dot.className = 'story-dot';
				dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
				(function (index) {
					dot.addEventListener('click', function () {
						goTo(index);
						resetAutoplay();
					});
				})(i);
				dotsWrap.appendChild(dot);
			}
			update();
		}

		function update() {
			var page = currentIndex();
			var dots = dotsWrap.querySelectorAll('.story-dot');
			dots.forEach(function (d, i) { d.classList.toggle('is-active', i === page); });
		}

		function startAutoplay() {
			if (reduceMotion || cards.length < 2) return;
			stopAutoplay();
			autoplayTimer = setInterval(function () { goTo(currentIndex() + 1); }, AUTOPLAY_MS);
		}

		function stopAutoplay() {
			if (autoplayTimer) { clearInterval(autoplayTimer); autoplayTimer = null; }
		}

		function resetAutoplay() {
			if (reduceMotion) return;
			stopAutoplay();
			startAutoplay();
		}

		prev.addEventListener('click', function () { goTo(currentIndex() - 1); resetAutoplay(); });
		next.addEventListener('click', function () { goTo(currentIndex() + 1); resetAutoplay(); });

		slider.addEventListener('mouseenter', stopAutoplay);
		slider.addEventListener('mouseleave', startAutoplay);
		slider.addEventListener('focusin', stopAutoplay);
		slider.addEventListener('focusout', startAutoplay);
		slider.addEventListener('touchstart', stopAutoplay, { passive: true });
		slider.addEventListener('touchend', function () {
			setTimeout(startAutoplay, AUTOPLAY_MS);
		}, { passive: true });

		document.addEventListener('visibilitychange', function () {
			if (document.hidden) stopAutoplay();
			else startAutoplay();
		});

		var scrollTimer;
		track.addEventListener('scroll', function () {
			if (scrollTimer) cancelAnimationFrame(scrollTimer);
			scrollTimer = requestAnimationFrame(update);
		});

		var resizeTimer;
		window.addEventListener('resize', function () {
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(function () {
				buildDots();
				track.scrollTo({ left: cardStep() * currentIndex(), behavior: 'auto' });
			}, 150);
		});

		buildDots();
		startAutoplay();
	}

	function initAll() {
		document.querySelectorAll('[data-slider]').forEach(initSlider);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initAll);
	} else {
		initAll();
	}
})();
</script>

<?php
$hr_shield_icon = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v6c0 5-3.5 8-7 9-3.5-1-7-4-7-9V6l7-3z"/></svg>';

$hr_shield_check_icon = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v6c0 5-3.5 8-7 9-3.5-1-7-4-7-9V6l7-3z"/><path d="M9 12l2 2 4-4"/></svg>';

$hr_cert_badge_icon = '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="9" r="6"/><path d="M9 14.5L7 21l5-3 5 3-2-6.5"/></svg>';

$hr_certifications = array(
	array( 'ISO 27001:2022', 'Information Security Management' ),
	array( 'ISO 9001:2015', 'Quality Management System' ),
	array( 'HIPAA Compliant', 'Health Information Privacy' ),
	array( 'GDPR Compliant', 'Data Protection Regulation' ),
	array( 'SOC 2 Type II', 'Security, Availability & Confidentiality' ),
	array( 'MeitY Empanelled', 'Government of India Empanelled' ),
);

$hr_security_features = array(
	array(
		'icon'  => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/></svg>',
		'title' => 'End-to-End Encryption',
		'desc'  => 'All data is encrypted while stored and transmitted, ensuring that sensitive information remains protected.',
	),
	array(
		'icon'  => $hr_shield_check_icon,
		'title' => 'Multi-Factor Authentication (MFA)',
		'desc'  => 'Adds an extra layer of security by verifying user identity through multiple authentication steps.',
	),
	array(
		'icon'  => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>',
		'title' => 'Permission-Based Access',
		'desc'  => 'Role-based access control ensures users can only view and manage data based on their authorized permissions.',
	),
	array(
		'icon'  => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 18h9a4 4 0 000-8 5.6 5.6 0 00-10.9-1.6A4 4 0 007 18z"/></svg>',
		'title' => 'Cloud-Based Data Storage',
		'desc'  => 'Hospital information is securely stored in a robust cloud infrastructure with automated backups and disaster recovery.',
	),
);
?>

<section class="sec-padded-40 security-compliance">
	<div class="container">
		<div class="heading text-center">
			<span class="eyebrow">Security &amp; Compliance</span>
			<h2>Built to Protect Your Hospital Data</h2>
			<p>Healthray uses industry-standard security practices to keep your hospital data safe, private and always under your control.</p>
		</div>

		<div class="security-grid">
			<div class="cert-panel">
				<span class="cert-panel-label">Our Certifications &amp; Compliances</span>
				<div class="cert-grid">
					<?php foreach ( $hr_certifications as $hr_cert ) : ?>
						<div class="cert-item">
							<span class="cert-icon"><?php echo $hr_cert_badge_icon; ?></span>
							<b class="cert-name"><?php echo esc_html( $hr_cert[0] ); ?></b>
							<span class="cert-desc"><?php echo esc_html( $hr_cert[1] ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="security-note">
					<span class="note-icon"><?php echo $hr_shield_check_icon; ?></span>
					<p>Regular audits, vulnerability assessments and strict security controls are protected at every step.</p>
				</div>
			</div>

			<div class="feature-panel">
				<div class="feature-list">
					<?php foreach ( $hr_security_features as $hr_feature ) : ?>
						<div class="feature-item">
							<span class="feature-icon"><?php echo $hr_feature['icon']; ?></span>
							<div class="feature-copy">
								<h3><?php echo esc_html( $hr_feature['title'] ); ?></h3>
								<p><?php echo esc_html( $hr_feature['desc'] ); ?></p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="security-note">
					<span class="note-icon"><?php echo $hr_shield_check_icon; ?></span>
					<p>Your trust is our responsibility. We follow strict security practices to keep your hospital data safe and always accessible to you.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="sec-padded-40 demo-cta">
	<div class="container">
		<div class="demo-cta-grid">
			<div class="demo-cta-copy">
				<p>Transform Your Hospital With Healthray For 10x Time Savings!</p>
			</div>
			<div class="lead-card">
				<p class="lead-card-title">Book A Free Demo</p>
				<p>See how Healthray fits your hospital workflows. Our team replies within one business day.</p>
				<?php echo do_shortcode( '[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]' ); ?>
			</div>
		</div>
	</div>
</section>