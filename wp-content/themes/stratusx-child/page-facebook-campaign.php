<style>
	body { height: 100vh; }
	.wrap { height: calc( 100% - 181px); }
	.form-field .row { --bs-gutter-x: 16px; }
	.mobile-width { max-width: 420px; margin: 0 auto; padding: 0 12px; }
	.facebook-ads {  padding: 40px 0; }
	.facebook-ads h1 { font-size: 52px; text-align: center; } 
	.wpcf7-form .field-wrapper { margin-bottom: 20px; }
	.form-field .wpcf7-form-control { padding: 24px 20px; font-size: 18px; }
	.form-submit-btn .wpcf7-form-control.wpcf7-submit { padding: 20px 20px; font-size: 24px; font-weight: 900; }
	.wpcf7-form-control[type=submit] { text-transform:  uppercase; }
</style>
<div class="facebook-ads">
	<h1> <?= get_the_title(); ?> </h1>		
	<div class="mobile-width">
		<?= the_content(); ?>
	</div>
</div>
