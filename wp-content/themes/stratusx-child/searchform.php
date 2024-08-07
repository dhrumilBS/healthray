<style>
	.search-form input { border-top-left-radius: 50px; border: 1px solid #e2e2e2; box-shadow: none; }
	.search-form input { border-right: 0 !important; border-top-left-radius: 50px !important; border-bottom-left-radius: 50px !important; }
	.search-form input:focus { border: 1px solid #31335a; }
	.search-form input[type=search] { padding: 6px 20px 6px 25px; }
	.search-submit { padding: 14px 20px; color: #fff; background-color: #31335a; opacity: 1; border: 0; margin-left: 0 !important; }
	.search-submit:hover { color: #fff; background-color: #31335a; opacity: 0.9; box-shadow: none; }
	
	.search-form { margin-bottom: 0;}
	.input-group { position: relative; display: flex; align-items: stretch; width: 100%; }
	.input-group .btn { position: relative; z-index: 2; }	
	.search-form .input-group input.search-field { border-bottom-right-radius: 0; border-top-right-radius: 0;  flex-grow:1; }	
	.input-group-btn:last-child>.btn{ border-radius: 50px; border-bottom-left-radius: 0; border-top-left-radius: 0}

	@media (max-width: 479px) {
		.btn-default { padding: 10px 25px ; }
	}
</style>
<form role="search" method="get" class="search-form form-inline" action="<?php echo esc_url(home_url('/')); ?>">
	<div class="input-group">
		<input type="search" value="<?php if (is_search()) { echo get_search_query(); } ?>" name="s" class="search-field form-control" placeholder="<?php esc_html_e('Search', 'stratus'); ?> <?php bloginfo('name'); ?>">
		<span class="input-group-btn">
			<button type="submit" class="search-submit btn btn-default"><?php esc_html_e('Search', 'stratus'); ?></button>
		</span>
	</div>
</form>