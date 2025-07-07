<?php
/**
 * Plugin Name: Renthub
 * Plugin URI: https://example.com/renthub
 * Description: A multi-owner/multi-tenant booking and inventory platform.
 * Version: 0.1.0
 * Author: Jules
 * Author URI: https://example.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: renthub
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Namespace: Renthub
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'RENTHUB_VERSION', '0.1.0' );
define( 'RENTHUB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'RENTHUB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Activation hook.
 *
 * Creates custom database tables.
 */
function renthub_activate() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	// Table: wp_rh_units
	$table_name_units = $wpdb->prefix . 'rh_units';
	$sql_units = "CREATE TABLE $table_name_units (
		unit_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		owner_id BIGINT(20) UNSIGNED NOT NULL,
		name VARCHAR(255) NOT NULL,
		description TEXT,
		type VARCHAR(50) NOT NULL, -- e.g., boat, motorhome, cabin, flat
		capacity INT UNSIGNED NOT NULL DEFAULT 1,
		location_text VARCHAR(255),
		latitude DECIMAL(10, 8),
		longitude DECIMAL(11, 8),
		amenities TEXT, -- JSON or comma-separated
		pricing_details TEXT, -- JSON for seasonal, length-of-stay
		status VARCHAR(20) NOT NULL DEFAULT 'available', -- available, unavailable, maintenance
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (unit_id),
		KEY owner_id (owner_id),
		KEY type (type),
		KEY status (status)
	) $charset_collate;";
	dbDelta( $sql_units );

	// Table: wp_rh_bookings
	$table_name_bookings = $wpdb->prefix . 'rh_bookings';
	$sql_bookings = "CREATE TABLE $table_name_bookings (
		booking_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		unit_id BIGINT(20) UNSIGNED NOT NULL,
		user_id BIGINT(20) UNSIGNED, -- WordPress user ID if applicable
		guest_name VARCHAR(255) NOT NULL,
		guest_email VARCHAR(255) NOT NULL,
		guest_phone VARCHAR(50),
		start_date DATETIME NOT NULL,
		end_date DATETIME NOT NULL,
		adults INT UNSIGNED NOT NULL DEFAULT 1,
		children INT UNSIGNED DEFAULT 0,
		total_price DECIMAL(10, 2) NOT NULL,
		deposit_paid DECIMAL(10, 2) DEFAULT 0.00,
		balance_paid DECIMAL(10, 2) DEFAULT 0.00,
		currency VARCHAR(3) NOT NULL DEFAULT 'NOK',
		payment_status VARCHAR(20) NOT NULL DEFAULT 'pending', -- pending, partial, paid, refunded
		booking_status VARCHAR(20) NOT NULL DEFAULT 'confirmed', -- pending_payment, confirmed, cancelled, completed
		extras TEXT, -- JSON for upsell extras
		coupon_code VARCHAR(50),
		notes TEXT,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (booking_id),
		KEY unit_id (unit_id),
		KEY user_id (user_id),
		KEY start_date (start_date),
		KEY end_date (end_date),
		KEY booking_status (booking_status),
		KEY payment_status (payment_status)
	) $charset_collate;";
	dbDelta( $sql_bookings );

	// Table: wp_rh_inventory
	$table_name_inventory = $wpdb->prefix . 'rh_inventory';
	$sql_inventory = "CREATE TABLE $table_name_inventory (
		item_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		item_name VARCHAR(255) NOT NULL,
		item_type VARCHAR(50) NOT NULL, -- rentable_unit_addon, retail_stock
		sku VARCHAR(100) UNIQUE,
		quantity INT NOT NULL DEFAULT 0,
		reorder_level INT DEFAULT 0,
		supplier_info TEXT, -- JSON
		serial_numbers TEXT, -- JSON or comma-separated if using serial numbers
		fifo_details TEXT, -- JSON for FIFO tracking
		last_ordered_at DATETIME,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (item_id),
		KEY item_type (item_type)
	) $charset_collate;";
	dbDelta( $sql_inventory );

	// Table: wp_rh_owners
	$table_name_owners = $wpdb->prefix . 'rh_owners';
	$sql_owners = "CREATE TABLE $table_name_owners (
		owner_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		user_id BIGINT(20) UNSIGNED NOT NULL, -- WordPress user ID
		company_name VARCHAR(255),
		contact_email VARCHAR(255) NOT NULL,
		phone_number VARCHAR(50),
		address TEXT,
		revenue_split_rules TEXT, -- JSON
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (owner_id),
		UNIQUE KEY user_id (user_id)
	) $charset_collate;";
	dbDelta( $sql_owners );

    // Add a version option to track schema changes
    add_option('renthub_db_version', RENTHUB_VERSION);
}
register_activation_hook( __FILE__, 'renthub_activate' );

