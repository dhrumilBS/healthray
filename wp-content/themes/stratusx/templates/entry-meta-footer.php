<?php
esc_html__('Tags: ', 'stratus');
the_tags( '<div class="entry-meta meta-tags">'.esc_html__('Tags: ', 'stratus').'<span class="tag-links">', ', ', '</span></div>' ); ?>



<?php
if (is_single()){
    wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>'));
}
?>