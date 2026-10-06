<?php

function register_gdpr_field(){
	register_setting( 'handl-utm-grabber-settings-group', 'show_gdpr_notice' );
}
add_action( 'admin_init', 'register_gdpr_field' );

function add_gdpr_field(){
    global $handl_fields_disabled;
	?>
	<tr>
		<th scope='row'>Enable GDPR</th>
		<td>
			<fieldset>
				<legend class='screen-reader-text'>
					<span>Enable GDPR</span>
				</legend>
				<label for='show_gdpr_notice'>
					<input name='show_gdpr_notice' id='show_gdpr_notice' type='checkbox' value='1' <?php print checked( '1', get_option( 'show_gdpr_notice' ) ) ?> <?php print $handl_fields_disabled;?> />
					Check if you'd like to be complaint with EU's GDPR.
                    <span class="description">This will display HandL's consent bar in your website. Our plugin won't collect any data until consent is given. If you are using other consent plugins, you do not need to enable this. </span>
				</label>
			</fieldset>
		</td>
	</tr>
	<?php
}
add_filter("insert_rows_to_handl_options", "add_gdpr_field", 10);

function gdpr_consented( $good2go ) {
	if (get_option( 'show_gdpr_notice') == '1' && $good2go['good2go'] === 1){
	    if ( isset($_COOKIE['gdprConsent']) ){
            $good2go['good2go'] = (int)$_COOKIE['gdprConsent'];
	    }else{
            $good2go['good2go'] = 0;
	    }
	}
    return $good2go;
}
add_filter( 'is_ok_to_capture_utms', 'gdpr_consented', 10, 1 );

function handl_pro_add_gdpr_notice() {
	if (get_option( 'show_gdpr_notice') == '1' && !defined("SHOW_CT_BUILDER")) {
		echo '
	    <style>
	    .modal {
	        display: none; /* Hidden by default */
	        position: fixed; /* Stay in place */
	        z-index: 1; /* Sit on top */
	        left: 0;
	        top: 0;
	        width: 100%; /* Full width */
	        height: 100%; /* Full height */
	        overflow: auto; /* Enable scroll if needed */
	//        background-color: rgb(0,0,0); /* Fallback color */
	//        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
	    }
	    
	    /* Modal Content/Box */
	    .modal-content {
	        position: fixed;
	        bottom: 0;
	        background-color: #fefefe;
	        padding: 0.5em;
	        border: 1px solid #888;
	        width: 100%;
	        line-height: 1.2em;
	        font-size: 85%;
	    }
	    
	    a.handl-gppr-btn {
	        background-color: gray;
	        border: none;
	        color: white;
	        padding: 8px 12px;
	        text-align: center;
	        text-decoration: none;
	        display: inline-block;
	        font-size: 16px;
	    }
	    
	    a.handl-gppr-btn.bg-green{
	        background-color: #4CAF50;
	    }
	    
	    /* The Close Button */
	    .close {
	        color: #aaa;
	        float: right;
	        font-size: 28px;
	        font-weight: bold;
	    }
	    
	    .close:hover,
	    .close:focus {
	        color: black;
	        text-decoration: none;
	        cursor: pointer;
	    }
	    </style>
	    
	    <div id="myModal" class="modal">
	        <!-- Modal content -->
	        <div class="modal-content">
	            <p>We use a handful of cookies to help with analysis and improve your site experience. We don\'t resell your data ever but you can opt-out if you wish. Learn more by visiting our Privacy Policy.</p>
	            <a href="#" class="handl-gppr-btn" data-gdpr-answer="deny">DENY</a>
	            <a href="#" class="handl-gppr-btn bg-green" data-gdpr-answer="accept">ACCEPT</a>
	        </div>
	    </div>
	    
	    <script>
	        // Get the modal
	        var modal = document.getElementById("myModal");
	        
	        // Get the <span> element that closes the modal
	        var span = document.getElementsByClassName("close")[0];
	        
	        jQuery( document ).ready(function($) {
	            $(".handl-gppr-btn").click(function(event){
	                event.preventDefault()
	                if ($(this).data("gdprAnswer") === "accept"){
	                    Cookies.set("gdprConsent", 1);
	                }else{
	                    Cookies.set("gdprConsent", 0);
	                }
	                modal.style.display = "none";
	            });
	        });
	        
	        if ( Cookies.get("gdprConsent") === undefined ){
	            modal.style.display = "block";
	        }
	        
	    </script>
	    ';
	}
}
add_action( 'wp_footer', 'handl_pro_add_gdpr_notice' );