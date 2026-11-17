<?php
/**
 * Consent for video player plugins that build the player with JavaScript.
 *
 * @package CookieYes\Lite\Frontend\Modules\Video_Players
 */

namespace CookieYes\Lite\Frontend\Modules\Video_Players;

use CookieYes\Lite\Admin\Modules\Cookies\Includes\Category_Controller;
use CookieYes\Lite\Admin\Modules\Cookies\Includes\Cookie_Categories;
use CookieYes\Lite\Admin\Modules\Cookies\Includes\Cookie_Controller;
use CookieYes\Lite\Admin\Modules\Settings\Includes\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Video_Players
 *
 * Some player plugins print an empty container and build the YouTube player in JavaScript. The
 * CookieYes blocker cancels the first YouTube request they make, but the player loads the video
 * again in a way the blocker does not catch, so YouTube sets its cookies before consent
 * (reproduced with Video Player for YouTube 2.1.2).
 *
 * Instead of chasing the iframe, this holds back the player plugin's own scripts until the visitor
 * consents to the categories the banner blocks the provider under, as other consent plugins do:
 * the script tags are printed inert (`type="text/plain"`, URL in `data-cky-player-src`), so nothing
 * is built and nothing loads. Once those categories are accepted, a small loader runs them one after
 * another in the order the browser would have (classic scripts first, then deferred ones). Releasing
 * them in that order matters: the player script depends on the Plyr library, and re-inserting all
 * of them at once — what the banner script does with blocked scripts — runs them out of order.
 */
class Video_Players {

	/**
	 * Fallback category for a player added through the filter without one.
	 *
	 * @var string
	 */
	const DEFAULT_CATEGORY = 'advertisement';

	/**
	 * Categories resolved for this request, per player.
	 *
	 * @var array
	 */
	private $categories = array();

	/**
	 * Whether the CookieYes banner script is on this page, resolved once per request.
	 *
	 * @var boolean|null
	 */
	private $banner_loads = null;

	/**
	 * Whether the site is connected to the CookieYes web app, resolved once per request.
	 *
	 * @var boolean|null
	 */
	private $connected = null;

	/**
	 * Players whose scripts were held back on this page: slug => category. The loader is printed
	 * only when this is not empty.
	 *
	 * @var array
	 */
	private $held = array();

	/**
	 * Hosts the held scripts are served from (a CDN or a mapped domain can differ from the page's),
	 * so the loader releases exactly what was held: host => true.
	 *
	 * @var array
	 */
	private $hosts = array();

