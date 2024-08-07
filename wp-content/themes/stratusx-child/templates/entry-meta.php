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

if (comments_open()) {
	if ($num_comments == 0) {
		$comments = esc_html__('No Comments', 'stratus');
	} elseif ($num_comments > 1) {
		$comments = $num_comments . esc_html__(' Comments', 'stratus');
	} else {
		$comments = esc_html__('1 Comment', 'stratus');
	}
	$write_comments = '| <a href="' . esc_url(get_comments_link()) . '">' . $comments . '</a>';
}
?>
<div class="post-meta">
	<span class="show-author"> Written by <?php the_author_posts_link(); ?></span>
	<?php if ($num_comments >= 1) { ?> <span class="show-comments"><?php echo wp_kses_post($write_comments); ?></span> <?php } ?>
	<span class="show-category"><?= $str ?> </span>
</div>