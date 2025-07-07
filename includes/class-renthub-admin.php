<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renthub_Admin Class.
 *
 * Handles WordPress admin area functionalities.
 */
class Renthub_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		// Add other admin hooks here
	}

	/**
	 * Add admin menu pages.
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'Renthub', 'renthub' ),
			__( 'Renthub', 'renthub' ),
			'manage_options', // Capability - adjust as needed for different roles
			'renthub',
			array( $this, 'main_dashboard_page' ),
			'dashicons-calendar-alt', // Icon
			25 // Position
		);

		add_submenu_page(
			'renthub',
			__( 'Timeline', 'renthub' ),
			__( 'Timeline', 'renthub' ),
			'manage_options', // Capability
			'renthub-timeline',
			array( $this, 'timeline_page_content' )
		);

		add_submenu_page(
			'renthub',
			__( 'Bookings', 'renthub' ),
			__( 'Bookings', 'renthub' ),
			'manage_options', // Capability
			'renthub-bookings',
			array( $this, 'bookings_page_content' )
		);

        add_submenu_page(
			'renthub',
			__( 'Units', 'renthub' ),
			__( 'Units', 'renthub' ),
			'manage_options', // Capability
			'renthub-units',
			array( $this, 'units_page_content' )
		);

        add_submenu_page(
			'renthub',
			__( 'Owners', 'renthub' ),
			__( 'Owners', 'renthub' ),
			'manage_options', // Capability - likely higher for this
			'renthub-owners',
			array( $this, 'owners_page_content' )
		);

        add_submenu_page(
			'renthub',
			__( 'Inventory', 'renthub' ),
			__( 'Inventory', 'renthub' ),
			'manage_options', // Capability
			'renthub-inventory',
			array( $this, 'inventory_page_content' )
		);

		add_submenu_page(
			'renthub',
			__( 'Settings', 'renthub' ),
			__( 'Settings', 'renthub' ),
			'manage_options', // Capability
			'renthub-settings',
			array( $this, 'settings_page_content' )
		);

        add_submenu_page(
			'renthub',
			__( 'System Health', 'renthub' ),
			__( 'System Health', 'renthub' ),
			'manage_options', // Capability
			'renthub-system-health',
			array( $this, 'system_health_page_content' )
		);
	}

	/**
	 * Main dashboard page content.
	 */
	public function main_dashboard_page() {
		echo '<h1>' . esc_html__( 'Renthub Dashboard', 'renthub' ) . '</h1>';
		echo '<p>' . esc_html__( 'Welcome to Renthub! This is the main dashboard. The Timeline will be the primary booking overview.', 'renthub' ) . '</p>';
        echo '<p>' . sprintf(
            esc_html__( 'Navigate to the %sTimeline%s.', 'renthub' ),
            '<a href="' . admin_url('admin.php?page=renthub-timeline') . '">',
            '</a>'
        ) . '</p>';
	}

	/**
	 * Timeline page content.
	 */
	public function timeline_page_content() {
		echo '<h1>' . esc_html__( 'Booking Timeline', 'renthub' ) . '</h1>';
		echo '<p>' . esc_html__( 'This page will display the Hostaway-like booking timeline.', 'renthub' ) . '</p>';
		// Placeholder for timeline component
		echo '<div id="renthub-timeline-app"></div>';
	}

    /**
	 * Bookings page content.
	 */
	public function bookings_page_content() {
		echo '<h1>' . esc_html__( 'Manage Bookings', 'renthub' ) . '</h1>';
		echo '<p>' . esc_html__( 'This page will list all bookings with filtering and actions.', 'renthub' ) . '</p>';
		// We will likely use WP_List_Table here or a custom React/Vue component
	}

    /**
	 * Units page content.
	 */
	public function units_page_content() {
		echo '<h1>' . esc_html__( 'Manage Units', 'renthub' ) . '</h1>';
		echo '<p>' . esc_html__( 'This page will allow management of rentable units.', 'renthub' ) . '</p>';
	}

    /**
	 * Owners page content.
	 */
	public function owners_page_content() {
		echo '<h1>' . esc_html__( 'Manage Owners', 'renthub' ) . '</h1>';
		echo '<p>' . esc_html__( 'This page will allow management of property owners and their settings.', 'renthub' ) . '</p>';
	}

    /**
	 * Inventory page content.
	 */
	public function inventory_page_content() {
		echo '<h1>' . esc_html__( 'Manage Inventory', 'renthub' ) . '</h1>';
		echo '<p>' . esc_html__( 'This page will allow management of inventory items (e.g. life jackets, bike racks).', 'renthub' ) . '</p>';
	}

	/**
	 * Settings page content.
	 */
	public function settings_page_content() {
		echo '<h1>' . esc_html__( 'Renthub Settings', 'renthub' ) . '</h1>';
		echo '<p>' . esc_html__( 'Configure Renthub settings here (Stripe keys, API integrations, etc.).', 'renthub' ) . '</p>';
		// Placeholder for settings form
	}

    /**
	 * System Health page content.
	 */
	public function system_health_page_content() {
		echo '<h1>' . esc_html__( 'Renthub System Health', 'renthub' ) . '</h1>';
		echo '<p>' . esc_html__( 'This page will display system status, error logs (integration with Sentry), etc.', 'renthub' ) . '</p>';
	}

	// Add other admin functionalities here
}
?>