	/**
	 * Module is always available; it only acts on the player plugins it knows.
	 *
	 * @return boolean
	 */
	public function is_active() {
		return ! is_admin();
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'script_loader_tag', array( $this, 'mark_player_script' ), 20, 3 );
		// Late, so player scripts printed in the footer are already on the page and recorded.
		add_action( 'wp_footer', array( $this, 'print_loader' ), PHP_INT_MAX );
	}

	/**
	 * Known player plugins: the path fragment their front-end scripts are served from, the provider
	 * whose consent they wait for, and the category to wait for when the banner does not list it.
	 *
	 * @return array
	 */
	public function get_players() {
		$players = array(
			// Video Player for YouTube. The scanner classes YouTube's cookies as advertisement.
			'yt-player' => array(
				'path'      => '/plugins/yt-player/',
				'provider'  => 'youtube',
				'category'  => 'advertisement',
				'container' => '.wp-block-yt-player-video[data-attributes]',
			),
			// Vimeify: its click-to-play script builds the Vimeo iframe. The scanner classes Vimeo's
			// cookies as analytics. The preview box is 16:9 by padding with everything inside
			// positioned absolutely, so the placeholder goes on top.
			'vimeify'   => array(
				'path'      => '/plugins/vimeify/public/frontend/video',
				'provider'  => 'vimeo',
				'category'  => 'analytics',
				'container' => '.vimeify-embed-modern',
				'overlay'   => true,
			),
		);
		/**
		 * Filter the player plugins whose scripts wait for consent.
		 *
		 * @param array $players slug => array( path, provider, category, container, overlay ).
		 *                       `category` is the fallback when the banner has no rule for the
		 *                       provider (optional, advertisement by default); `container` is the CSS
		 *                       selector of the element the player is built in, which shows the consent
		 *                       placeholder until then (optional); `overlay` places it over the
		 *                       container's content instead of after it (optional).
		 */
		return (array) apply_filters( 'cky_video_player_scripts', $players );
	}

	/**
	 * Print a player plugin's script tag inert until consent, with the inline script WordPress
	 * prints after it, which may use the player's globals.
	 *
	 * @param string $tag    The script tag.
	 * @param string $handle Script handle.
	 * @param string $src    Script URL.
	 * @return string
	 */
	public function mark_player_script( $tag, $handle, $src ) {
		if ( ! is_string( $tag ) || ! is_string( $src ) || '' === $src || false !== strpos( $tag, 'data-cky-player=' ) ) {
			return $tag;
		}
		foreach ( $this->get_players() as $slug => $player ) {
			if ( empty( $player['path'] ) || false === strpos( $src, $player['path'] ) ) {
				continue;
			}
			$category = $this->player_category( $slug, $player );
			// Hold nothing the banner could never release: without its script on the page there is no
			// consent to wait for, and a provider in no consent category needs none.
			if ( '' === $category || ! $this->banner_loads() ) {
				return $tag;
			}
			$held = preg_replace_callback(
				'/<script\b([^>]*)\ssrc=(["\'])(.*?)\2([^>]*)>/i',
				function ( $m ) use ( $slug ) {
					$attributes = $m[1] . $m[4];
					$deferred   = (bool) preg_match( '/\s(defer|async)\b/i', $attributes );
					$module     = (bool) preg_match( '/\stype=(["\'])module\1/i', $attributes );
					return '<script type="text/plain" data-cky-player="' . esc_attr( $slug ) . '"'
						. ( $deferred || $module ? ' data-cky-defer="1"' : '' )
						. ( $module ? ' data-cky-module="1"' : '' )
						. ' data-cky-player-src="' . esc_attr( html_entity_decode( $m[3], ENT_QUOTES ) ) . '"' . $this->strip_run_attributes( $attributes ) . '>';
				},
				$tag,
				1,
				$count
			);
			if ( 0 === $count || ! is_string( $held ) ) {
				return $tag;
			}
			// The "after" inline script runs once the player script has loaded; "before" and the
			// localized data only set values, so they still run straight away.
			$after = preg_replace_callback(
				'/<script\b([^>]*\sid=(["\'])' . preg_quote( $handle . '-js-after', '/' ) . '\2[^>]*)>/i',
				function ( $m ) use ( $slug ) {
					return '<script type="text/plain" data-cky-player-after="' . esc_attr( $slug ) . '"' . $this->strip_run_attributes( $m[1] ) . '>';
				},
				$held
			);
			$url   = wp_parse_url( html_entity_decode( $src, ENT_QUOTES ) );
			if ( ! empty( $url['host'] ) ) {
				// Written as the browser's URL.host: lower case, without a default port.
				$port = empty( $url['port'] ) || in_array( (int) $url['port'], array( 80, 443 ), true ) ? '' : ':' . (int) $url['port'];
				$this->hosts[ strtolower( $url['host'] ) . $port ] = true;
			}
			$this->held[ $slug ] = $category;
			return is_string( $after ) ? $after : $held;
		}
		return $tag;
	}

	/**
	 * Drop the attributes that would make the browser run a script: its type, defer and async.
	 *
	 * @param string $attributes Attributes of a script tag.
	 * @return string
	 */
	private function strip_run_attributes( $attributes ) {
		return (string) preg_replace( '/\s(type=(["\'])[^"\']*\2|defer(=(["\'])[^"\']*\4)?|async(=(["\'])[^"\']*\6)?)(?=[\s\/>]|$)/i', '', $attributes );
	}

	/**
	 * Print the loader that releases held player scripts once their categories are accepted.
	 *
	 * @return void
	 */
	public function print_loader() {
		if ( empty( $this->held ) ) {
			return;
		}
		$players = $this->get_players();
		$config  = array();
		foreach ( $this->held as $slug => $category ) {
			$config[] = array(
				'slug'      => (string) $slug,
				'provider'  => isset( $players[ $slug ]['provider'] ) ? (string) $players[ $slug ]['provider'] : '',
				'category'  => $category,
				'container' => isset( $players[ $slug ]['container'] ) ? (string) $players[ $slug ]['container'] : '',
				'path'      => isset( $players[ $slug ]['path'] ) ? (string) $players[ $slug ]['path'] : '',
				'overlay'   => ! empty( $players[ $slug ]['overlay'] ),
			);
		}
		$fallback = __( 'Please accept cookies to access this content', 'cookie-law-info' );
		$script   = '(function(){'
			. 'var players=' . wp_json_encode( $config, JSON_HEX_TAG | JSON_HEX_AMP ) . ',hosts=' . wp_json_encode( array_keys( $this->hosts ), JSON_HEX_TAG | JSON_HEX_AMP ) . ',fallback=' . wp_json_encode( $fallback, JSON_HEX_TAG | JSON_HEX_AMP ) . ',busy=false,again=false,released={};'
			. 'function allowed(c){try{var s=window.getCkyConsent&&window.getCkyConsent();return !!(s&&s.categories&&s.categories[c]);}catch(e){return false;}}'
			. 'function player(slug){for(var i=0;i<players.length;i++){if(players[i].slug===slug){return players[i];}}return null;}'
			// The categories the banner itself blocks the provider under — from the web app's settings
			// on a connected site, the cookie list otherwise — so the player is released together with
			// the provider's embeds. The fallback category when the banner has no rule for it. Only
			// categories that exist and need consent count; with none left there is nothing to wait for.
			. 'function categories(p){var s=(window.cookieyes&&window.cookieyes._ckyStore)||window._ckyConfig;'
			. 'if(!s||!s._categories||!s._providersToBlock){return [p.category];}'
			. 'var list=[];s._providersToBlock.forEach(function(r){var re=r.url||r.re||"";if(p.provider&&re.indexOf(p.provider)!==-1){list=list.concat(r.categories||[]);}});'
			// No rule: the provider's cookies are all necessary (nothing to wait for), or not listed.
			. 'if(!list.length){list=s._categories.some(function(k){return k.isNecessary&&(k.cookies||[]).some(function(c){return p.provider&&((c.provider||"")+" "+(c.domain||"")).indexOf(p.provider)!==-1;});})?[]:[p.category];}'
			. 'return list.filter(function(c){return s._categories.some(function(k){return k.slug===c&&!k.isNecessary;});});}'
			. 'function ready(p){return !!p&&categories(p).every(allowed);}'
			// Runs on every consent change: each pass releases the players whose categories are now all
			// accepted, so a player accepted later is released too. One pass at a time.
			. 'function release(){'
			. 'if(busy){again=true;return;}'
			// Only scripts this module held: from the page's host or a host a held script came from, and
			// from the player's own folder. Inert markup injected elsewhere is never turned into a script.
			. 'function trusted(n){try{var p=player(n.getAttribute("data-cky-player")),u=new URL(n.getAttribute("data-cky-player-src"),location.href);'
			. 'return !!p&&/^https?:$/.test(u.protocol)&&(u.host===location.host||hosts.indexOf(u.host)!==-1)&&u.pathname.indexOf(p.path)!==-1;}catch(e){return false;}}'
			. 'var held=[].slice.call(document.querySelectorAll(\'script[type="text/plain"][data-cky-player]\')).filter(function(n){return trusted(n)&&ready(player(n.getAttribute("data-cky-player")));});'
			. 'if(!held.length){return;}'
			. 'busy=true;'
			. 'held.forEach(function(n){released[n.getAttribute("data-cky-player")]=true;});'
			. '[].forEach.call(document.querySelectorAll("[data-cky-player-placeholder]"),function(n){if(ready(player(n.getAttribute("data-cky-player-placeholder")))){n.parentNode.removeChild(n);}});'
			// Classic scripts before deferred ones, page order within each — the browser's own order.
			. 'var ordered=held.filter(function(n){return !n.hasAttribute("data-cky-defer");}).concat(held.filter(function(n){return n.hasAttribute("data-cky-defer");}));'
			// Player scripts start on DOMContentLoaded, which has already fired by the time consent is
			// given. While they load, collect the ready/load listeners they register and run them once
			// every held script has loaded — when the browser would have fired the events — instead of
			// re-firing the events for the whole page; restore the normal behaviour afterwards.
			. 'var queue=[],restore=function(){};'
			. 'if(document.readyState!=="loading"){'
			. 'var wrap=function(target,events){var own=Object.prototype.hasOwnProperty.call(target,"addEventListener"),orig=target.addEventListener;'
			. 'target.addEventListener=function(type,fn){if(events.indexOf(type)!==-1&&typeof fn==="function"){queue.push({target:target,type:type,fn:fn});return;}return orig.apply(this,arguments);};'
			. 'return function(){if(own){target.addEventListener=orig;}else{delete target.addEventListener;}};};'
			. 'var r1=wrap(document,["DOMContentLoaded"]),r2=wrap(window,["DOMContentLoaded","load"]);'
			. 'restore=function(){r1();r2();};'
			. '}'
			. 'function flush(){while(queue.length){var q=queue.splice(0,queue.length);'
			. 'q.filter(function(l){return l.type!=="load";}).concat(q.filter(function(l){return l.type==="load";})).forEach(function(l){'
			. 'try{l.fn.call(l.target,new Event(l.type));}catch(e){setTimeout(function(){throw e;},0);}});}}'
			. 'function copy(from,to){for(var k=0;k<from.attributes.length;k++){var a=from.attributes[k];if(a.name!=="type"&&a.name.indexOf("data-cky-")!==0){to.setAttribute(a.name,a.value);}}}'
			. '(function next(i){'
			. 'if(i>=ordered.length){setTimeout(function(){flush();restore();busy=false;if(again){again=false;release();}},0);return;}'
			. 'var old=ordered[i],s=document.createElement("script");'
			. 'copy(old,s);'
			. 'if(old.hasAttribute("data-cky-module")){s.type="module";}'
			// Then the script's own "after" inline script, printed right behind it.
			. 's.async=false;s.onload=s.onerror=function(){var a=s.nextElementSibling;'
            . 'if(a&&a.parentNode&&s.id&&a.id===s.id.replace(/-js$/,"-js-after")&&a.getAttribute("data-cky-player-after")===old.getAttribute("data-cky-player")){var t=document.createElement("script");copy(a,t);t.text=a.text;a.parentNode.replaceChild(t,a);}'
			. 'next(i+1);};'
			. 's.src=old.getAttribute("data-cky-player-src");'
			. 'old.parentNode.replaceChild(s,old);'
			. '})(0);'
			. '}'
			// Until then, an empty player container shows the CookieYes placeholder (the banner's own
			// text), full width at 16:9 so it follows the layout, and with no video thumbnail so nothing
			// is requested from the provider before consent.
			. 'function placeholders(){'
			// Same rules as the banner's own video placeholder: honour its on/off setting and styles,
			// and let a click reopen the consent preferences once the visitor has chosen.
			. 'var cfg={};try{cfg=window._ckyConfig._bannerConfig.config.videoPlaceholder||{};}catch(e){}'
			. 'if(cfg.status===false){return;}'
			. 'var tpl="";try{(window._ckyConfig&&window._ckyConfig._shortCodes||[]).forEach(function(c){if(c.key==="cky_video_placeholder"){tpl=c.content;}});}catch(e){}'
			. 'players.forEach(function(p,pi){if(!p.container||ready(p)){return;}'
			. '[].forEach.call(document.querySelectorAll(p.container),function(c,ci){'
			. 'if(c.querySelector("iframe,[data-cky-player-placeholder]")){return;}'
			. 'var ph=document.createElement("div");ph.setAttribute("data-cky-player-placeholder",p.slug);'
			. 'if(p.overlay){if(getComputedStyle(c).position==="static"){c.style.position="relative";}ph.style.cssText="position:absolute;top:0;right:0;bottom:0;left:0;z-index:2;";}'
			. 'else{ph.style.cssText="width:100%;aspect-ratio:16/9;";}'
			. 'if(tpl){ph.innerHTML=tpl.replace("[UNIQUEID]","cky-player-"+pi+"-"+ci);var inner=ph.firstElementChild;if(inner){inner.style.width="100%";inner.style.height="100%";}}'
			. 'else{ph.textContent=fallback;}'
			. '[].forEach.call(ph.querySelectorAll(\'[data-cky-tag="placeholder-title"]\'),function(t){t.style.display="block";'
			. 'var st=cfg.styles||{};for(var k in st){if(st[k]){t.style[k]=st[k];}}'
			. 't.addEventListener("click",function(){if(window.revisitCkyConsent){window.revisitCkyConsent();}});});'
			. 'c.appendChild(ph);});});'
			. '}'
			. 'function start(){placeholders();release();}'
			// Consent withdrawn for a player that already runs: a running player cannot be stopped in
			// place, so reload once — the page then loads with it held again.
			. 'function changed(){for(var slug in released){if(released.hasOwnProperty(slug)&&!ready(player(slug))){window.location.reload();return;}}release();}'
			. 'document.addEventListener("cookieyes_consent_update",changed);'
			. 'if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",start);}else{start();}'
			. '})();';
		if ( function_exists( 'wp_print_inline_script_tag' ) ) {
			wp_print_inline_script_tag( $script, array( 'id' => 'cky-video-players-js' ) );
			return;
		}
		echo '<script id="cky-video-players-js">' . $script . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static script, no user data.
	}

	/**
	 * Category a player waits for, as far as the server knows. Standalone, it is a non-necessary
	 * category the provider's cookies are in (the banner blocks the provider under those), else the
	 * player's fallback; an empty string when the provider's cookies are all necessary or the
	 * fallback does not exist, as there is nothing to wait for or nothing could release the player.
	 * The loader waits for every category the banner lists, not only this one. Connected, the
	 * cookie list lives in the web app, so this is the fallback and the loader reads the real
	 * categories from the banner on the page.
	 *
	 * @param string $slug   Player slug.
	 * @param array  $player Player settings.
	 * @return string Category slug, or '' when the player should not be held.
	 */
	private function player_category( $slug, $player ) {
		if ( isset( $this->categories[ $slug ] ) ) {
			return $this->categories[ $slug ];
		}
		$provider = isset( $player['provider'] ) ? (string) $player['provider'] : '';
		$fallback = empty( $player['category'] ) ? self::DEFAULT_CATEGORY : (string) $player['category'];
		if ( $this->is_connected() || ! class_exists( Category_Controller::class ) || ! class_exists( Cookie_Controller::class ) ) {
			$this->categories[ $slug ] = $fallback;
			return $fallback;
		}
		$matched   = '';
		$necessary = false;
		$exists    = false;
		foreach ( Category_Controller::get_instance()->get_items() as $item ) {
			$item_category = new Cookie_Categories( $item );
			$item_slug     = $item_category->get_slug();
			if ( $item_slug === $fallback ) {
				$exists = true;
			}
			if ( '' === $provider ) {
				continue;
			}
			foreach ( Cookie_Controller::get_instance()->get_items_by_category( $item_category->get_id() ) as $cookie ) {
				$pattern = isset( $cookie->url_pattern ) ? (string) $cookie->url_pattern : '';
				if ( '' === $pattern || false === stripos( $pattern, $provider ) ) {
					continue;
				}
				// Like the banner, necessary cookies make no blocking rule: the provider is blocked
				// under any other category it has cookies in.
				if ( 'necessary' !== $item_slug ) {
					$matched = $item_slug;
					break 2;
				}
				$necessary = true;
				break;
			}
		}
		if ( '' !== $matched ) {
			$category = $matched;
		} elseif ( $necessary || ! $exists || 'necessary' === $fallback ) {
			$category = '';
		} else {
			$category = $fallback;
		}
		$this->categories[ $slug ] = $category;
		return $category;
	}

	/**
	 * Whether the site is connected to the CookieYes web app.
	 *
	 * @return boolean
	 */
	private function is_connected() {
		if ( null === $this->connected ) {
			$this->connected = class_exists( Settings::class ) && ( new Settings() )->is_connected();
		}
		return $this->connected;
	}

	/**
	 * Whether the CookieYes banner script is on this page: the plugin's script when standalone (only
	 * enqueued with an active banner), the CDN script when connected.
	 *
	 * @return boolean
	 */
	private function banner_loads() {
		if ( null === $this->banner_loads ) {
			$this->banner_loads = wp_script_is( 'cookie-law-info', 'enqueued' ) || $this->is_connected();
		}
		return $this->banner_loads;
	}
}
