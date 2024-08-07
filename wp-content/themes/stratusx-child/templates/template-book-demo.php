<?php
/*
Template Name: Book a Demo
*/
?>
<html>
	<head>

	</head>
	<body>

		<style>
			.book-a-demo .row {display: flex;}

			.book-a-demo .left-container{ display: flex; align-items: center; height:100vh; background-color:#F0F1FA; }
		</style>

		<div class="book-a-demo">
			<div class="row">
				<div class="col-md-7 d-flex left-container vh-100 align-items-center">
					<div class="logo">
						<?php the_custom_logo(); ?>
					</div>
					<div class="signup-ctn text-center">
						<h2>Get a Personalized Demo of Healthray</h2>
						<p>We'll cover everything you need to start using Healthray, provide resources for advanced training, and answer your questions.</p>
					</div>
				</div>
				<div class="col-md-5 d-flex right-container vh-100 align-items-center justify-content-center">
					<div class="signup-details w-100 book-demo">
						<div class="signin-blocks m-auto" id="book-demo-form">
							<div class="title">
								<h1	>Get a Demo</h1>
							</div> 

							<form action="javascript:void(0);" id="demo_bookings_form" onsubmit="return validateBookDemo();" class="d-none" >
								<div class="form-group">
									<input type="email" class="form-control" id="demo_email" name="email" aria-describedby="inputGroupPrepend" placeholder="Email address" required="" value="">
									<input type="text" class="form-control" id="demo_name" name="name" aria-describedby="inputGroupPrepend" placeholder="Your Name" required="" value="">
									<input type="hidden" name="fname" id="demo_fname">
									<input type="hidden" name="lname" id="demo_lname">
									<textarea class="form-control" id="demo_message" name="message" placeholder="Anything else that you'd like us to know" rows="2"></textarea>
									<button type="submit" class="btn btn-primary btn-block">Book a Demo</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>

			<script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js" async> </script>
			<script>
				jQuery('#demo_bookings_form').hide(); 
				Calendly.initInlineWidget({
					url: 'https://calendly.com/healthray/30min?hide_landing_page_details=1&hide_gdpr_banner=1',
					parentElement: document.querySelector('.calendly-inline-widget'),
				});

			</script>
		</div>

	</body>
</html>
