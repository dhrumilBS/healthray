<?php
$num_comments = get_comments_number(); // get_comments_number returns only a numeric value
$write_comments = "";
global $post;
$str = '';

foreach (wp_get_post_terms($post->ID, 'category') as $term) {
	$temp['title'] =  $term->name;
	$temp['url'] = get_term_link($term);
	$termsList[] = $temp;
}
if ($termsList > 0) {
	$str .= '| ';
	foreach ($termsList as $index => $term) {
		$str .= '<a href="' . $term['url'] . '">' . $term['title'] . (isset($termsList[$index + 1]) ? ', ' : '') . '</a>';
	}
}
?>
<div class="post-meta">
	<span class="show-author"> Written by <?php the_author_posts_link(); ?></span>
	<span class="date"> | <?php echo get_the_date(); ?></span>
	<span class="show-category"><?= $str ?> </span>
</div>