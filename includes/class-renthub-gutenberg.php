<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renthub_Gutenberg Class.
 *
 * Handles Gutenberg block registration and rendering.
 */
class Renthub_Gutenberg {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_blocks' ) );
		// Potentially enqueue block assets for editor and front-end
        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
        // add_action( 'enqueue_block_assets', array( $this, 'enqueue_frontend_assets' ) ); // If frontend JS/CSS is specific to the block
	}

	/**
	 * Register Gutenberg blocks.
	 */
	public function register_blocks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			// Gutenberg is not active.
			return;
		}

		// Register Booking Engine Block
		register_block_type( 'renthub/booking-engine', array(
			'editor_script'   => 'renthub-booking-engine-editor-script', // Handle for the editor script
			// 'editor_style'    => 'renthub-booking-engine-editor-style',  // Handle for the editor style
			// 'style'           => 'renthub-booking-engine-style',         // Handle for the front-end style
			'render_callback' => array( $this, 'render_booking_engine_block' ),
			'attributes'      => array(
                // Define attributes for the block
                'showLocationFilter' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'showDateFilter' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'showCapacityFilter' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'showAmenitiesFilter' => array(
                    'type' => 'boolean',
                    'default' => false,
                ),
                'defaultLocation' => array(
                    'type' => 'string',
                    'default' => '',
                ),
                // Add more attributes as needed
            ),
            'category'        => 'renthub', // Custom category
		) );

        // Potentially register a custom block category
        add_filter( 'block_categories_all', function ( $categories, $post ) {
            return array_merge(
                $categories,
                array(
                    array(
                        'slug'  => 'renthub',
                        'title' => __( 'Renthub Blocks', 'renthub' ),
                    ),
                )
            );
        }, 10, 2 );

        // Shortcode for legacy support
        add_shortcode( 'renthub_booking_engine', array( $this, 'render_booking_engine_shortcode' ) );
	}

    /**
     * Enqueue block editor assets.
     */
    public function enqueue_editor_assets() {
        // This is where you would enqueue the JavaScript and CSS for the block's editor interface
        // For a real block, this JS would be compiled from React/Vue/etc.
        wp_enqueue_script(
            'renthub-booking-engine-editor-script',
            RENTHUB_PLUGIN_URL . 'assets/js/editor/booking-engine-block.js', // Placeholder path
            array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-editor', 'wp-components' ), // Dependencies
            RENTHUB_VERSION,
            true // In footer
        );

        // Example: wp.blocks.registerBlockType( 'renthub/booking-engine', { title: 'Renthub Booking', icon: 'calendar-alt', category: 'renthub', edit: function() { return <p>Booking Engine Editor</p>; }, save: function() { return null; } } );
        // The actual block JS would be more complex, likely using JSX.
    }


	/**
	 * Render the Booking Engine block.
	 *
	 * @param array $attributes Block attributes.
	 * @return string HTML content of the block.
	 */
	public function render_booking_engine_block( $attributes ) {
		// This function will generate the front-end HTML for the booking engine.
		// It will likely involve:
		// - Search filters (location, dates, capacity, amenities)
		// - Display of available units
		// - Integration with Stripe for payments

        // For MVP, this could be a simple placeholder that loads a React/Vue app,
        // or it could render server-side HTML with JS for interactivity.

		ob_start();
		?>
		<div class="renthub-booking-engine-wrapper" data-attributes="<?php echo esc_attr(json_encode($attributes)); ?>">
			<h2><?php esc_html_e( 'Find Your Rental', 'renthub' ); ?></h2>
			<form id="renthub-booking-form">
                <?php if ( ! empty( $attributes['showLocationFilter'] ) && $attributes['showLocationFilter'] ) : ?>
				<div class="form-group">
					<label for="rh-location"><?php esc_html_e( 'Location', 'renthub' ); ?></label>
					<input type="text" id="rh-location" name="rh_location" placeholder="<?php esc_attr_e( 'e.g., Oslo, Geirangerfjord', 'renthub' ); ?>" value="<?php echo esc_attr($attributes['defaultLocation'] ?? ''); ?>">
				</div>
                <?php endif; ?>

                <?php if ( ! empty( $attributes['showDateFilter'] ) && $attributes['showDateFilter'] ) : ?>
				<div class="form-group">
					<label for="rh-start-date"><?php esc_html_e( 'Check-in', 'renthub' ); ?></label>
					<input type="date" id="rh-start-date" name="rh_start_date">
				</div>
				<div class="form-group">
					<label for="rh-end-date"><?php esc_html_e( 'Check-out', 'renthub' ); ?></label>
					<input type="date" id="rh-end-date" name="rh_end_date">
				</div>
                <?php endif; ?>

                <?php if ( ! empty( $attributes['showCapacityFilter'] ) && $attributes['showCapacityFilter'] ) : ?>
				<div class="form-group">
					<label for="rh-capacity"><?php esc_html_e( 'Guests', 'renthub' ); ?></label>
					<input type="number" id="rh-capacity" name="rh_capacity" min="1" value="1">
				</div>
                <?php endif; ?>

                <?php if ( ! empty( $attributes['showAmenitiesFilter'] ) && $attributes['showAmenitiesFilter'] ) : ?>
                <div class="form-group">
					<label><?php esc_html_e( 'Amenities', 'renthub' ); ?></label>
                     ફિલ્ટર<!-- Placeholder for amenities checkboxes/multiselect -->
                    <p><em><?php esc_html_e( 'Amenities filter will be implemented here.', 'renthub' ); ?></em></p>
				</div>
                <?php endif; ?>

				<button type="submit" class="button button-primary"><?php esc_html_e( 'Search', 'renthub' ); ?></button>
			</form>
			<div id="renthub-search-results">
				<!-- Search results will be loaded here via AJAX -->
                <p><em><?php esc_html_e( 'Search results will appear here.', 'renthub' ); ?></em></p>
			</div>
            <div id="renthub-booking-details" style="display:none;">
                <!-- Booking form for selected unit and payment will appear here -->
            </div>
		</div>
        <style>
            /* Basic styling for the form - can be moved to a separate CSS file */
            .renthub-booking-engine-wrapper .form-group { margin-bottom: 15px; }
            .renthub-booking-engine-wrapper label { display: block; margin-bottom: 5px; }
            .renthub-booking-engine-wrapper input[type="text"],
            .renthub-booking-engine-wrapper input[type="date"],
            .renthub-booking-engine-wrapper input[type="number"] { width: 100%; padding: 8px; box-sizing: border-box; }
        </style>
        <script>
        // Basic inline script for placeholder functionality
        // In a real app, this would be a more robust JS application (React, Vue, etc.)
        // or handled by enqueued JS files.
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('renthub-booking-form');
            if (form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    // Placeholder for search logic
                    const resultsDiv = document.getElementById('renthub-search-results');
                    resultsDiv.innerHTML = '<p><em>Searching... (AJAX call to REST API will happen here)</em></p>';
                    // Simulate API call
                    setTimeout(function() {
                        resultsDiv.innerHTML = '<p><em>No units found for your criteria (this is a placeholder).</em></p>';
                        // Show booking details placeholder for demo
                        // document.getElementById('renthub-booking-details').style.display = 'block';
                    }, 1500);
                });
            }
        });
        </script>
		<?php
		return ob_get_clean();
	}

    /**
     * Render the Booking Engine via shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML content for the shortcode.
     */
    public function render_booking_engine_shortcode( $atts ) {
        // Convert shortcode atts to block attributes if necessary
        $default_attributes = array(
            'showLocationFilter' => true,
            'showDateFilter' => true,
            'showCapacityFilter' => true,
            'showAmenitiesFilter' => false,
            'defaultLocation' => '',
        );

        // Normalize boolean attributes from shortcode
        if (isset($atts['showlocationfilter'])) {
            $atts['showLocationFilter'] = filter_var($atts['showlocationfilter'], FILTER_VALIDATE_BOOLEAN);
        }
        if (isset($atts['showdatefilter'])) {
            $atts['showDateFilter'] = filter_var($atts['showdatefilter'], FILTER_VALIDATE_BOOLEAN);
        }
        if (isset($atts['showcapacityfilter'])) {
            $atts['showCapacityFilter'] = filter_var($atts['showcapacityfilter'], FILTER_VALIDATE_BOOLEAN);
        }
         if (isset($atts['showamenitiesfilter'])) {
            $atts['showAmenitiesFilter'] = filter_var($atts['showamenitiesfilter'], FILTER_VALIDATE_BOOLEAN);
        }
        if (isset($atts['defaultlocation'])) {
            $atts['defaultLocation'] = sanitize_text_field($atts['defaultlocation']);
        }


        $attributes = shortcode_atts( $default_attributes, $atts, 'renthub_booking_engine' );
        return $this->render_booking_engine_block( $attributes );
    }

	// Add methods for other blocks if any
}

?>
