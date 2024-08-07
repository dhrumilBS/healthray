<style>
	
	/* category list */
	.categories-list {display: flex; justify-content: center; }
	.categories-list>ul {display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; list-style: none; padding: 0; margin: 0; }
	.categories-list>ul>li.cat-item a { padding: 4px 12px; background-color: #3a669b3b; border-radius: 50px; display: flex; }
	.categories-list>ul>li.cat-item a:hover { background-color: #31335a; color: #FFF; }

	.blog-category-list { display: none;} 
	.categories-list ul{ position: relative; }
	.more-category .blog-category-more-list { position: absolute; top: 44px; right: -30px; background: #FFF; min-width: 270px; z-index: 3; border-radius: 10px;  overflow: hidden }
	.more-category .blog-category-more-list ul { max-height: 300px; overflow: auto; padding: 20px; cursor: default; margin: 0; border: 1px solid #DCC; border-radius: 10px; display: block; max-width: inherit; }
	.more-category .blog-category-more-list ul li { display: block; cursor: default }
	.more-category .blog-category-more-list ul li a { display: block; padding: 8px 0; line-height: 1.5; color: #1D1C39; background-color: transparent }
	/* .more-category .blog-category-more-list ul li a:hover,  */
	.more-category .blog-category-more-list ul li a.active { color: #ffffff; background-color: #31335a;} 

	.more-category .blog-category-more-link svg {font-size: 12px; margin-left: 8px;}
	.more-category .blog-category-more-list ul::-webkit-scrollbar { width: 5px; right: 5px; }
	.more-category .blog-category-more-list ul::-webkit-scrollbar-thumb { background: var(--hr-primary-color); border-radius: 4px;}
	.more-category .blog-category-more-list ul::-webkit-scrollbar-thumb:hover { background: #1D1C39; } 

	@media only screen and (min-width: 768px) {	
		.more-category { position: relative; } 
		.blog-category-list { display: flex; flex-wrap: wrap; align-items: center; row-gap: 15px; column-gap: 10px; position: relative; } 
		.blog-category-list .cat-item { font-size: 14px; border-radius: 30px; border: 1px solid #ffffff; color: #FFF; letter-spacing: 1px; text-transform: uppercase; padding: 4px 10px; }
		.blog-category-list .cat-item.active, .blog-category-list .cat-item:hover { background-color: #ffffff; color: #1D1C39; transition: all .5s;}
		.more-category .blog-category-more-link { cursor: pointer; display: flex; align-items: center; }
	}
	@media only screen and (max-width: 375px) {
		.container{ width: 100%; }
		.more-category .blog-category-more-list{ top: 100%; left: 0; right: 0; } 
	}</style>
<div class="categories-list">
	<?php 
	if(is_author()){
		while (have_posts()) { the_post(); foreach ( get_the_category($post->ID) as $key => $value) { $cats[] = $value->term_id; $cats = array_unique($cats); } } ?>
	<ul>
		<?php foreach($cats as $category){$term=get_term($category,'category'); echo '<li class="cat-item"> <a href="'.get_term_link($category).'">'.$term->name.'</a></li>';} ?>
	</ul>

	<?php } else { ?>

	<ul>
		<?php 
		$blog_cat_id = 34;
		$cat = [];
		$show_cat = 7;
		foreach (get_categories(['orderby' => 'count', 'order'   => 'DESC']) as $i => $category) {
			$class  = ' catagory-'.$category->term_taxonomy_id;
			$class .= (get_queried_object()->term_taxonomy_id == $category->term_taxonomy_id) ? ' active': '';

			if($category->term_id != $blog_cat_id){
				if($i < $show_cat ){ 
					echo '<li class="cat-item'.$class.'"><a href="' . get_category_link($category) . '">' . $category->name . '</a> </li>';
				} else {
					$cat[] = '<a class="more-cat-item cat-item' .$class.'"  href="' . get_category_link($category) . '">' . $category->name . '</a> ';
				}
			}
		}
		?>
		<?php if($i > $show_cat ){ ?>
		<li class="more-category cat-item">
			<a class="blog-category-more-link"><span>More </span>
				<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="width: 14px;"><path fill="currentColor" d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"></path></svg>
			</a>
			<div class="blog-category-more-list" style="display: none; ">
				<ul><?php foreach ($cat as $c) {echo '<li>'. $c.'</li>';} ?></ul>
			</div>
		</li>
		<?php } ?>
	</ul>
	<?php } ?> 
</div>

