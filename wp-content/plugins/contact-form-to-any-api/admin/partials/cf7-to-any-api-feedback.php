
<div class="cf7-any-api-modal" style="display:none">
	<div class="cf7-any-api-modal-dialog">
		<form action="#" method="post" name="cf7-any-api-data">
			<div class="cf7-any-api-modal-header">
				<h4><?php esc_html_e( 'Quick Feedback', 'contact-form-to-any-api' ); ?></h4>
			</div>
			<div class="cf7-any-api-modal-body">
				<h3><strong><?php esc_html_e( 'If you have a moment, please let us know why you are deactivating:', 'contact-form-to-any-api' ); ?></strong></h3>
				<ul id="cf7-any-api-list">
					<li class="reason">
			            <label>
			                <input type="radio" name="selected-reason" value="I found a better plugin.">
			                <span for="rad"><?php esc_html_e( 'I found a better plugin', 'contact-form-to-any-api' ); ?></span>
			            </label>
			       	</li>
			       	<li class="reason">
			            <label>
			                <input type="radio" name="selected-reason" value="This plugin does not work on my site.">
			                <span><?php esc_html_e( 'This plugin does not work on my site', 'contact-form-to-any-api' ); ?></span>
			            </label>
			        </li>
			        <li class="reason">
			            <label>
			                <input type="radio" name="selected-reason" value="It's just temporary. I will be back soon.">
			                <span><?php esc_html_e( "It's just temporary. I will be back soon", 'contact-form-to-any-api' ); ?></span>
			            </label>
			        </li>
			        <li class="reason">
			            <label>
			                <input type="radio" name="selected-reason" value="I am finding it difficult to configure it as per my needs">
			                <span><?php esc_html_e( 'I am finding it difficult to configure it as per my needs', 'contact-form-to-any-api' ); ?></span>
			            </label>
			        </li>
			        <li class="reason">
			            <label>
			                <input type="radio" name="selected-reason" value="Design is outdated, difficult to navigate.">
			                <span><?php esc_html_e( 'Design is outdated, difficult to navigate', 'contact-form-to-any-api' ); ?></span>
			            </label>
			        </li>
			        <li class="reason">
			            <label>
			                <input type="radio" name="selected-reason" value="It is not what I am looking for.">
			                <span><?php esc_html_e( 'It is not what I am looking for', 'contact-form-to-any-api' ); ?></span>
			            </label>
			        </li>
			        <li class="reason">
			            <label>
			                <input type="radio" name="selected-reason" value="__other_option__">
			                <span><?php esc_html_e( 'Other', 'contact-form-to-any-api' ); ?></span>
			                <textarea name="comment"></textarea>
			            </label>
			        </li>
			        <li class="reason email">
			            <label>
			                <span><?php esc_html_e( 'Email Address', 'contact-form-to-any-api' ); ?></span>
			                <input type="email" name="selected-email" value="" placeholder="<?php esc_attr_e( 'Enter Email Address', 'contact-form-to-any-api' ); ?>">
			            </label>
			        </li>
			    </ul>
			</div>
			<div class="cf7-any-api-modal-footer">
				<input type="submit" class="button button-secondary" name="submit" value="<?php esc_attr_e( 'Submit &amp; Deactivate', 'contact-form-to-any-api' ); ?>">
				<?php wp_nonce_field('cf7_any_api_action', 'cf7_any_api_nonce_field');?>
				<a href="jQuery:;" class="button button-secondary button-close"><?php esc_html_e( 'Cancel', 'contact-form-to-any-api' ); ?></a>
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
	jQuery('tr.active[data-slug="contact-form-to-any-api"] .deactivate a').click(function(e){
		e.preventDefault(); 	
		
  		jQuery('.cf7-any-api-modal').show();
  		jQuery('.cf7-any-api-modal').addClass('active');

		var cf7apisubmit = jQuery(this).attr('href');

		jQuery('.cf7-any-api-modal form').attr('action', cf7apisubmit);

	});
	jQuery('.cf7-any-api-modal input[type=radio]').change(function() {
		if (this.value == '__other_option__') {
			jQuery('.cf7-any-api-modal input[type="radio"]:checked').siblings('textarea').attr('required', 'required');
		}else{
			jQuery('.cf7-any-api-modal .reason textarea').removeAttr("required");
		}
	});
	jQuery('.cf7-any-api-modal .button-close').click(function(e){
		jQuery('.cf7-any-api-modal').hide();
	});	
	jQuery('body').click(function (e) {
		if(e.target === jQuery('.cf7-any-api-modal form')['0']) {
		    jQuery('.cf7-any-api-modal').hide();
		}     
	});
</script>