<?php
/**
 * WhatsApp chat: sets $ppc_wa (link) and $ppc_wa_icon for the mobile bar, and prints the desktop floating button.
 */

defined('ABSPATH') || exit;

$ppc_wa      = 'https://wa.me/919714874435?text=' . rawurlencode("Hello Healthray, I'd like a free demo of your hospital management software for my hospital. Please share a suitable time.");
$ppc_wa_icon = '<svg class="wa-ico" viewBox="0 0 32 32" aria-hidden="true"><path fill="currentColor" d="M16 3C8.8 3 3 8.7 3 15.8c0 2.5.7 4.9 2 7L3 29l6.4-2c2 1.1 4.3 1.7 6.6 1.7 7.2 0 13-5.7 13-12.8S23.2 3 16 3zm0 23.4c-2.1 0-4.2-.6-6-1.7l-.4-.3-3.8 1.2 1.2-3.7-.3-.4c-1.2-1.8-1.9-3.9-1.9-6.1C4.8 9.7 9.8 4.8 16 4.8s11.2 4.9 11.2 11c0 6-5 10.6-11.2 10.6zm6.1-7.9c-.3-.2-2-1-2.3-1.1-.3-.1-.5-.2-.8.2-.2.3-.9 1.1-1.1 1.3-.2.2-.4.3-.7.1-.3-.2-1.4-.5-2.7-1.7-1-.9-1.7-2-1.9-2.3-.2-.3 0-.5.1-.7l.5-.6c.2-.2.2-.4.3-.6.1-.2 0-.5 0-.6l-1-2.5c-.3-.7-.6-.6-.8-.6h-.7c-.2 0-.6.1-.9.4-.3.3-1.2 1.1-1.2 2.8s1.2 3.3 1.4 3.5c.2.2 2.4 3.7 5.9 5.1 2.9 1.1 3.5.9 4.1.8.6-.1 2-.8 2.3-1.6.3-.8.3-1.5.2-1.6-.1-.1-.4-.2-.7-.4z"/></svg>';
?>
<a class="wa-float js-wa" href="<?php echo esc_url($ppc_wa); ?>" target="_blank" rel="noopener"><?php echo $ppc_wa_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Chat on WhatsApp</a>
