<?php
/**
 * Retune the copy on the "Contact Page CTA" form (CF7 post 25006).
 *
 * The form was cloned from the demo-request forms, so its button read
 * "Request my demo". On the rebuilt Contact page the same form also carries
 * support, tender and partnership enquiries, where that label is wrong -
 * someone reporting a billing outage should not be asked to request a demo.
 *
 * Only the submit label and the note below it change. Field names, tags,
 * mail templates and any CF7 Redirection actions are untouched, so nothing
 * downstream needs updating.
 *
 * Usage (from this directory):
 *   php update-form-labels.php
 *   php update-form-labels.php --apply
 *   php update-form-labels.php --restore=backups/cf7-25006-<stamp>.txt
 *
 * @package stratusx-child
 */

if ( 'cli' !== PHP_SAPI ) {
	exit( 'CLI only.' );
}

define( 'WP_USE_THEMES', false );
require_once dirname( __DIR__, 5 ) . '/wp-load.php';

$args    = getopt( '', array( 'apply', 'restore::', 'id::' ) );
$apply   = isset( $args['apply'] );
$restore = isset( $args['restore'] ) ? (string) $args['restore'] : '';
$form_id = isset( $args['id'] ) ? (int) $args['id'] : 25006;
$dir     = __DIR__ . '/backups';

if ( ! is_dir( $dir ) ) {
	mkdir( $dir, 0755, true );
}

if ( '' !== $restore ) {
	$path = ( 0 === strpos( $restore, '/' ) || preg_match( '#^[A-Za-z]:#', $restore ) ) ? $restore : __DIR__ . '/' . $restore;

	if ( ! file_exists( $path ) ) {
		fwrite( STDERR, "Backup not found: {$path}\n" );
		exit( 1 );
	}

	update_post_meta( $form_id, '_form', file_get_contents( $path ) );
	echo "Restored form {$form_id} from {$path}\n";
	exit( 0 );
}

$form = get_post_meta( $form_id, '_form', true );

if ( ! $form ) {
	fwrite( STDERR, "Form {$form_id} has no _form meta.\n" );
	exit( 1 );
}

$replacements = array(
	'[submit "Request my demo"]'                            => '[submit "Send my enquiry"]',
	'No spam. Your details are used only to arrange the demo.' => 'No spam. Your details are used only to reply to this enquiry.',
);

$updated = $form;
$applied = array();

foreach ( $replacements as $from => $to ) {
	if ( false !== strpos( $updated, $from ) ) {
		$updated  = str_replace( $from, $to, $updated );
		$applied[] = $from . "\n      -> " . $to;
	}
}

echo ( $apply ? '=== APPLY ===' : '=== DRY RUN (add --apply to write) ===' ) . "\n";
echo 'Form ' . $form_id . ': ' . get_the_title( $form_id ) . "\n\n";

if ( empty( $applied ) ) {
	echo "Nothing to change - the copy is already current.\n";
	exit( 0 );
}

foreach ( $applied as $line ) {
	echo '  ' . $line . "\n";
}

if ( ! $apply ) {
	exit( 0 );
}

$backup = $dir . '/cf7-' . $form_id . '-' . gmdate( 'Ymd-His' ) . '.txt';
file_put_contents( $backup, $form );

update_post_meta( $form_id, '_form', $updated );

echo "\nApplied. Previous version saved to:\n  {$backup}\n";
