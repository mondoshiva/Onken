/**
 * Renthub Booking Engine Gutenberg Block - Editor Script
 */
( function( blocks, element, i18n, components, editor ) {
    const { __ } = i18n;
    const { registerBlockType } = blocks;
    const { Fragment } = element;
    const { PanelBody, ToggleControl, TextControl } = components;
    const { InspectorControls } = editor;

    registerBlockType( 'renthub/booking-engine', {
        title: __( 'Renthub Booking Engine', 'renthub' ),
        icon: 'calendar-alt',
        category: 'renthub',
        keywords: [
            __( 'booking', 'renthub' ),
            __( 'rental', 'renthub' ),
            __( 'search', 'renthub' ),
        ],
        attributes: {
            showLocationFilter: {
                type: 'boolean',
                default: true,
            },
            showDateFilter: {
                type: 'boolean',
                default: true,
            },
            showCapacityFilter: {
                type: 'boolean',
                default: true,
            },
            showAmenitiesFilter: {
                type: 'boolean',
                default: false,
            },
            defaultLocation: {
                type: 'string',
                default: '',
            },
        },

        edit: function( props ) {
            const { attributes, setAttributes } = props;
            const {
                showLocationFilter,
                showDateFilter,
                showCapacityFilter,
                showAmenitiesFilter,
                defaultLocation
            } = attributes;

            return (
                <Fragment>
                    <InspectorControls>
                        <PanelBody title={ __( 'Filter Settings', 'renthub' ) }>
                            <ToggleControl
                                label={ __( 'Show Location Filter', 'renthub' ) }
                                checked={ !!showLocationFilter }
                                onChange={ () => setAttributes( { showLocationFilter: !showLocationFilter } ) }
                            />
                            { showLocationFilter && (
                                <TextControl
                                    label={ __( 'Default Location', 'renthub' ) }
                                    value={ defaultLocation }
                                    onChange={ ( value ) => setAttributes( { defaultLocation: value } ) }
                                    help={ __( 'Optional: Pre-fill the location field.', 'renthub' ) }
                                />
                            )}
                            <ToggleControl
                                label={ __( 'Show Date Filter', 'renthub' ) }
                                checked={ !!showDateFilter }
                                onChange={ () => setAttributes( { showDateFilter: !showDateFilter } ) }
                            />
                            <ToggleControl
                                label={ __( 'Show Capacity Filter', 'renthub' ) }
                                checked={ !!showCapacityFilter }
                                onChange={ () => setAttributes( { showCapacityFilter: !showCapacityFilter } ) }
                            />
                            <ToggleControl
                                label={ __( 'Show Amenities Filter', 'renthub' ) }
                                checked={ !!showAmenitiesFilter }
                                onChange={ () => setAttributes( { showAmenitiesFilter: !showAmenitiesFilter } ) }
                                help= { __( 'Full amenities filter will be available on the frontend.', 'renthub')}
                            />
                        </PanelBody>
                    </InspectorControls>
                    <div className={ props.className }>
                        <h4>{ __( 'Renthub Booking Engine', 'renthub' ) }</h4>
                        <p>{ __( 'Displays the rental search and booking form.', 'renthub' ) }</p>
                        <p>
                            <strong>{ __( 'Active Filters (Editor Preview):', 'renthub' ) }</strong>
                        </p>
                        <ul>
                            { showLocationFilter && <li>{ __( 'Location Filter', 'renthub' ) }{ defaultLocation ? ` (Default: ${defaultLocation})` : '' }</li> }
                            { showDateFilter && <li>{ __( 'Date Filter', 'renthub' ) }</li> }
                            { showCapacityFilter && <li>{ __( 'Capacity Filter', 'renthub' ) }</li> }
                            { showAmenitiesFilter && <li>{ __( 'Amenities Filter', 'renthub' ) }</li> }
                        </ul>
                        <p><em>{ __( 'The actual booking form will be rendered on the front-end.', 'renthub' ) }</em></p>
                    </div>
                </Fragment>
            );
        },

        save: function( props ) {
            // Rendering is handled by PHP, so save returns null.
            return null;
        },
    } );
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.i18n,
    window.wp.components,
    window.wp.blockEditor // Corrected from window.wp.editor for modern WP
) );
