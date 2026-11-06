<?php
// Optional "Reviewed by" field for posts and alternatives.

if (function_exists('acf_add_local_field_group')) {
	acf_add_local_field_group(array(
		'key'      => 'group_hr_reviewer',
		'title'    => 'Reviewer',
		'fields'   => array(
			array(
				'key'           => 'field_hr_reviewed_by',
				'label'         => 'Reviewed by',
				'name'          => 'reviewed_by',
				'type'          => 'user',
				'instructions'  => 'Optional. Leave empty if this content has no reviewer.',
				'required'      => 0,
				'allow_null'    => 1,
				'multiple'      => 0,
				'return_format' => 'id',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'post',
				),
			),
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'alternatives',
				),
			),
		),
		'position' => 'side',
		'style'    => 'default',
	));
}

// Reviewer details for a post, or null when none is set.
function hr_get_reviewer($post_id)
{
	if (!function_exists('get_field')) {
		return null;
	}

	$reviewer_id = (int) get_field('reviewed_by', $post_id);
	if (!$reviewer_id) {
		return null;
	}

	$user = get_userdata($reviewer_id);
	if (!$user) {
		return null;
	}

	$name = trim((string) $user->display_name);

	$position = trim((string) get_user_meta($reviewer_id, 'user_position', true));
	if ($position === '') {
		$position = trim((string) get_user_meta($reviewer_id, 'job_title', true));
	}

	return array(
		'id'       => $reviewer_id,
		'name'     => $name,
		'url'      => get_author_posts_url($reviewer_id),
		'avatar'   => (string) get_avatar_url($reviewer_id, array('size' => 100)),
		'initials' => implode('', array_map(
			fn($w) => strtoupper($w[0]),
			preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY) ?: array()
		)),
		'position' => $position,
	);
}
