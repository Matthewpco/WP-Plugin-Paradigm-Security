<?php
// Enable /blog/ prefix to posts
if (get_option('wpst_enable_blog_prefix')) {
    add_filter('rewrite_rules_array', 'add_blog_prefix_rewrite_rule');
    add_filter('post_link', 'add_blog_prefix_to_posts', 10, 3);
    

} else {
    remove_filter('rewrite_rules_array', 'add_blog_prefix_rewrite_rule');
    remove_filter('post_link', 'add_blog_prefix_to_posts', 10, 3);
    
    
}

// Add custom rewrite rule for the /blog/ prefix
function add_blog_prefix_rewrite_rule($rules) {
	$new_rules = array(
		 'blog/([^/]+)/?$' => 'index.php?name=$matches[1]',
	);
	return $new_rules + $rules;
}


// Function to modify post permalinks
function add_blog_prefix_to_posts($permalink, $post, $leavename) {
	if (get_post_type($post) != 'post' || !in_array($post->post_status, ['publish', 'inherit'])) {
		 return $permalink;
	}
	$permalink = home_url('/blog/' . $post->post_name . '/');
	return $permalink;
}
