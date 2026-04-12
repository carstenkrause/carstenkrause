<?php
/**
 * Plugin Name: CDO Times Fractional Executives
 * Description: Provides a searchable list of fractional executives and generates AI-enhanced resumes using a remote API.
 * Version: 0.1
 * Author: CDO Times
 */

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register custom post type for executives.
 */
function cdt_register_executive_cpt() {
    $labels = array(
        'name'               => 'Executives',
        'singular_name'      => 'Executive',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Executive',
        'edit_item'          => 'Edit Executive',
        'new_item'           => 'New Executive',
        'view_item'          => 'View Executive',
        'search_items'       => 'Search Executives',
        'not_found'          => 'No executives found',
        'not_found_in_trash' => 'No executives found in Trash',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'show_in_rest'       => true,
        'supports'           => array('title', 'editor', 'thumbnail'),
        'rewrite'            => array('slug' => 'executives'),
    );

    register_post_type('cdt_executive', $args);
}
add_action('init', 'cdt_register_executive_cpt');

/**
 * Shortcode to display searchable list of executives.
 */
function cdt_executive_list_shortcode($atts) {
    $search = isset($_GET['cdt_search']) ? sanitize_text_field($_GET['cdt_search']) : '';
    $args   = array(
        'post_type'      => 'cdt_executive',
        's'              => $search,
        'posts_per_page' => -1,
    );
    $query  = new WP_Query($args);

    ob_start();
    ?>
    <form method="get" class="cdt-executive-search">
        <input type="text" name="cdt_search" value="<?php echo esc_attr($search); ?>" placeholder="Search executives">
        <button type="submit">Search</button>
    </form>
    <ul class="cdt-executive-list">
    <?php
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            echo '<li><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></li>';
        }
    } else {
        echo '<li>No executives found.</li>';
    }
    ?>
    </ul>
    <?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('cdt_executive_list', 'cdt_executive_list_shortcode');

/**
 * Generate AI resume via external API.
 *
 * Expects constant CDT_AI_ENDPOINT to be defined with the API URL.
 */
function cdt_generate_ai_resume($post_id) {
    if (!defined('CDT_AI_ENDPOINT')) {
        return 'AI endpoint not configured.';
    }

    $content = strip_tags(get_post_field('post_content', $post_id));
    $title   = get_the_title($post_id);
    $prompt  = "Create a professional resume for {$title} based on the following details:\n{$content}";

    $response = wp_remote_post(CDT_AI_ENDPOINT, array(
        'headers' => array('Content-Type' => 'application/json'),
        'body'    => wp_json_encode(array('prompt' => $prompt)),
        'timeout' => 20,
    ));

    if (is_wp_error($response)) {
        return 'Error contacting AI service.';
    }

    $body = wp_remote_retrieve_body($response);
    return esc_html($body);
}

/**
 * Shortcode to display AI resume for a given executive.
 */
function cdt_ai_resume_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => get_the_ID(),
    ), $atts, 'cdt_ai_resume');

    return '<pre class="cdt-ai-resume">' . cdt_generate_ai_resume(intval($atts['id'])) . '</pre>';
}
add_shortcode('cdt_ai_resume', 'cdt_ai_resume_shortcode');
