<?php
/**
 * Search form.
 *
 * Submits to the site search (search.php), limited to the section the form sits in:
 * an archive searches its own post type, blog listings search blog posts, the search
 * page keeps its current filter, and everywhere else searches the whole site.
 * See hr_search_form_scope() in lib/search.php.
 */

$hr_scope       = function_exists('hr_search_form_scope') ? hr_search_form_scope() : '';
$hr_types       = function_exists('hr_search_types') ? hr_search_types() : array();
$hr_placeholder = ($hr_scope && isset($hr_types[$hr_scope])) ? 'Search ' . $hr_types[$hr_scope]['plural'] : 'Search Healthray';

// A page can hold more than one form (hero and empty state), so ids must be unique.
$hr_field_id = wp_unique_id('search-field-');
?>
<form role="search" method="get" class="search-form form-inline" action="<?php echo esc_url(home_url('/')); ?>">
    <?php if ($hr_scope) : ?>
        <input type="hidden" name="post_type" value="<?php echo esc_attr($hr_scope); ?>">
    <?php endif; ?>

    <div class="search-group">
        <label class="screen-reader-text" for="<?php echo esc_attr($hr_field_id); ?>"><?php _e('Search for:', 'stratus'); ?></label>
        <input
            type="search"
            id="<?php echo esc_attr($hr_field_id); ?>"
            name="s"
            class="search-field"
            value="<?php echo esc_attr(get_search_query()); ?>"
            placeholder="<?php echo esc_attr($hr_placeholder); ?>"
            aria-label="<?php echo esc_attr($hr_placeholder); ?>"
        >
        <button class="btn-submit" type="submit" aria-label="<?php esc_attr_e('Submit search', 'stratus'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 118.783 118.783" fill="currentColor" aria-hidden="true" focusable="false">
                <path d="M115.97,101.597L88.661,74.286c4.64-7.387,7.333-16.118,7.333-25.488c0-26.509-21.49-47.996-47.998-47.996S0,22.289,0,48.798c0,26.51,21.487,47.995,47.996,47.995c10.197,0,19.642-3.188,27.414-8.605l26.984,26.986c1.875,1.873,4.333,2.806,6.788,2.806c2.458,0,4.913-0.933,6.791-2.806C119.72,111.423,119.72,105.347,115.97,101.597z M47.996,81.243c-17.917,0-32.443-14.525-32.443-32.443s14.526-32.444,32.443-32.444c17.918,0,32.443,14.526,32.443,32.444S65.914,81.243,47.996,81.243z"/>
            </svg>
        </button>
    </div>
</form>
