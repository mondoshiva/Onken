<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renthub_Rest_Api Class.
 *
 * Handles the registration of custom REST API endpoints.
 */
class Renthub_Rest_Api {

	/**
	 * Namespace for the REST API.
	 */
	protected $namespace = 'renthub/v1';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register REST API routes.
	 */
	public function register_routes() {
		// Units Endpoints
		register_rest_route( $this->namespace, '/units', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_units' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => array(), // Add args for filtering, pagination
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_unit' ),
				'permission_callback' => array( $this, 'create_item_permissions_check' ),
				'args'                => $this->get_endpoint_args_for_item_schema( true ),
			),
		) );

		register_rest_route( $this->namespace, '/units/(?P<id>[\d]+)', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_unit' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => array(
					'context' => $this->get_context_param( array( 'default' => 'view' ) ),
				),
			),
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_unit' ),
				'permission_callback' => array( $this, 'update_item_permissions_check' ),
				'args'                => $this->get_endpoint_args_for_item_schema( false ),
			),
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_unit' ),
				'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				'args'                => array(
					'force' => array(
						'default'     => false,
						'description' => __( 'Whether to bypass trash and force deletion.', 'renthub' ),
					),
				),
			),
		) );

		// Bookings Endpoints
		register_rest_route( $this->namespace, '/bookings', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_bookings' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_booking' ),
				'permission_callback' => '__return_true', // Public endpoint for booking creation initially
			),
		) );

        register_rest_route( $this->namespace, '/bookings/(?P<id>[\d]+)', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_booking' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
			),
            array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_booking' ),
				'permission_callback' => array( $this, 'update_item_permissions_check' ),
			),
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_booking' ),
				'permission_callback' => array( $this, 'delete_item_permissions_check' ),
			),
		) );

		// Owners Endpoints (Simplified for now)
        register_rest_route( $this->namespace, '/owners', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_owners' ),
				'permission_callback' => array( $this, 'manage_options_permissions_check' ), // Higher permission
			),
			// Add CREATABLE for owners if needed via API
		) );
         register_rest_route( $this->namespace, '/owners/(?P<id>[\d]+)', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_owner' ),
				'permission_callback' => array( $this, 'manage_options_permissions_check' ),
			),
            // Add EDITABLE, DELETABLE for owners if needed via API
		) );

		// TODO: Add Inventory Endpoints
		// TODO: Add other necessary endpoints (e.g., for timeline specific data, settings)
	}

	/**
	 * Permissions check for getting items.
	 */
	public function get_items_permissions_check( $request ) {
		// Allow public access for GET requests for now, or check for specific capabilities.
		// return current_user_can( 'read' );
        // For MVP, some GET endpoints might be more open, others might require specific roles.
        // Example: Viewing units might be public, viewing all bookings requires login.
		return true; // Placeholder - adjust per endpoint
	}

    /**
	 * Permissions check for getting a single item.
	 */
	public function get_item_permissions_check( $request ) {
		return $this->get_items_permissions_check( $request );
	}

	/**
	 * Permissions check for creating an item.
	 *
	 * For now, only users who can 'edit_posts' (Editor role and above) or a custom capability.
	 */
	public function create_item_permissions_check( $request ) {
		return current_user_can( 'edit_posts' ); // Placeholder - adjust with custom capabilities
	}

	/**
	 * Permissions check for updating an item.
	 */
	public function update_item_permissions_check( $request ) {
		return current_user_can( 'edit_post', $request['id'] ); // Placeholder - adjust
	}

	/**
	 * Permissions check for deleting an item.
	 */
	public function delete_item_permissions_check( $request ) {
		return current_user_can( 'delete_post', $request['id'] ); // Placeholder - adjust
	}

    /**
     * Permissions check for manage_options capability.
     */
    public function manage_options_permissions_check( $request ) {
        return current_user_can( 'manage_options' );
    }

	// --- Units Callbacks ---

	public function get_units( $request ) {
		global $wpdb;
		$table_name = renthub_get_table_name('units');
		// Add pagination, filtering logic here
		$results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY unit_id DESC LIMIT 20" );
		return new WP_REST_Response( $results, 200 );
	}

	public function get_unit( $request ) {
		global $wpdb;
		$table_name = renthub_get_table_name('units');
		$unit_id = absint( $request['id'] );
		$unit = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE unit_id = %d", $unit_id ) );
		if ( empty( $unit ) ) {
			return new WP_Error( 'renthub_not_found', __( 'Unit not found.', 'renthub' ), array( 'status' => 404 ) );
		}
		return new WP_REST_Response( $unit, 200 );
	}

	public function create_unit( $request ) {
		global $wpdb;
		$table_name = renthub_get_table_name('units');
		$params = $request->get_params();

		// Basic validation and sanitization (expand significantly)
		$data = array(
			'name'        => sanitize_text_field( $params['name'] ),
            'owner_id'    => absint( $params['owner_id'] ), // Ensure owner exists
			'type'        => sanitize_text_field( $params['type'] ),
			'capacity'    => absint( $params['capacity'] ),
            // ... other fields
		);
        // Add default values if not provided, e.g. status

		$result = $wpdb->insert( $table_name, $data );

		if ( $result === false ) {
			return new WP_Error( 'renthub_create_failed', __( 'Failed to create unit.', 'renthub' ), array( 'status' => 500 ) );
		}
		$unit_id = $wpdb->insert_id;
        $unit = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE unit_id = %d", $unit_id ) );
		return new WP_REST_Response( $unit, 201 );
	}

	public function update_unit( $request ) {
		global $wpdb;
		$table_name = renthub_get_table_name('units');
		$unit_id = absint( $request['id'] );
		$params = $request->get_params();

        // Fetch existing unit
        $existing_unit = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE unit_id = %d", $unit_id ) );
		if ( empty( $existing_unit ) ) {
			return new WP_Error( 'renthub_not_found', __( 'Unit not found for update.', 'renthub' ), array( 'status' => 404 ) );
		}

		// Prepare data for update (only update fields that are sent)
		$data = array();
        if ( isset( $params['name'] ) ) $data['name'] = sanitize_text_field( $params['name'] );
        if ( isset( $params['owner_id'] ) ) $data['owner_id'] = absint( $params['owner_id'] );
        if ( isset( $params['type'] ) ) $data['type'] = sanitize_text_field( $params['type'] );
        if ( isset( $params['capacity'] ) ) $data['capacity'] = absint( $params['capacity'] );
        // ... other fields
        $data['updated_at'] = current_time('mysql', 1);


		if ( empty($data) ) {
            return new WP_Error( 'renthub_no_data_to_update', __( 'No data provided for update.', 'renthub' ), array( 'status' => 400 ) );
        }

		$result = $wpdb->update( $table_name, $data, array( 'unit_id' => $unit_id ) );

		if ( $result === false ) {
			return new WP_Error( 'renthub_update_failed', __( 'Failed to update unit.', 'renthub' ), array( 'status' => 500 ) );
		}
        $updated_unit = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE unit_id = %d", $unit_id ) );
		return new WP_REST_Response( $updated_unit, 200 );
	}

	public function delete_unit( $request ) {
		global $wpdb;
		$table_name = renthub_get_table_name('units');
		$unit_id = absint( $request['id'] );
        // TODO: Check for related bookings before deleting, or handle cascading deletes/archiving.
		$result = $wpdb->delete( $table_name, array( 'unit_id' => $unit_id ) );

		if ( $result === false ) {
			return new WP_Error( 'renthub_delete_failed', __( 'Failed to delete unit.', 'renthub' ), array( 'status' => 500 ) );
		}
		return new WP_REST_Response( array( 'message' => __( 'Unit deleted successfully.', 'renthub' ), 'deleted' => true, 'id' => $unit_id ), 200 );
	}


	// --- Bookings Callbacks (Placeholders) ---

	public function get_bookings( $request ) {
		global $wpdb;
		$table_name = renthub_get_table_name('bookings');
        // Add filtering by date range, unit_id, status, etc. for timeline and booking management
		$results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY booking_id DESC LIMIT 20" );
		return new WP_REST_Response( $results, 200 );
	}

    public function get_booking( $request ) {
		global $wpdb;
		$table_name = renthub_get_table_name('bookings');
		$booking_id = absint( $request['id'] );
		$booking = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE booking_id = %d", $booking_id ) );
		if ( empty( $booking ) ) {
			return new WP_Error( 'renthub_not_found', __( 'Booking not found.', 'renthub' ), array( 'status' => 404 ) );
		}
		return new WP_REST_Response( $booking, 200 );
	}

	public function create_booking( $request ) {
        global $wpdb;
        $table_name = renthub_get_table_name('bookings');
        $params = $request->get_params();

        // **Crucial: Add validation for booking conflicts here or in a separate service class**
        // Example: Check if unit_id is available for start_date and end_date.
        // This will be a complex piece of logic.

        // Basic validation and sanitization
		$data = array(
			'unit_id'       => absint( $params['unit_id'] ),
            'guest_name'    => sanitize_text_field( $params['guest_name'] ),
            'guest_email'   => sanitize_email( $params['guest_email'] ),
            'start_date'    => sanitize_text_field( $params['start_date'] ), // Further validation for date format
            'end_date'      => sanitize_text_field( $params['end_date'] ),   // Further validation for date format
            'total_price'   => floatval( $params['total_price'] ), // Price calculation logic will be complex
            'currency'      => isset($params['currency']) ? sanitize_text_field( $params['currency'] ) : 'NOK',
            'booking_status'=> 'pending_payment', // Initial status
            // ... other fields
		);

        // Ensure start_date < end_date, unit exists, etc.

        $result = $wpdb->insert( $table_name, $data );

		if ( $result === false ) {
			return new WP_Error( 'renthub_booking_failed', __( 'Failed to create booking.', 'renthub' ), array( 'status' => 500 ) );
		}
		$booking_id = $wpdb->insert_id;
        $booking = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE booking_id = %d", $booking_id ) );

        // TODO: Trigger Stripe payment flow here if applicable for MVP (or return data to client to initiate)
        // TODO: Send confirmation email (will be part of automated messaging)

		return new WP_REST_Response( $booking, 201 );
	}

    public function update_booking( $request ) {
        global $wpdb;
		$table_name = renthub_get_table_name('bookings');
		$booking_id = absint( $request['id'] );
		$params = $request->get_params();

        $existing_booking = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE booking_id = %d", $booking_id ) );
		if ( empty( $existing_booking ) ) {
			return new WP_Error( 'renthub_not_found', __( 'Booking not found for update.', 'renthub' ), array( 'status' => 404 ) );
		}

        // Data to update
        $data = array();
        // Example: Allow updating status, notes, payment details by admin/manager
        if ( isset( $params['booking_status'] ) ) $data['booking_status'] = sanitize_text_field( $params['booking_status'] );
        if ( isset( $params['payment_status'] ) ) $data['payment_status'] = sanitize_text_field( $params['payment_status'] );
        if ( isset( $params['notes'] ) ) $data['notes'] = sanitize_textarea_field( $params['notes'] );
        // Recalculate price if dates/unit change? This is complex.
        // **Add conflict checks if start_date, end_date, or unit_id are changed.**
        $data['updated_at'] = current_time('mysql', 1);

        if ( empty($data) ) {
            return new WP_Error( 'renthub_no_data_to_update', __( 'No data provided for booking update.', 'renthub' ), array( 'status' => 400 ) );
        }

        $result = $wpdb->update( $table_name, $data, array( 'booking_id' => $booking_id ) );

        if ( $result === false ) {
			return new WP_Error( 'renthub_update_failed', __( 'Failed to update booking.', 'renthub' ), array( 'status' => 500 ) );
		}
        $updated_booking = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE booking_id = %d", $booking_id ) );
		return new WP_REST_Response( $updated_booking, 200 );
    }

    public function delete_booking( $request ) {
        global $wpdb;
		$table_name = renthub_get_table_name('bookings');
		$booking_id = absint( $request['id'] );
        // Instead of delete, usually set status to 'cancelled'
        // $result = $wpdb->delete( $table_name, array( 'booking_id' => $booking_id ) );
        $result = $wpdb->update( $table_name,
            array('booking_status' => 'cancelled', 'updated_at' => current_time('mysql', 1)),
            array('booking_id' => $booking_id)
        );


		if ( $result === false ) {
			return new WP_Error( 'renthub_cancel_failed', __( 'Failed to cancel booking.', 'renthub' ), array( 'status' => 500 ) );
		}
        // TODO: Process refund if applicable through Stripe

		return new WP_REST_Response( array( 'message' => __( 'Booking cancelled successfully.', 'renthub'), 'cancelled' => true, 'id' => $booking_id ), 200 );
    }


    // --- Owners Callbacks (Placeholders) ---
    public function get_owners( $request ) {
        global $wpdb;
		$table_name = renthub_get_table_name('owners');
		$results = $wpdb->get_results( "SELECT owner_id, user_id, company_name, contact_email FROM $table_name ORDER BY owner_id DESC" ); // Avoid sending sensitive data like revenue rules by default
		return new WP_REST_Response( $results, 200 );
    }

    public function get_owner( $request ) {
        global $wpdb;
		$table_name = renthub_get_table_name('owners');
        $owner_id = absint( $request['id'] );
		$owner = $wpdb->get_row( $wpdb->prepare( "SELECT owner_id, user_id, company_name, contact_email, phone_number, address FROM $table_name WHERE owner_id = %d", $owner_id ) );
        if ( empty( $owner ) ) {
			return new WP_Error( 'renthub_not_found', __( 'Owner not found.', 'renthub' ), array( 'status' => 404 ) );
		}
		return new WP_REST_Response( $owner, 200 );
    }


	/**
	 * Get endpoint args for item schema.
	 * This is a placeholder and should be replaced with actual schema definitions
	 * for each CPT or data type if you were using WP CPTs.
	 * For custom tables, you define what's creatable/editable.
	 */
	protected function get_endpoint_args_for_item_schema( $is_creating = true ) {
		$args = array(
			// Define common arguments for your items here
			// Example for 'units'
            'name' => array(
                'required' => $is_creating,
                'type' => 'string',
                'description' => __('Name of the unit', 'renthub'),
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'owner_id' => array(
                'required' => $is_creating, // Or could be set automatically based on current user
                'type' => 'integer',
                'description' => __('Owner ID of the unit', 'renthub'),
                'sanitize_callback' => 'absint',
            ),
            'type' => array(
                'required' => $is_creating,
                'type' => 'string',
                'description' => __('Type of the unit (e.g., boat, cabin)', 'renthub'),
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'capacity' => array(
                'required' => false,
                'type' => 'integer',
                'description' => __('Capacity of the unit', 'renthub'),
                'sanitize_callback' => 'absint',
                'default' => 1,
            ),
            // Add all other fields from wp_rh_units table schema
		);
		return $args;
	}

    /**
	 * Get the context param.
	 *
	 * @param  array $args Optional. Additional arguments for context parameter. Default empty array.
	 * @return array Context parameter argument options.
	 */
	protected function get_context_param( $args = array() ) {
		$param_details = array(
			'description' => __( 'Scope under which the request is made; determines fields present in response.', 'renthub' ),
			'type'        => 'string',
			'default'     => 'view',
			'enum'        => array(
				'view',
				'embed',
				'edit',
			),
		);
		return array_merge( $param_details, $args );
	}
}
?>
