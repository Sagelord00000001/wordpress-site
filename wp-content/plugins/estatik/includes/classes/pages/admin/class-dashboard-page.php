<?php

/**
 * Class Es_Dashboard_Page.
 */
class Es_Dashboard_Page {

	public static function es_posts_timeout_extend() {
		return 120;
	}

    /**
     * Get estatik.net articles.
     *
     * @return bool|array
     */
    public static function get_posts() {
	    add_filter( 'http_request_timeout', array( 'Es_Dashboard_Page', 'es_posts_timeout_extend' ) );

        $response = wp_remote_get( 'https://estatik.net/wp-json/wp/v2/posts?_fields=modified,link,title&per_page=10' );

		remove_filter( 'http_request_timeout', array( 'Es_Dashboard_Page', 'es_posts_timeout_extend' ) );

        // Exit if error.
        if ( is_wp_error( $response ) ) {
            return false;
        }

        // Get the body.
        return json_decode( wp_remote_retrieve_body( $response ) );
    }

	/**
	 * Changelog info.
	 *
	 * @return array[]
	 */
	public static function get_changelog() {
		return array(
			'4.1.6' => array(
				'date' => _x( 'August, 12, 2024', 'changelog', 'es' ),
				'changes' => array(
					array(
						'text' => _x( 'New field "Field Description" added (PRO & Premium)', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Ability to add logical operator to checkboxes added (All versions)', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Display of "Rent Period" field has been corrected for agents (PRO & Premium)', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'New option "Open collapsed filters" added (All versions)', 'changelog', 'es' ),
						'label' => 'new',
					),

					array(
						'text' => _x( 'Display of archive page "tags" fixed (All versions)', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Issues with taxonomy pages fixed (All versions)', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Issue with disappearing images in gallery when duplicating fixed (All versions)', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'User geolocation in contact form fetching fixed (All versions)', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Pagination on archives has been fixed (All versions)', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'The problem with displaying images on mobile devices has been fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Problem with loading elementor for shortcode [‘es-property-map’] fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
				),
			),
			'4.1.5' => array(
				'date' => _x( 'July, 04, 2024', 'changelog', 'es' ),
				'changes' => array(
					array(
						'text' => _x( 'Registered agent confirmation functionality added (PRO & Premium)', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Field "Visible on the front" enhanced (All versions)', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Agency creation in subscription agent account added (PRO & Premium)', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Authors attribute added to shortcode [es_my_listing] (All versions)', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Translations creation for properties in agent\'s account added (only for Polylang) (PRO & Premium)', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Time offset setting for Open House added to MLS (Premium)', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Sorting fixed (All versions)', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Listing filter fixed (All versions)', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'List display corrected (All versions)', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Layout saving after screen reboot fixed (All versions)', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
				),
			),
			'4.1.4' => array(
				'date' => _x( 'May, 22, 2024', 'changelog', 'es' ),
				'changes' => array(
					array(
						'text' => _x( 'Languages with non-Latin alphabets support added (All versions)', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Location saving functionality for [es_my_listing] shortcode added (PRO & Premium)', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( '"Clear all filters" button to expanded filter in mobile version added (All versions)', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Listing limit notification to agent\'s profile added (PRO & Premium)', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Polylang and WPML support for custom fields added (All versions)', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Option to change Subject field for "Saved Search" email added (PRO & Premium)', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Italian language files updated (All versions)', 'changelog', 'es' ),
						'label' => 'new',
					),


					array(
						'text' => _x( 'Agent Logout link fixed  (PRO & Premium)', 'changelog', 'es' ),
						'label' => 'fix',
					),
					array(
						'text' => _x( 'Automatic data input during authorization fixed  (All versions)', 'changelog', 'es' ),
						'label' => 'fix',
					),
					array(
						'text' => _x( 'Saving address (Google Maps) issue fixed  (All versions)', 'changelog', 'es' ),
						'label' => 'fix',
					),
					array(
						'text' => _x( '"Call for price" option cancellation upon saving listing fixed  (All versions)', 'changelog', 'es' ),
						'label' => 'fix',
					),
					array(
						'text' => _x( 'Number-type filter values reset functionality improved  (All versions)', 'changelog', 'es' ),
						'label' => 'fix',
					),
					array(
						'text' => _x( 'Bulk selection issue in admin panel fixed  (All versions)', 'changelog', 'es' ),
						'label' => 'fix',
					),
					array(
						'text' => _x( 'Filter functionality for resetting categories fixed  (All versions)', 'changelog', 'es' ),
						'label' => 'fix',
					),
					array(
						'text' => _x( 'MLS URL modification for existing profiles blocked (Premium)', 'changelog', 'es' ),
						'label' => 'fix',
					),
					array(
						'text' => _x( 'Pause functionality for hidden automatic import fixed (Premium)', 'changelog', 'es' ),
						'label' => 'fix',
					),
					array(
						'text' => _x( 'WEB API access token regeneration issue fixed (Premium)', 'changelog', 'es' ),
						'label' => 'fix',
					),
				),
			),
			'4.1.3' => array(
				'date' => _x( 'April, 08, 2024', 'changelog', 'es' ),
				'changes' => array(
					array(
						'text' => _x( 'Extra price input format in search added.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Request form fields order changed.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( '“Homes” to “properties” switched in pagination.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'For subscription agents, the functionality for adding and removing listings corrected.', 'changelog', 'es' ),
						'label' => 'fix',
					),
					array(
						'text' => _x( 'Logic for sorting listings refactored.', 'changelog', 'es' ),
						'label' => 'fix',
					),
					array(
						'text' => _x( '“Number” type filter issue fixed.', 'changelog', 'es' ),
						'label' => 'fix',
					),
					array(
						'text' => _x( 'Agent saving issue fixed.', 'changelog', 'es' ),
						'label' => 'fix',
					),
					array(
						'text' => _x( 'Agent profile links issue fixed.', 'changelog', 'es' ),
						'label' => 'fix',
					),
					array(
						'text' => _x( 'The issue with incorrect translations has been fixed.', 'changelog', 'es' ),
						'label' => 'fix',
					),
				),
			),
			'4.1.2' => array(
				'date' => _x( 'March, 01, 2024', 'changelog', 'es' ),
				'changes' => array(
					array(
						'text' => _x( 'Added MLS Classes support for raprets MLS Provider', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'MLS Actualizer refactored', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'MLS Automatic import refactored', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Added collapsed description option for agent & agency single page', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Neighborhoods added in Data Manager', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'WP All Import property keywords generation added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Plugin DB migration refactored', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Duplicated taxonomies in admin property form fix added', 'changelog', 'es' ),
						'label' => 'fix',
					),
					array(
						'text' => _x( 'MLS multiple values saving fix added', 'changelog', 'es' ),
						'label' => 'fix',
					),
					array(
						'text' => _x( 'MLS Grid requests issue fix added', 'changelog', 'es' ),
						'label' => 'fix',
					),
					array(
						'text' => _x( 'Optimized locations saving', 'changelog', 'es' ),
						'label' => 'fix',
					),
					array(
						'text' => _x( 'Switcher field in search form fix added', 'changelog', 'es' ),
						'label' => 'fix',
					),
					array(
						'text' => _x( 'Minor fixes', 'changelog', 'es' ),
						'label' => 'fix',
					),
				),
			),
			'4.1.1' => array(
				'date' => _x( 'December, 13, 2023', 'changelog', 'es' ),
				'changes' => array(
					array(
						'text' => _x( 'MLS media re-import implemented.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'MLS entity popup upgraded.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'MLS Actualizer new hooks added.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Added option for disable property carousel.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Implemented MLS import for dropdown, checkboxes fields types.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Added url slugs for plugin taxonomies.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Added recaptcha for Login and Reset password forms.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Added map_show="all" attribute for es_my_listing shortcode with half map layout.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Added option for change Open House time format.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Implemented values converter for 0,1 MLS values.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Implemented new MLS operator From -> To.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'New "link" FB field type added.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Plugin security upgraded.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Added scripts & styles versions in enqueue\register functions.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'MLS import entities count fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Twitter icon changed to X.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Lookup values retrieving fix added for Web API.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Google Auth fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Search form labels fixed.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Fixed property images in saved searches email.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Plugin translations added dictionaries.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Listings alax loading animation fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Property addresses saving fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Show more link fixed for Features section on single property page.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Reset search button fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'MLS sync not_equal operator fixed.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Request notes fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Agent avatar fixed.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Social sharing option switcher fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),

					array(
						'text' => _x( 'No min, No max labels translation fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'FB fields & sections saving fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Floor plans render fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Multiple estatik elementor widgets saving fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Minor fixes', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
				),
			),
			'4.1.0' => array(
				'date' => _x( 'September, 03, 2023', 'changelog', 'es' ),
				'changes' => array(
					array(
						'text' => _x( 'MLS import & sync refactored.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Property meta icons refactored.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Log out link added on user profile page for mobile devices.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Added new email settings fields.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'MLS Realtyserver autoconfig added.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Added search support for es_parking, es_roof etc fields.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'strict_address param added for es_my_listing shortcode.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Back button on compare page css fix added.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'RTL support added for all of slick sliders.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Option for disable geolocation of request form added.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Auto assign agent to agency implemented for MLS Import.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Added zoom level option for single property page map.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Polylang integrated for breadcrumbs links on single property page.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Reset search button refactored.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'es_get_contact_link function php warning fixed.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Paragonapi WebApi autoconfig added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Map zoom saving fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Predefined values for search form fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Tel field php warning fixed.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Buyers migration fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Plugin migration fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Unlimited subscription plans fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Appointments deletion fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Subscription plans ID generation fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Field builder fixes added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Fixed plugin emails fatal error.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Data manager fixes added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Subscription labels fixes added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Properties slider fixes added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Responsive fixed for single property page.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Recaptcha fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Slick slider in flexbox container fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Added video field import support for WP All Import plugin.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Map popup fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Price format fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'MLS import video fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Fixed location fields in search form.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Minor fixes', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
				),
			),
			'4.0.7' => array(
				'date' => _x( 'June, 24, 2023', 'changelog', 'es' ),
				'changes' => array(
					array(
						'text' => _x( 'Pure autoconfig for MLSAligned, harmls MLS provider added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'FB support for video section in single property added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Appsecret_proof for facebook auth added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Default archive page disable option added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Request form default message deleted', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Google fonts cache optimized', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'mls_resource and mls_class attributes to es_my_listing shortcode added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Translation support for multiple values in FB added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Drop-down fields placeholder to search form added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Currency code display support added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Meta icons loading in property box optimized', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Import limits in MLS schedules added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Sorting by labels implemented', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Search form autocomplete support for listings addresses added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Switcher field values changed to Yes / No', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Neighborhood field in property management added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'WP All Import address components generation added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'White label for admin plugin area improved', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( '[es_property_map] shortcode added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'HTML editor field in FB added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Search from Select2 fields CSS + JS fix added', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Compare listings container fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Property video section CSS sizes fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'URL field type fixed.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Listings duplication for crea ddf preventive fix added', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Agents order in Request form fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'es_the_formatted_field $before, $after variables for empty value fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'ReCaptcha for AJAX request forms fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'JSON_UNESCAPED_UNICODE to json_encode for address components added', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Properties removal actualizer fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Search form range fields fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Taxonomy archive title fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Comma separation for ‘city’ values in es_my_listing shortcode fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Price variation in search form fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Property sections translation bug fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Scroll top animation after using pagination - fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Profile single request info page fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'es_get_agents_list function allowed memory size error fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Incorrect MLS credentials fatal error fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Contacts field fatal error fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Data Manager Icon uploader bug fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Polylang migration fatal error fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Locations breadcrumbs bug fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Migration progress bar fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
				),
			),
			'4.0.6' => array(
				'date' => _x( 'April, 17, 2023', 'changelog', 'es' ),
				'changes' => array(
					array(
						'text' => _x( 'appsecret_proof argument added for Facebook auth.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'MLS mlsaligned provider integrated.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Fields builder request form section message setting added.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Reset option for MLS sync fields added.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'City field set as multiple in estatik search widget.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Tags field support added for listings shortcode.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Range mode for custom numeric fb fields implemented.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Added bulk actions for admin requests archive.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Select2 for elementor popups fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Properties carousel vertical layout fixed.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Search widget locations fields fix added for elementor popups.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Fields builder fields deletion fix added for PDF brochure.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Duplicated machine name for fb fields & sections fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Agent tel saving fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Status field for properties map fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Property SVG icon color fixed.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'FB fields translations fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Admin properties archive pagination fixed.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'User profile avatar fixed.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Agent description css fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Rooms functionality fixes added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Admin widgets broken page fixed.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Property file fields saving fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Minor fixes.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
				),
			),
			'4.0.5' => array(
				'date' => _x( 'February, 25, 2023', 'changelog', 'es' ),
				'changes' => array(
					array(
						'text' => _x( 'Title field for MLS Profiles added.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Option for disable saved search functionality added.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'New settings tab for manage user profile added.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'MLS Web API classes added.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Raprets MLS provider media support added.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Plugin fonts uploading fixed.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'file_get_contents function for retrieve SVG content deleted.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'prop_id attribute added for [es_my_listing shortcode]', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Multiple support for select2 fields in estatik framework added.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'No min, No max labels changed to min,max in search form.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'es_before_single_wrapper, es_after_single_wrapper actions for estatik single templates added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Full width field setting in fields builder form added.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Deletion of child automatic imports implemented.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'MLSGrid v2 provider autoconfig added.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'German translations modified.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Property management form buttons css fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'CSS fix for MLS password field added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Agent & agencies enabler switcher fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Elementor listings widget filter fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Fields builder translations fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Fixed fields builder special characters for machine_name.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'file_type warning fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Property mobile gallery css fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Login form prefilled fields submit button fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Search php warning fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Fixed price field formatter.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Incorrect top margin in property gallery lightbox removed.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Fixed search form selected values labels.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Duplicated label for phone field deleted.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Openhouse fields FB fix added', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
				),
			),
			'4.0.4' => array(
				'date' => _x( 'January, 27, 2023', 'changelog', 'es' ),
				'changes' => array(
					array(
						'text' => _x( 'Added new option for disable tel country code field.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Added new attribute named "default" in [es_property_field] shortcode for empty property fields.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Implemented agents registration confirmation email.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Set dynamic content disabled by default.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Deleted formatters for bathrooms, bedrooms fields on single property page.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Added new plugin translations.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Added new settings for manage PDF fields in Fields Builder.', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Fixed images uploading via front property management.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Property management agent assignment fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Fixed search fields order in Elementor search form widget.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Google maps callback error fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Fixed property quick edit form agents saving.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Fixed comments saving PHP warning.', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Fixed deactivated sections render.', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Fixed breadcrumbs locations order.', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Fixed property price spaces.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Fixed duplicated HTML input IDs in DOM.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Recaptcha fix added.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Fixed slick slider initializing for property boxes.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Fixed search widget location fields loading for non authorised users.', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Fixed MLS automatic import table render', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
				),
			),
			'4.0.3' => array(
				'date' => _x( 'December, 25, 2022', 'changelog', 'es' ),
				'changes' => array(
					array(
						'text' => _x( 'Captcha issues fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'FB tab fields issues fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Single property pages mobile layout fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'MLS ID display bug fixed', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Translation for sorting fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
				),
			),
			'4.0.2' => array(
				'date' => _x( 'November, 30, 2022', 'changelog', 'es' ),
				'changes' => array(
					array(
						'text' => _x( 'Lazy load for carousel images added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Google fonts GDPR issue fixed', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Request Info form (subject and from email issue fixed)', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'SEO issues fixed', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'Responsive js refactored', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
				),
			),
			'4.0.1' => array(
				'date' => '',
				'changes' => array(
					array(
						'text' => _x( 'Added min & max map zoom setting fields', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Polylang support added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'MLS migration fix added', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
				),
			),
			'4.0.0' => array(
				'date' => '',
				'changes' => array(
					array(
						'text' => _x( 'Front-and back-end interface design updated', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Agencies support added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'One-time payments added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Compare feature added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Buyer\'s & agent\'s profiles upgraded', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Requests to profile added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'AJAX map search added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Fields Builder considerably improved', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Data Manager improved', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'WP ALL Import support added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'New widgets added: agencies, locations, slideshow widget', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Share via email added', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Elementor Support improved', 'changelog', 'es' ),
						'label' => 'new',
					),
					array(
						'text' => _x( 'Loads of minor fixes and improvements', 'changelog', 'es' ),
						'label' => 'new',
					),
				),
			),
			'3.11.14' => array(
				'date' => _x( 'July, 26, 2022', 'changelog date', 'es' ),
				'changes' => array(
					array(
						'text' => _x( 'Estatik settings php warning fixed (All versions)', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'PDF library fixed (Pro & Premium)', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
					array(
						'text' => _x( 'minor fixes', 'changelog', 'es' ),
						'label' => 'bugfix',
					),
				),
			),
		);
	}

    /**
     * @return array
     */
    public static function get_links() {
        return apply_filters( 'es_dashboard_get_links', array(
            'my-listings' => array(
                'name' => __( 'My listings', 'es' ),
                'url' => admin_url( 'edit.php?post_type=properties' ),
                'icon' => '<span class="es-icon es-icon_home es-icon--rounded es-icon--green"></span>',
            ),
            'settings' => array(
                'name' => __( 'Settings', 'es' ),
                'url' => admin_url( 'admin.php?page=es_settings' ),
                'icon' => '<span class="es-icon es-icon_settings es-icon--rounded es-icon--green"></span>',
            ),
            'fields-builder' => array(
                'name' => __( 'Fields builder', 'es' ),
                'url' => admin_url( 'admin.php?page=es_fields_builder' ),
                'icon' => '<span class="es-icon es-icon_apps es-icon--rounded es-icon--green"></span>',
            ),
            'add-new' => array(
                'name' => __( 'Add new property', 'es' ),
                'url' => admin_url( 'post-new.php?post_type=properties' ),
                'icon' => '<span class="es-icon es-icon_plus es-icon--rounded es-icon--green"></span>',
            ),
            'shortcodes' => array(
                'name' => __( 'Shortcodes', 'es' ),
                'url' => 'https://estatik.net/docs-category/shortcodes/',
                'icon' => '<span class="es-icon es-icon_shortcode es-icon--rounded es-icon--green"></span>',
            ),
            'agents' => array(
                'name' => __( 'Agents', 'es' ),
                'label' => '<span class="es-label es-label--green">' . __( 'PRO', 'es' ) . '</span>',
                'url' => '#',
                'icon' => '<span class="es-icon es-icon_glasses es-icon--rounded es-icon--green"></span>',
                'disabled' => true,
            ),
            'agencies' => array(
                'name' => __( 'Agencies', 'es' ),
                'label' => '<span class="es-label es-label--green">' . __( 'PRO', 'es' ) . '</span>',
                'url' => '#',
                'icon' => '<span class="es-icon es-icon_case es-icon--rounded es-icon--green"></span>',
                'disabled' => true,
            ),
            'rets-import' => array(
                'name' => __( 'MLS Import', 'es' ),
                'label' => '<span class="es-label es-label--orange">' . __( 'Premium', 'es' ) . '</span>',
                'url' => '#',
                'icon' => '<span class="es-icon es-icon_cloud-connect es-icon--rounded es-icon--green"></span>',
                'disabled' => true,
            ),
        ) );
    }

    /**
     * @return array
     */
    public static function get_carousel_items() {
        return array(
            'estatik-native' => array(
                'link' => 'https://estatik.net/product/theme-native/',
                'name' => __( 'Native Theme', 'es' ),
                'demo_link' => 'http://native.estatik.net/',
                'image_url' => ES_PLUGIN_URL . 'admin/images/native.png',
                'free' => true,
            ),
            'estatik-trendy' => array(
                'link' => 'https://estatik.net/product/theme-trendy-estatik-pro/',
                'name' => __( 'Trendy Theme', 'es' ),
                'demo_link' => 'http://trendy.estatik.net/',
                'image_url' => ES_PLUGIN_URL . 'admin/images/portal.png',
            ),
            'estatik-project' => array(
                'link' => 'https://estatik.net/product/estatik-project-theme/',
                'name' => __( 'Project Theme', 'es' ),
                'demo_link' => 'http://project.estatik.net/',
                'image_url' => ES_PLUGIN_URL . 'admin/images/portal.png',
            ),
            'estatik-portal' => array(
                'link' => 'https://estatik.net/product/portal-theme/',
                'name' => __( 'Portal Theme', 'es' ),
                'demo_link' => 'http://portal.estatik.net/',
                'image_url' => ES_PLUGIN_URL . 'admin/images/portal.png',
            ),
            'estatik-realtor' => array(
                'link' => 'https://estatik.net/product/estatik-realtor-theme/',
                'name' => __( 'Realtor Theme', 'es' ),
                'demo_link' => 'http://realtor.estatik.net/',
                'image_url' => ES_PLUGIN_URL . 'admin/images/realtor.png',
            ),
            'mortgage-calc' => array(
                'link' => 'https://estatik.net/product/estatik-mortgage-calculator/',
                'name' => __( 'Mortgage Calculator', 'es' ),
                'demo_link' => '',
                'image_url' => ES_PLUGIN_URL . 'admin/images/portal.png',
                'free' => true,
            ),
        );
    }

    /**
     * @return array
     */
    public static function get_services() {
        return array(
            array(
                'link' => 'https://estatik.net/estatik-customization/',
                'text' => __( 'We can extend plugin features and customize it to meet your requirements. To get an estimate, just fill out the form and we will get back to you with a quote.', 'es' ),
                'title' => __( 'Custom Development', 'es' ),
            ),
            array(
                'link' => 'https://estatik.net/product/installation-setup/',
                'text' => __( 'If you are limited in time or just don’t feel like setting up the plugin yourself, our team is at your service. We can help set up your WordPress website to look like our plugin or theme demo websites.', 'es' ),
                'title' => __( 'Installation & Setup', 'es' ),
            ),
			array(
				'link' => 'https://estatik.net/product/estatik-premium-setup/',
				'text' => __( 'Installation, connection to MLS, and mapping MLS fields to Estatik for every property type (Residential, Commercial, Multifamily, Lease, LotsAndLand, etc.), setting up automatic import, and launching synchronization.', 'es' ),
				'title' => __( 'Premium MLS Setup (for Premium users only)', 'es' ),
			),
//            array(
//                'link' => '',
//                'text' => __( 'Estatik Pro integration with any MLS provider via RETS or IDX on individual custom basis.', 'es' ),
//                'title' => __( 'MLS integration service', 'es' ),
//            ),
//            array(
//                'link' => '',
//                'text' => __( 'Design, development, testing of your custom real estate website.', 'es' ),
//                'title' => __( 'Turn-key website', 'es' ),
//            ),
        );
    }

	/**
	 * Render page action.
	 *
	 * @return void
	 */
	public static function render() {
	    $f = es_framework_instance();
	    $f->load_assets();
	    wp_enqueue_script( 'es-slick' );
	    wp_enqueue_script( 'es-admin' );
	    wp_enqueue_style( 'es-dashboard', ES_PLUGIN_URL . 'admin/css/dashboard.min.css', array( 'es-admin', 'es-slick' ), Estatik::get_version() );

		es_load_template( 'admin/dashboard/index.php', array(
		    'links' => static::get_links(),
            'posts' => static::get_posts(),
            'products' => static::get_carousel_items(),
            'services' => static::get_services(),
			'changelog' => static::get_changelog(),
        ) );
	}
}
