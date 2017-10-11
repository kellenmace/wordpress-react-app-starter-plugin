<?php
/**
 * Enqueue Asssets.
 *
 * @since   1.0.0
 * @package React_App
 */

/**
 * Enqueue Assets
 *
 * @since 1.0.0
 */
class RA_Enqueue_Assets {
	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 *
	 * @var   React_App
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 *
	 * @param  React_App $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  1.0.0
	 */
	public function hooks() {
		add_filter( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since  1.0.0
	 */
	public function enqueue_scripts() {

		if ( ! $this->is_react_app_page_template_being_used() ) {
			return;
		}

		wp_enqueue_style( 'react-app-styles', $this->plugin->url . 'dist/style.css', array(), '1.0.0' );

		wp_enqueue_script( 'react-app-js', $this->plugin->url . 'dist/scripts.js', array(), '1.0.0', true );

		// Send this data to the front end on page load for use in the React app.
		wp_localize_script( 'react-app-js', 'RAReactAppData', $this->get_app_data() );
	}

	/**
	 * Is the React App page template currently being used?
	 *
	 * @return boolean Whether the React App page template is being used.
	 */
	private function is_react_app_page_template_being_used() {
		return 'react-app-template.php' === basename( get_page_template_slug() );
	}

	/**
	 * Get the app data.
	 *
	 * @since  1.0.0
	 * @return array The app data.
	 */
	public function get_app_data() {
		return array(
			'RESTBaseURL'    => esc_url_raw( $this->plugin->rest_api_endpoints->rest_base_url ),
			'isUserLoggedIn' => is_user_logged_in(),
			'currentUserID'  => get_current_user_id(),
			'nonce'          => wp_create_nonce( 'wp_rest' ),
			'loginURL'       => wp_login_url( get_permalink() ),
		);
	}

}
