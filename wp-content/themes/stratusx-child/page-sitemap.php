<style>
	.container {margin: 0 auto; max-width: 1170px;}
	.sitemap-menu {margin: 30px 0;}
	.sitemap-menu .col {display: flex;flex-direction: column;margin-top: 20px;}
	.sitemap-menu .menu-item {background-color: #d8eeff;margin-top: 24px;box-shadow: 0 0 5px 0 #00000010;border: 1px solid var(--hr-primary-color);}
	.sitemap-menu .menu-item h2 { font-size: 24px; margin: 0; font-weight: 800; padding: 10px; color: #fff; background-color: var(--hr-primary-color);  }
	.sitemap-menu .menu-item .sub-menu { --column: 1; padding: 0; margin: 0; display: flex; flex-wrap: wrap; }
	.sitemap-menu .menu-item .sub-menu li { width: calc(100% / var(--column)); color: #1d1c39; list-style: none; margin: 0;border: 1px solid var(--bs-info-bg-subtle); padding: 6px; }
	.sitemap-menu .menu-item a:hover { text-decoration: underline; }
	@media screen and (min-width: 575px) {
		.sitemap-menu .menu-item .sub-menu { --column: 2; }
	} 
	@media screen and (min-width: 991px) {
		.sitemap-menu .menu-item .sub-menu { --column: 3; }
	}
</style>
<?php global $post; ?>
<section class="blog-hero hero">
	<div class="container">
		<div class="page-title">
			<h1 class='entry-title header-default'> Healthray Sitemap</h1>
		</div>
		<?php
		$categories = get_categories();
		if ($categories) {
		?>
		<div class="main-sidebar">
			<?php get_template_part('templates/category-list'); ?>
		</div>
		<?php } ?>
		<div class="sidebar-form">
			<?php echo get_search_form(); ?>
		</div>

	</div>
</section>

<div class="sitemap-menu">
	<!-- Company -->
	<div class="container">
		<div class="menu-item sub-menu-item">
			<div>
				<h2>Company</h2>
				<ul class="sub-menu">
					<li class="menu-link"><a href="https://healthray.com/" target="_blank"> Home </a></li>
					<li class="menu-link"><a href="https://healthray.com/blogs/" target="_blank"> Blogs </a></li>
					<li class="menu-link"><a href="https://healthray.com/why-healthray/" target="_blank"> Why Healthray </a></li>
					<li class="menu-link"><a href="https://healthray.com/careers/" target="_blank"> Careers </a></li>
					<li class="menu-link"><a href="https://healthray.com/jobs/" target="_blank"> Jobs </a></li>
					<li class="menu-link"><a href="https://healthray.com/contact/" target="_blank"> Contact </a></li>
				</ul> 
			</div>
		</div>
	</div>
	<!-- HEalthray Products -->
	<div class="container">
		<div class="menu-item sub-menu-item">
			<div>
				<h2>Healthray Products</h2>
				<ul class="sub-menu">
					<li class="menu-link"><a href="https://healthray.com/hospital-information-management-system/" target="_blank"> Hospital Information Management System </a></li>
					<li class="menu-link"><a href="https://healthray.com/emr-software/" target="_blank"> EMR Software </a></li>
					<li class="menu-link"><a href="https://healthray.com/ehr-software/" target="_blank"> EHR Software </a></li>
					<li class="menu-link"><a href="https://healthray.com/pharmacy-management-system/" target="_blank"> Pharmacy Management System </a></li>
					<li class="menu-link"><a href="https://healthray.com/laboratory-information-management-system/" target="_blank"> Laboratory Information Management System </a></li>
					<li class="menu-link"><a href="https://healthray.com/emr-ehr-software/" target="_blank"> EMR/EHR Software </a></li>
				</ul>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="menu-item sub-menu-item">
			<div>
				<h2>Speciality</h2>
				<ul class="sub-menu">
					<li class="menu-link"><a href="https://healthray.com/gastroenterologists/" target="_blank"> Best EMR Software Gastroenterologists </a></li>
					<li class="menu-link"><a href="https://healthray.com/dermatologists/" target="_blank"> Best EMR Software Dermatologists </a></li>
					<li class="menu-link"><a href="https://healthray.com/nephrologists/" target="_blank"> Best EMR Software Nephrologists </a></li>
					<li class="menu-link"><a href="https://healthray.com/orthopedics/" target="_blank"> Best EMR Software Orthopedics </a></li>
					<li class="menu-link"><a href="https://healthray.com/consultant-physicians/" target="_blank"> Best EMR Software Consultant Physicians </a></li>
					<li class="menu-link"><a href="https://healthray.com/pulmonologist/" target="_blank"> Best EMR Software Pulmonologist </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-diabetologist/" target="_blank"> Best EMR Software Diabetologist </a></li>
					<li class="menu-link"><a href="https://healthray.com/gynecologist/" target="_blank"> Best EMR Software Gynecologist </a></li>
				</ul>
			</div>
		</div>
	</div>
	<!-- ABHA -->
	<div class="container">
		<div class="menu-item sub-menu-item">
			<div>
				<h2>ABHA </h2>
				<ul class="sub-menu">
					<li class="menu-link"><a href="https://healthray.com/abha/" target="_blank"> ABHA </a></li>
					<li class="menu-link"><a href="https://healthray.com/abdm/" target="_blank"> ABDM </a></li>
					<li class="menu-link"><a href="https://healthray.com/dhis/" target="_blank"> DHIS </a></li>
					<li class="menu-link"><a href="https://healthray.com/pmjay/" target="_blank"> PMJAY </a></li>
				</ul>    
			</div>
		</div>
	</div>
	<!-- City -->
	<div class="container">
		<div class="menu-item sub-menu-item">
			<div>
				<h2>Best HIMS Software For India</h2>
				<ul class="sub-menu">
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-india-hospital/" target="_blank"> Best HIMS Software For India </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-chandigarh-hospital/" target="_blank"> Best HIMS Software For Chandigarh Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-bhubaneswar-hospital/" target="_blank"> Best HIMS Software For Bhubaneswar Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-ahmedabad-hospital/" target="_blank"> Best HIMS Software For Ahmedabad Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-coimbatore-hospital/" target="_blank"> Best HIMS Software For Coimbatore Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-gurugram-hospital/" target="_blank"> Best HIMS Software For Gurugram Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-hyderabad-hospital/" target="_blank"> Best HIMS Software For Hyderabad Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-jaipur-hospital/" target="_blank"> Best HIMS Software For Jaipur Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-kolkata-hospital/" target="_blank"> Best HIMS Software For Kolkata Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-lucknow-hospital/" target="_blank"> Best HIMS Software For Lucknow Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-ludhiana-hospital/" target="_blank"> Best HIMS Software For Ludhiana Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-bangalore-hospital/" target="_blank"> Best HIMS Software For Bangalore Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-visakhapatnam-hospital/" target="_blank"> Best HIMS Software For Visakhapatnam Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-trivandrum-hospital/" target="_blank"> Best HIMS Software For Trivandrum Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-agra-hospital/" target="_blank"> Best HIMS Software For Agra Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-indore-hospital/" target="_blank"> Best HIMS Software For Indore Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-nashik-hospital/" target="_blank"> Best HIMS Software For Nashik Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-mumbai-hospital/" target="_blank"> Best HIMS Software For Mumbai Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-noida-hospital/" target="_blank"> Best HIMS Software For Noida Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-nagpur-hospital/" target="_blank"> Best HIMS Software For Nagpur Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-kerala-hospital/" target="_blank"> Best HIMS Software For Kerala Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-pune-hospital/" target="_blank"> Best HIMS Software For Pune Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-surat-hospital/" target="_blank"> Best HIMS Software For Surat Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-chennai-hospital/" target="_blank"> Best HIMS Software For Chennai Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-delhi-hospital/" target="_blank"> Best HIMS Software For Delhi Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-gandhinagar-hospital/" target="_blank"> Best HIMS Software For Gandhinagar Hospital </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hims-software-vadodara-hospital/" target="_blank"> Best HIMS Software For Vadodara Hospital </a></li>
				</ul>
			</div>
		</div>
	</div>

	<!-- City -->
	<div class="container">
		<div class="menu-item sub-menu-item">
			<div>
				<h2>Best Hospital Management Software In India</h2>
				<ul class="sub-menu">
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-india/" target="_blank">Best Hospital Management Software In India</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-andaman-nicobar-islands/" target="_blank">Best Hospital Management Software In Andaman and Nicobar Islands</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-andhra-pradesh/" target="_blank">Best Hospital Management Software In Andhra Pradesh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-arunachal-pradesh/" target="_blank">Best Hospital Management Software In Arunachal Pradesh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-assam/" target="_blank">Best Hospital Management Software In Assam</a></li> 					<li
					class="menu-link"><a href="https://healthray.com/best-hospital-management-software-bihar/" target="_blank">Best Hospital Management Software In Bihar</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-chandigarh/" target="_blank">Best Hospital Management Software In Chandigarh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-chhattisgarh/" target="_blank">Best Hospital Management Software In Chhattisgarh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-dadra-nagar-haveli-daman-diu/" target="_blank">Best Hospital Management Software In Dadra &amp; Nagar Haveli &amp; Daman &amp; Diu</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-delhi/" target="_blank">Best Hospital Management Software In Delhi</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-goa/" target="_blank">Best Hospital Management Software In Goa</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-gujarat/" target="_blank">Best Hospital Management Software In Gujarat</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-haryana/" target="_blank">Best Hospital Management Software In Haryana</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-himachal-pradesh/" target="_blank">Best Hospital Management Software In Himachal Pradesh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-jammu-kashmir/" target="_blank">Best Hospital Management Software In Jammu and Kashmir</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-jharkhand/" target="_blank">Best Hospital Management Software In Jharkhand</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-karnataka/" target="_blank">Best Hospital Management Software In Karnataka</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-kerala/" target="_blank">Best Hospital Management Software In Kerala</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-ladakh/" target="_blank">Best Hospital Management Software In Ladakh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-lakshadweep/" target="_blank">Best Hospital Management Software In Lakshadweep</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-madhya-pradesh/" target="_blank">Best Hospital Management Software In Madhya Pradesh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-maharashtra/" target="_blank">Best Hospital Management Software In Maharashtra</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-manipur/" target="_blank">Best Hospital Management Software In Manipur</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-meghalaya/" target="_blank">Best Hospital Management Software In Meghalaya</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-mizoram/" target="_blank">Best Hospital Management Software In Mizoram</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-nagaland/" target="_blank">Best Hospital Management Software In Nagaland</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-odisha/" target="_blank">Best Hospital Management Software In Odisha</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-puducherry/" target="_blank">Best Hospital Management Software In Puducherry</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-punjab/" target="_blank">Best Hospital Management Software In Punjab</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-rajasthan/" target="_blank">Best Hospital Management Software In Rajasthan</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-sikkim/" target="_blank">Best Hospital Management Software In Sikkim</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-tamil-nadu/" target="_blank">Best Hospital Management Software In Tamil Nadu</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-telangana/" target="_blank">Best Hospital Management Software In Telangana</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-tripura/" target="_blank">Best Hospital Management Software In Tripura</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-uttar-pradesh/" target="_blank">Best Hospital Management Software In Uttar Pradesh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-uttarakhand/" target="_blank">Best Hospital Management Software In Uttarakhand</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-hospital-management-software-west-bengal/" target="_blank">Best Hospital Management Software In West Bengal</a> </li>
				</ul>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="menu-item sub-menu-item">
			<div>
				<h2>Best Lab Software In India</h2>
				<ul class="sub-menu">
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-india/" target="_blank"> Best Lab Software In India </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-andaman-nicobar-islands/" target="_blank"> Best Lab Software In Andaman And Nicobar Islands </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-andhra-pradesh/" target="_blank"> Best Lab Software In Andhra Pradesh </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-arunachal-pradesh/" target="_blank"> Best Lab Software In Arunachal Pradesh </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-assam/" target="_blank"> Best Lab Software In Assam </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-bihar/" target="_blank"> Best Lab Software In Bihar </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-chandigarh/" target="_blank"> Best Lab Software In Chandigarh </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-chhattisgarh/" target="_blank"> Best Lab Software In Chhattisgarh </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-dadra-nagar-haveli-daman-diu/" target="_blank"> Best Lab Software In Dadra And Nagar Haveli And Daman And Diu </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-delhi/" target="_blank"> Best Lab Software In Delhi </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-goa/" target="_blank"> Best Lab Software In Goa </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-gujarat/" target="_blank"> Best Lab Software In Gujarat </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-haryana/" target="_blank"> Best Lab Software In Haryana </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-himachal-pradesh/" target="_blank"> Best Lab Software In Himachal Pradesh </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-jammu-kashmir/" target="_blank"> Best Lab Software In Jammu And Kashmir </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-jharkhand/" target="_blank"> Best Lab Software In Jharkhand </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-karnataka/" target="_blank"> Best Lab Software In Karnataka </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-kerala/" target="_blank"> Best Lab Software In Kerala </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-ladakh/" target="_blank"> Best Lab Software In Ladakh </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-lakshadweep/" target="_blank"> Best Lab Software In Lakshadweep </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-madhya-pradesh/" target="_blank"> Best Lab Software In Madhya Pradesh </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-maharashtra/" target="_blank"> Best Lab Software In Maharashtra </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-manipur/" target="_blank"> Best Lab Software In Manipur </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-meghalaya/" target="_blank"> Best Lab Software In Meghalaya </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-mizoram/" target="_blank"> Best Lab Software In Mizoram </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-nagaland/" target="_blank"> Best Lab Software In Nagaland </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-odisha/" target="_blank"> Best Lab Software In Odisha </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-puducherry/" target="_blank"> Best Lab Software In Puducherry </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-punjab/" target="_blank"> Best Lab Software In Punjab </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-rajasthan/" target="_blank"> Best Lab Software In Rajasthan </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-sikkim/" target="_blank"> Best Lab Software In Sikkim </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-tamil-nadu/" target="_blank"> Best Lab Software In Tamil Nadu </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-telangana/" target="_blank"> Best Lab Software In Telangana </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-tripura/" target="_blank"> Best Lab Software In Tripura </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-uttar-pradesh/" target="_blank"> Best Lab Software In Uttar Pradesh </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-uttarakhand/" target="_blank"> Best Lab Software In Uttarakhand </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-lab-software-west-bengal/" target="_blank"> Best Lab Software In West Bengal </a></li>
				</ul>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="menu-item sub-menu-item">
			<div>
				<h2>Best EMR Software In India</h2>
				<ul class="sub-menu">
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-india/" target="_blank"> Best EMR Software In India </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-andaman-nicobar-islands/">Best EMR Software In Andaman and Nicobar Islands</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-andhra-pradesh/">Best EMR Software In Andhra Pradesh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-arunachal-pradesh/">Best EMR Software In Arunachal Pradesh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-assam/">Best EMR Software In Assam</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-bihar/">Best EMR Software In Bihar</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-chandigarh/">Best EMR Software In Chandigarh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-chhattisgarh/">Best EMR Software In Chhattisgarh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-dadra-nagar-haveli-daman-diu/">Best EMR Software In Dadra and Nagar Haveli and Daman and Diu</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-delhi/">Best EMR Software In Delhi</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-goa/">Best EMR Software In Goa</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-gujarat/">Best EMR Software In Gujarat</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-haryana/">Best EMR Software In Haryana</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-himachal-pradesh/">Best EMR Software In Himachal Pradesh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-jammu-kashmir/">Best EMR Software In Jammu and Kashmir</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-jharkhand/">Best EMR Software In Jharkhand</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-karnataka/">Best EMR Software In Karnataka</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-kerala/">Best EMR Software In Kerala</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-ladakh/">Best EMR Software In Ladakh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-lakshadweep/">Best EMR Software In Lakshadweep</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-madhya-pradesh/">Best EMR Software In Madhya Pradesh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-maharashtra/">Best EMR Software In Maharashtra</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-manipur/">Best EMR Software In Manipur</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-meghalaya/">Best EMR Software In Meghalaya</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-mizoram/">Best EMR Software In Mizoram</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-nagaland/">Best EMR Software In Nagaland</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-odisha/">Best EMR Software In Odisha</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-puducherry/">Best EMR Software In Puducherry</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-punjab/">Best EMR Software In Punjab</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-rajasthan/">Best EMR Software In Rajasthan</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-sikkim/">Best EMR Software In Sikkim</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-tamil-nadu/">Best EMR Software In Tamil Nadu</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-telangana/">Best EMR Software In Telangana</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-tripura/">Best EMR Software In Tripura</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-uttar-pradesh/">Best EMR Software In Uttar Pradesh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-uttarakhand/">Best EMR Software In Uttarakhand</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-emr-software-west-bengal/">Best EMR Software In West Bengal</a></li>
				</ul>
			</div>
		</div>
	</div>
	
	
	<div class="container">
		<div class="menu-item sub-menu-item">
			<div>
				<h2>Best EHR Software In India</h2>
				<ul class="sub-menu">
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-india/" target="_blank"> Best EHR Software In India </a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-andaman-nicobar-islands/">Best EHR Software In Andaman and Nicobar Islands</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-andhra-pradesh/">Best EHR Software In Andhra Pradesh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-arunachal-pradesh/">Best EHR Software In Arunachal Pradesh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-assam/">Best EHR Software In Assam</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-bihar/">Best EHR Software In Bihar</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-chandigarh/">Best EHR Software In Chandigarh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-chhattisgarh/">Best EHR Software In Chhattisgarh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-dadra-nagar-haveli-daman-diu/">Best EHR Software In Dadra and Nagar Haveli and Daman and Diu</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-delhi/">Best EHR Software In Delhi</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-goa/">Best EHR Software In Goa</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-gujarat/">Best EHR Software In Gujarat</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-haryana/">Best EHR Software In Haryana</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-himachal-pradesh/">Best EHR Software In Himachal Pradesh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-jammu-kashmir/">Best EHR Software In Jammu and Kashmir</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-jharkhand/">Best EHR Software In Jharkhand</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-karnataka/">Best EHR Software In Karnataka</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-kerala/">Best EHR Software In Kerala</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-ladakh/">Best EHR Software In Ladakh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-lakshadweep/">Best EHR Software In Lakshadweep</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-madhya-pradesh/">Best EHR Software In Madhya Pradesh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-maharashtra/">Best EHR Software In Maharashtra</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-manipur/">Best EHR Software In Manipur</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-meghalaya/">Best EHR Software In Meghalaya</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-mizoram/">Best EHR Software In Mizoram</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-nagaland/">Best EHR Software In Nagaland</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-odisha/">Best EHR Software In Odisha</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-puducherry/">Best EHR Software In Puducherry</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-punjab/">Best EHR Software In Punjab</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-rajasthan/">Best EHR Software In Rajasthan</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-sikkim/">Best EHR Software In Sikkim</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-tamil-nadu/">Best EHR Software In Tamil Nadu</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-telangana/">Best EHR Software In Telangana</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-tripura/">Best EHR Software In Tripura</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-uttar-pradesh/">Best EHR Software In Uttar Pradesh</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-uttarakhand/">Best EHR Software In Uttarakhand</a></li>
					<li class="menu-link"><a href="https://healthray.com/best-ehr-software-west-bengal/">Best EHR Software In West Bengal</a></li>
				</ul>
			</div>
		</div>
	</div>

	<!-- Alternative -->
	<div class="container">
		<div class="menu-item sub-menu-item">
			<div>
				<h2> HIMS Compare With </h2>
				<ul class="sub-menu">
					<li class="menu-link"><a href="https://healthray.com/advancedmd-alternative/" target="_blank"> AdvancedMD Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/docon-alternative/" target="_blank"> Docon Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/docpulse-alternative/" target="_blank"> Docpulse Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/healthplix-alternative/" target="_blank"> Healthplix Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/karexpert-alternative/" target="_blank"> Karexpert Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/mocdoc-alternative/" target="_blank"> MocDoc Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/practo-alternative/" target="_blank"> Practo Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/akhil-system-alternative/" target="_blank"> Akhil System Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/ezovion-alternative/" target="_blank"> Ezovion Alternative </a></li>
					
				</ul>
			</div>
		</div>
	</div>
	<!-- Lab Alternative -->
	<div class="container">
		<div class="menu-item sub-menu-item">
			<div>
				<h2> LIMS Compare With </h2>
				<ul class="sub-menu">
					<li class="menu-link"><a href="https://healthray.com/crelio-laboratory-alternative/" target="_blank"> Crelio Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/labsmart-alternative/" target="_blank"> LabSmart Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/labguru-alternative/" target="_blank"> Labguru Alternative </a></li>
					<li class="menu-link"><a href="https://healthray.com/elabassist-alternative/" target="_blank"> eLabAssist Alternative </a></li>
				</ul>
			</div>
		</div>
	</div>
	<!-- Extra Links -->
	<div class="container">
		<div class="menu-item sub-menu-item">

			<div>
				<h2>Extra Links</h2>
				<ul class="sub-menu">
					<li class="menu-link"><a href="https://healthray.com/medical-billing-services/" target="_blank"> Medical Billing Services </a></li>
					<li class="menu-link"><a href="https://healthray.com/telehealth-services/" target="_blank"> Telehealth Services </a></li>
					<li class="menu-link"><a href="https://healthray.com/patient-mobile-app/" target="_blank"> Patient Mobile App </a></li>
					<li class="menu-link"><a href="https://healthray.com/doctor-mobile-app/" target="_blank"> Doctor Mobile App </a></li>
					<li class="menu-link"><a href="https://healthray.com/medical-billing-software/" target="_blank"> Medical Billing Software </a></li>
					<li class="menu-link"><a href="https://healthray.com/practice-management/" target="_blank"> Practice Management </a></li>
					<li class="menu-link"><a href="https://healthray.com/patient-engagement-software/" target="_blank"> Patient Engagement Software </a></li>
					<li class="menu-link"><a href="https://healthray.com/medical-iot-solution/" target="_blank"> Medical IoT Solution </a></li>
					<li class="menu-link"><a href="https://healthray.com/patient-flow/" target="_blank"> Patient Flow </a></li>
				</ul>
			</div>

		</div>
	</div>
</div>
