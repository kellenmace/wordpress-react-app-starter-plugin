<?php
/**
 * React App CPT Registration.
 *
 * @since   1.0.0
 * @package React_App
 */

require_once __DIR__ . '/../vendor/cpt-core/CPT_Core.php';
require_once __DIR__ . '/../vendor/cmb2/init.php';

/**
 * React App CPT Registration.
 *
 * @since 1.0.0
 *
 * @see   https://github.com/WebDevStudios/CPT_Core
 */
class RA_CPT extends CPT_Core {
	/**
	 * Parent plugin class.
	 *
	 * @var React_App
	 * @since  1.0.0
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * Register Custom Post Types.
	 *
	 * See documentation in CPT_Core, and in wp-includes/post.php.
	 *
	 * @since  1.0.0
	 *
	 * @param  React_App $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();

		// Register this cpt.
		// First parameter should be an array with Singular, Plural, and Registered name.
		parent::__construct(
			array(
				esc_html__( 'Single Name', 'react-app' ),
				esc_html__( 'Plural Name', 'react-app' ),
				'cpt_slug',
			),
			array(
				'supports'  => array(
					'title',
					'editor',
				),
				'menu_icon' => '', // https://developer.wordpress.org/resource/dashicons/
				'public'    => false,
				'show_ui'   => false, // Set this to false to hide this CPT in the WP admin.
			)
		);
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  1.0.0
	 */
	public function hooks() {
		add_action( 'cmb2_admin_init', array( $this, 'fields' ) );
	}

	/**
	 * Add custom fields to the CPT.
	 *
	 * @since  1.0.0
	 */
	public function fields() {

		// Set our prefix.
		$prefix = 'ra_';

		// Define our metaboxes and fields.
		// See documentation here: https://github.com/CMB2/CMB2/wiki/Field-Types
		$cmb = new_cmb2_box( array(
			'id'           => $prefix . 'metabox',
			'title'        => esc_html__( 'CPT Fields', 'react-app' ),
			'object_types' => array( 'cpt_slug' ),
		) );

		$cmb->add_field( array(
			'name'    => 'Text Field',
			'id'      => $prefix . 'text_field',
			'type'    => 'text',
		) );
	}

	/**
	 * Registers admin columns to display. Hooked in via CPT_Core.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $columns Array of registered column names/labels.
	 * @return array          Modified array.
	 */
	public function columns( $columns ) {
		$new_column = array();
		return array_merge( $new_column, $columns );
	}

	/**
	 * Handles admin column display. Hooked in via CPT_Core.
	 *
	 * @since  1.0.0
	 *
	 * @param array   $column   Column currently being rendered.
	 * @param integer $post_id  ID of post to display column for.
	 */
	public function columns_display( $column, $post_id ) {
		switch ( $column ) {
		}
	}
}
