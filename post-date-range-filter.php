<?php
/*
 * Plugin Name: Post Date Range Filter
 * Plugin URI: https://wordpress.org/plugins/post-date-range-filter/
 * Description: Any Post or any custom post category you can view Date Range Filter. Take full control over your WordPress site, build any shortcode paste you can imagine – no programming knowledge required.
 * Author: Md. Shahinur Islam
 * Author URI: https://profiles.wordpress.org/shahinurislam
 * Version: 1.02
 * Text Domain: post-date-range-filter
 * Domain Path: /lang
 * Network: True
 * License: GPLv2
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
//-------------All post show------------//
function pdrf_shortcode_wrapper($atts) {
ob_start();
//set attributies
$atts = shortcode_atts(
	array(
		'post_type' => '',
		'categories' => ''
	), $atts, 'pdrf_shahin'); 
?>	
<!-- Add the date range filter form HTML -->
<form method="GET" action="<?php global $wp; echo esc_url(home_url($wp->request)); ?>">
    <label for="start_date">Start Date:</label>
    <?php $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : '';?>
    <input type="date" id="start_date" name="start_date" value="<?php echo esc_attr($start_date); ?>">    
    <label for="end_date">End Date:</label>
    <?php $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : '';?>
    <input type="date" id="end_date" name="end_date" value="<?php echo esc_attr($end_date); ?>">
    <input type="submit" value="Filter">
</form>
<?php
// Check if filter parameters are set
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    // Retrieve the date range values from the form
    $start_date = sanitize_text_field($_GET['start_date']);
    $end_date = sanitize_text_field($_GET['end_date']);

    // Create the custom query to fetch posts within the date range
    $args = array(
        'post_type' => esc_html($atts['post_type']),
        'category_name' => esc_html($atts['categories']),
        'posts_per_page'=> esc_html(get_option('blogidpdrf')),
        'date_query' => array(
            'after' => $start_date,
            'before' => $end_date,
            'inclusive' => true,
        ),
    );

    // Perform the query
    $filtered_posts = new WP_Query($args);

    // Display the filtered posts
    if ($filtered_posts->have_posts()) {
        while ($filtered_posts->have_posts()) {
            $filtered_posts->the_post();
            // Display the post content using WPBakery elements or shortcodes
            ?>
        <div class="articles-list-pdrf">
        	<article class="post">
            <div class="post-thumbnail-wrap">
            	<div class="post-thumbnail">
            		<a href="<?php the_permalink();?>" class="post-thumbnail-rollover"><?php if(has_post_thumbnail()) the_post_thumbnail('main-thumb');?></a>
            	</div>
            </div>
            <div class="post-entry-content">
                	<h3 class="entry-title">
                		<a href="<?php the_permalink();?>" 
                		title="<?php the_title();?>" rel="bookmark"><?php the_title();?></a>
                	</h3>
                	<div class="entry-meta">
                	    <p>
                	        <time class="entry-date updated" datetime="2023-05-23T11:36:07+06:00"><?php echo esc_html(get_the_time('j'))?> <span> <?php echo esc_html(get_the_time('M'))?></span>, <span> <?php echo esc_html(get_the_time('Y'))?></span></time>
                	    </p>
                	</div>
                	<div class="entry-excerpt"><p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 15)); ?></p>
                    </div>
                <a href="<?php the_permalink();?>" class="post-details">Read more<i class="dt-icon-the7-arrow-03" aria-hidden="true"></i></a>
            
            </div>
            </article>
        </div>
        <?php
        }
        wp_reset_postdata();
    } else {
        // No posts found
        echo 'No posts found.';
    }
} else {
    // Retrieve all posts from the "News" category
    $args = array(
        'post_type' => esc_html($atts['post_type']),
        'category_name' => esc_html($atts['categories']),
        'posts_per_page'=> esc_html(get_option('blogidpdrf')),
    );

    // Perform the query
    $all_posts = new WP_Query($args);

    // Display all posts
    if ($all_posts->have_posts()) {
        while ($all_posts->have_posts()) {
            $all_posts->the_post();
            // Display the post content using WPBakery elements or shortcodes
            
            ?>
        <div class="articles-list-pdrf">
        	<article class="post">
            <div class="post-thumbnail-wrap">
            	<div class="post-thumbnail">
            		<a href="<?php the_permalink();?>" class="post-thumbnail-rollover"><?php if(has_post_thumbnail()) the_post_thumbnail('main-thumb');?></a>
            	</div>
            </div>
            <div class="post-entry-content">
                	<h3 class="entry-title">
                		<a href="<?php the_permalink();?>" 
                		title="<?php the_title();?>" rel="bookmark"><?php the_title();?></a>
                	</h3>
                	<div class="entry-meta">
                	    <p>
                	        <time class="entry-date updated" datetime="2023-05-23T11:36:07+06:00"><?php echo esc_html(get_the_time('j'))?> <span> <?php echo esc_html(get_the_time('M'))?></span>, <span> <?php echo esc_html(get_the_time('Y'))?></span></time>
                	    </p>
                	</div>
                	<div class="entry-excerpt"><p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 15)); ?></p>
                    </div>
                <a href="<?php the_permalink();?>" class="post-details">Read more<i class="dt-icon-the7-arrow-03" aria-hidden="true"></i></a>
            
            </div>
            </article>
        </div>
        <?php
            
        }
        wp_reset_postdata();
    } else {
        // No posts found
        echo 'No posts found.';
    }
}
?>		
<?php
    return ob_get_clean();
}
add_shortcode('pdrf_shortcode','pdrf_shortcode_wrapper');
// Dashboard Front Show settings page
register_activation_hook(__FILE__, 'pdrf_plugin_activate');
add_action('admin_init', 'pdrf_plugin_redirect');
function pdrf_plugin_activate() {
    add_option('pdrf_plugin_do_activation_redirect', true);
}
function pdrf_plugin_redirect() {
    if (get_option('pdrf_plugin_do_activation_redirect', false)) {
        delete_option('pdrf_plugin_do_activation_redirect');
        if(!isset($_GET['activate-multi']))
        {
            wp_redirect("edit.php?post_type=post&page=pdrf_settings");
        }
    }
}
//side setting link
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'pdrf_plugin_action_links' );
function pdrf_plugin_action_links( $actions ) {
   $actions[] = '<a href="'. esc_url( get_admin_url(null, 'edit.php?post_type=post&page=pdrf_settings') ) .'">Settings</a>';
   $actions[] = '<a href="https://m.me/md.shahinur.islam.96" target="_blank">Support for contact</a>';
   return $actions;
}
add_action('admin_menu', 'pdrf_register_my_custom_submenu_page'); 
function pdrf_register_my_custom_submenu_page() {
    add_submenu_page(
        'edit.php?post_type=post',
        'Settings',
        'Settings',
        'manage_options',
        'pdrf_settings',
        'pdrf_my_custom_submenu_page_callback' );
} 
function pdrf_my_custom_submenu_page_callback() {
    ?>
<h1>
<?php esc_html_e( 'Welcome to Post Date Range Filter.', 'post-date-range-filter' ); ?>
</h1>
<h3><?php esc_html_e( 'Copy and paste this shortcode here:', 'post-date-range-filter' );?></h3>
<p><?php esc_html_e( '[pdrf_shortcode post_type="post" categories="name"]', 'post-date-range-filter' );?></p>
<br/>
<?php echo esc_html(get_option('blogidpdrf')); ?>
<form method="post" action="options.php">
	<?php wp_nonce_field('update-options') ?>	
	<p><strong><?php esc_html_e( 'Put how many item per pages:', 'post-date-range-filter' );?></strong><br />
	<input type="text" name="blogidpdrf" size="45" value="<?php echo esc_html(get_option('blogidpdrf')); ?>" />
	<a><?php esc_html_e( 'Example: 10', 'post-date-range-filter' );?></a>
	</p>
	<p><input type="submit" name="Submit" value="Store Options" /></p>
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="blogidpdrf" />
</form>
<?php
}
