<?php
/**
 * Plugin Name:       Kebbet plugins - custom taxonomy: module
 * Plugin URI:        https://github.com/kebbet/kebbet-taxonomy-module
 * Description:       Register the custom taxonomy module
 * Version:           1.0
 * Author:            Erik Betshammar
 * Author URI:        https://verkan.se
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Update URI:        false
 *
 * @package kebbet-taxonomy-module
 * @author Erik Betshammar
 */

namespace kebbet\taxonomy\module;

const TAXONOMY   = 'module';
const POST_TYPES = array( 'post', 'event-archive' );
const HIDE_SLUG  = false;

/**
 * Hook into the 'init' action
 */
function init() {
	load_textdomain();
	register();
}
add_action( 'init', __NAMESPACE__ . '\init', 0 );

/**
 * Flush rewrite rules on registration.
 */
function rewrite_flush() {
	// First, we "add" the custom taxonomy via the above written function.
	register();

	// ATTENTION: This is *only* done during plugin activation hook in this example!
	// You should *NEVER EVER* do this on every page load!!
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\rewrite_flush' );

/**
 * Load plugin textdomain.
 */
function load_textdomain() {
	load_plugin_textdomain( 'kebbet-taxonomy-module', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
 * Register the taxonomy
 */
function register() {

	$tax_labels = array(
		'name'                       => _x( 'Program module', 'taxonomy general name', 'kebbet-taxonomy-module' ),
		'menu_name'                  => __( 'Program module', 'kebbet-taxonomy-module' ),
		'singular_name'              => _x( 'Program module', 'taxonomy singular name', 'kebbet-taxonomy-module' ),
		'all_items'                  => __( 'All program module tags', 'kebbet-taxonomy-module' ),
		'edit_item'                  => __( 'Edit tag', 'kebbet-taxonomy-module' ),
		'view_item'                  => __( 'View tag', 'kebbet-taxonomy-module' ),
		'update_item'                => __( 'Update tag', 'kebbet-taxonomy-module' ),
		'add_new_item'               => __( 'Add new tag', 'kebbet-taxonomy-module' ),
		'new_item_name'              => __( 'New tag name', 'kebbet-taxonomy-module' ),
		'separate_items_with_commas' => __( 'Separate program module tags with commas', 'kebbet-taxonomy-module' ),
		'search_items'               => __( 'Search tags', 'kebbet-taxonomy-module' ),
		'add_or_remove_items'        => __( 'Add or remove tags', 'kebbet-taxonomy-module' ),
		'choose_from_most_used'      => __( 'Choose from the most used  tags', 'kebbet-taxonomy-module' ),
		'not_found'                  => __( 'No tags found.', 'kebbet-taxonomy-module' ),
		'popular_items'              => __( 'Popular tags', 'kebbet-taxonomy-module' ),
		'parent_item'                => __( 'Parent tag', 'kebbet-taxonomy-module' ),
		'parent_item_colon'          => __( 'Parent tag:', 'kebbet-taxonomy-module' ),
		'back_to_items'              => __( '&larr; Back to tags', 'kebbet-taxonomy-module' ),
		'name_field_description'     => __( 'The name is how it appears in the user interface.', 'kebbet-taxonomy-module' ),
		'slug_field_description'     => __( 'The &#8220;slug&#8221; is a sanitized version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens. Do not change if not needed.', 'kebbet-taxonomy-module' ),
		'desc_field_description'     => __( 'The description is not used for Dansehallerne.', 'kebbet-taxonomy-module' ),
	);

	$capabilities = array(
		'manage_terms' => 'manage_categories', // Previous 'manage_options'.
		'edit_terms'   => 'manage_categories', // Previous 'manage_options'.
		'delete_terms' => 'manage_categories', // Previous 'manage_options'.
		'assign_terms' => 'publish_posts',
	);

	$tax_args = array(
		'capabilities'          => $capabilities,
		'hierarchical'          => false,
		'has_archive'           => false,
		'labels'                => $tax_labels,
		'public'                => false,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => false,
		'show_in_rest'          => true,
		'rewrite'               => false,
		'description'           => __( 'Metadata tag.', 'kebbet-taxonomy-module' ),
	);

	register_taxonomy( TAXONOMY, POST_TYPES, $tax_args );
}

/**
 * Remove the 'slug' column from the table in 'edit-tags.php'
 */
function remove_column_slug( $columns ) {
    if ( isset( $columns['slug'] ) )
        unset( $columns['slug'] );   

    return $columns;
}

/**
 * Run filter only if constant says so.
 */
if ( true === HIDE_SLUG ) {
	add_filter( 'manage_edit-' . TAXONOMY . '_columns', __NAMESPACE__ . '\remove_column_slug' );
}