/**
 * Deactivation hook.
 *
 * Placeholder for now. We might not want to delete data on deactivation.
 */
function renthub_deactivate() {
	// Potentially clean up scheduled cron jobs or temporary data
}
register_deactivation_hook( __FILE__, 'renthub_deactivate' );

/**
 * Uninstall hook.
 *
 * Deletes custom tables and options.
 */
function renthub_uninstall() {
    global $wpdb;
    $table_names = [
        $wpdb->prefix . 'rh_units',
        $wpdb->prefix . 'rh_bookings',
        $wpdb->prefix . 'rh_inventory',
        $wpdb->prefix . 'rh_owners',
    ];

    foreach ($table_names as $table_name) {
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }

    delete_option('renthub_db_version');
    // Delete other plugin options if any
}
register_uninstall_hook(__FILE__, 'renthub_uninstall');


// Include core files
require_once RENTHUB_PLUGIN_DIR . 'includes/class-renthub-admin.php';
require_once RENTHUB_PLUGIN_DIR . 'includes/class-renthub-rest-api.php';
require_once RENTHUB_PLUGIN_DIR . 'includes/class-renthub-gutenberg.php';
// More includes as the plugin grows (e.g., for Stripe, Yale, etc.)


/**
 * Initialize the plugin.
 *
 * Loads the main classes and sets up hooks.
 */
function renthub_init() {
	// Initialize Admin features
	if ( is_admin() ) {
		new Renthub_Admin();
	}

	// Initialize REST API
	new Renthub_Rest_Api();

	// Initialize Gutenberg Blocks
	new Renthub_Gutenberg();

	// Load plugin textdomain for translations
	load_plugin_textdomain( 'renthub', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    // Other initializations
}
add_action( 'plugins_loaded', 'renthub_init' );

// Placeholder for Stripe webhook (as per project brief)
// wp_ajax_nopriv_rh_stripe_webhook
add_action( 'wp_ajax_nopriv_rh_stripe_webhook', 'renthub_stripe_webhook_handler' );
add_action( 'wp_ajax_rh_stripe_webhook', 'renthub_stripe_webhook_handler' ); // For logged-in users if needed

function renthub_stripe_webhook_handler() {
    // Stripe webhook logic will go here
    // Verify Stripe signature
    // Process event
    // Return 200 OK to Stripe
    status_header(200);
    exit;
}

// Basic logging function for development
if ( ! function_exists('rh_log') ) {
    function rh_log($message) {
        if (WP_DEBUG === true) {
            if (is_array($message) || is_object($message)) {
                error_log(print_r($message, true));
            } else {
                error_log($message);
            }
        }
    }
}

// Function to get table names
function renthub_get_table_name($name) {
    global $wpdb;
    $tables = [
        'units' => $wpdb->prefix . 'rh_units',
        'bookings' => $wpdb->prefix . 'rh_bookings',
        'inventory' => $wpdb->prefix . 'rh_inventory',
        'owners' => $wpdb->prefix . 'rh_owners',
    ];
    return $tables[$name] ?? null;
}

// End of plugin.
?>
