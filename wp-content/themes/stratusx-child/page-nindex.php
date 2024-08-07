<link href="https://healthray.com/wp-content/uploads/assets/glightbox/css/glightbox.min.css" rel="stylesheet">
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.4.2/uicons-regular-rounded/css/uicons-regular-rounded.css'>
<style>
	.flex{ display: flex; flex-wrap:wrap; }
	.flex .col { width:calc(100% / 5);  }
	.flex .flex-image { border: 1px solid #dcdcdc; width:120px; text-align: center;}
	.flex .flex-image img{ object-fit: contain; height: 100px}


	.container { padding: 50px 0; max-width: 1440px !important; }

	.state-city{  margin-bottom: 50px }
	.state-city span{font-weight: 600;  }

	.list-style{ list-style-type: none; padding:0;}
</style>

<div class="hospital-mgt-pages">
	<div class="container">
		<h2 style="text-align: center"> EMR-mgt-pages </h2>
		<div class="row">		
			<?= get_template_pages('templates/template-emr-state.php', 'templates/template-emr-state-city.php'); ?>
		</div>
	</div>
</div>

<div class="hospital-mgt-pages">
	<div class="container">
		<h2 style="text-align: center"> lab-mgt-pages </h2>
		<div class="row">	
			<?= get_template_pages('templates/template-lab-state.php', 'templates/template-lab-state-city.php'); ?>
		</div>
	</div>
</div>	
<div class="hospital-mgt-pages">
	<div class="container">
		<h2 style="text-align: center"> Hospital management System pages </h2>
		<div class="row">	
			<?= get_template_pages('templates/template-state-city.php', 'templates/template-city.php'); ?>		
		</div>
	</div>
</div>
<?php 

function anchor($ID, $title){
// 	return "<a href='".site_url()."/wp-admin/post.php?post=".$ID."&action=edit'><i class='fi fi-rr-pencil'></i></a> - <a href='".get_permalink($ID)."'>".$title. "</a>";
	return "<a href='".get_permalink($ID)."'>".$title. "</a>";
};

function get_template_pages($state_template_Name, $city_template_Name ){
	$html = '';
	$return ='';
	$stateArgs = [
		'meta_key' => '_wp_page_template',
		'meta_value' => $state_template_Name,
		'posts_per_page'         => -1,
	];
	$cityArgs = [
		'meta_key' => '_wp_page_template',
		'meta_value' => $city_template_Name,
		'posts_per_page' => -1,
	];
	$statepages = get_pages($stateArgs);
	$citypages = get_pages($cityArgs);				
	foreach ($statepages as $index => $stateItem) {
		$state_id =	$stateItem->ID;
// 		$return = "<div class='state-city' style='width: calc(100% / 3)'>";
		$return = "<li class='menu-link'>";
// 		$return .= "<span>".anchor($state_id, $stateItem->post_title)."</span>";
		$return .=  anchor($state_id, $stateItem->post_title);
// 		$return .= "<ul class='list-style'>";
// 		foreach ($citypages as $index2 => $cityItem) {
// 			$city_state_id = get_field('state_name_link', $cityItem->ID);
// 			if($city_state_id->ID == $stateItem->ID){
// 				$return .=  "<li>".anchor($cityItem->ID, $cityItem->post_title)."</li>";
// 			} 
// 		} 
// 		$return .= "</ul>";
// 		$return .= "</div>";
		$return .= "</li>";
		$html .= $return;
	}
	return $html;
}









$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => 'https://api.countrystatecity.in/v1/countries/IN',
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_HTTPHEADER => array(
		'X-CSCAPI-KEY: YWY1ODZLODZWZFFUcXFJVGJGTEhTZkUzMlNZaG5TVDJTNWhySWRPTA=='
	),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

?>







<?php if(1 == 2){  ?>
<div class="flex">
	<?php
	$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
	$as = array(
		'post_type'      => 'attachment',
		'posts_per_page'         => -1,
		'post_mime_type' => 'image', 
		'paged' => $paged
	);
	$attachments = get_posts( $as ); 
	$os = array("image/webp", "image/svg+xml");

	if ( $attachments )
	{
		foreach ( $attachments as $attachment ) 
		{ 
			if (in_array($attachment->post_mime_type, $os)) {
				$png ='#fcf';
			}else{
				$png ='green';
			}
			$size = filesize( get_attached_file( $attachment->ID ) );
			$size = number_format($size / 1024, 2);

	?>
	<div href="<?= get_permalink($attachment->ID); ?>" class="flex-image" style='margin:2px;border:1px solid <?= $png ?>'>		
		<?= wp_get_attachment_image( $attachment->ID, array('100', 'auto'), "", array( "class" => "img-responsive" ) );  ?>
		<?= "<p> Size: ".$size . "</p>"; ?>
		<?= "<p>". $attachment->post_mime_type . "</p>"; ?>
		<a href="<?= get_permalink($attachment->ID); ?>" class="image" data-gallery="portfolioGallery">view</a>
	</div> 

	<?php

		}
	} else {
		echo 'No image attachments found.';
	}
	?>
</div>

<?php } ?> 
<script src="https://cdn.jsdelivr.net/npm/animejs@3.1.0/lib/anime.min.js"></script>
<script src="https://healthray.com/wp-content/uploads/assets/glightbox/js/glightbox.min.js"></script>
<script>
	const portfolioLightbox = GLightbox({
		selector: '.image',
		openEffect: 'zoom',
		closeEffect: 'fade',
		loop: true, 
		touchNavigation: true
	});</script>