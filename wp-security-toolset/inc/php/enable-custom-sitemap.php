<?php
// Enable custom sitemap generation
if (get_option('wpst_enable_custom_sitemap')) {
    add_filter('wp_sitemaps_add_provider', 'remove_sitemap_provider', 10, 2);
    add_filter('wp_sitemaps_posts_entry', 'add_lastmod_to_sitemap', 10, 3);
    add_filter('wp_sitemaps_pages_entry', 'add_lastmod_to_sitemap', 10, 3);
    add_action( "save_post", "eg_create_sitemap" );
    
} else {
    remove_filter('wp_sitemaps_add_provider', 'remove_sitemap_provider', 10, 2);
    remove_filter('wp_sitemaps_posts_entry', 'add_lastmod_to_sitemap', 10, 3);
    remove_filter('wp_sitemaps_pages_entry', 'add_lastmod_to_sitemap', 10, 3);
    remove_action( "save_post", "eg_create_sitemap" );
}

function remove_sitemap_provider($provider, $name) {
    $remove_providers = ['users', 'taxonomies'];
    if (in_array($name, $remove_providers)) {
        return false;
    }
    return $provider;
}

function add_lastmod_to_sitemap($url, $post_type, $post) {
    if ('post' == $post_type || 'page' == $post_type) {
        $url['lastmod'] = get_the_modified_date('c', $post);
    }
    return $url;
}

// function to create sitemap.xml file in root directory of site  
function eg_create_sitemap() {

    $sitemap = '';

    if ( str_replace( '-', '', get_option( 'gmt_offset' ) ) < 10 ) { 
        $tempo = '-0' . str_replace( '-', '', get_option( 'gmt_offset' ) ); 
    } else { 
        $tempo = get_option( 'gmt_offset' ); 
    }
    if( strlen( $tempo ) == 3 ) { $tempo = $tempo . ':00'; }
    $postsForSitemap = get_posts( array(
        'numberposts' => -1,
        'orderby'     => 'modified',
        'post_type'   => array( 'post', 'page' ),
        'order'       => 'DESC'
    ) );

    if ( ! empty($postsForSitemap) ) {
        $sitemap .= '<?xml version="1.0" encoding="UTF-8"?>' . '<?xml-stylesheet type="text/xsl" href="' . 
            esc_url( home_url( '/' ) ) . 'sitemap.xsl"?>';
        $sitemap .= "\n" . '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        $sitemap .= "\t" . '<url>' . "\n" .
            "\t\t" . '<loc>' . esc_url( home_url( '/' ) ) . '</loc>' .
            "\n\t\t" . '<lastmod>' . date( "Y-m-d\TH:i:s", current_time( 'timestamp', 0 ) ) . $tempo . '</lastmod>' .
            "\n\t" . '</url>' . "\n";
        foreach( $postsForSitemap as $post ) {
            setup_postdata( $post);
            $postdate = explode( " ", $post->post_modified );
            $sitemap .= "\t" . '<url>' . "\n" .
                "\t\t" . '<loc>' . get_permalink( $post->ID ) . '</loc>' .
                "\n\t\t" . '<lastmod>' . $postdate[0] . 'T' . $postdate[1] . $tempo . '</lastmod>' .
                "\n\t" . '</url>' . "\n";
        }
        $sitemap .= '</urlset>';
        $fp = fopen( ABSPATH . "sitemap.xml", 'w' );
        fwrite( $fp, $sitemap );
        fclose( $fp );
    }
}