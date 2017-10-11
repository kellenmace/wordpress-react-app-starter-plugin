<?php
/**
 * Page Template.
 *
 * @since   1.0.0
 * @package React_App
 */

/**
 * Page Template.
 *
 * @since 1.0.0
 */
class RA_Page_Template {
	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 *
	 * @var   React_App
	 */
	protected $plugin = null;

	/**
	 * The page template path and name.
	 *
	 * @since 1.0.0
	 *
	 * @param array
	 */
	protected $templates = array(
		'templates/react-app-template.php' => 'React App',
	);

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
		add_filter( 'theme_page_templates', array( $this, 'add_new_template' ) );
		add_filter( 'wp_insert_post_data',  array( $this, 'register_project_templates' ) );
		add_filter( 'template_include',     array( $this, 'view_project_template') );
	}

	/**
	 * Adds our template to the page dropdown for v4.7+.
	 *
	 * @since 1.0.0
	 */
	public function add_new_template( $posts_templates ) {

		$posts_templates = array_merge( $posts_templates, $this->templates );

		return $posts_templates;
	}

	/**
	 * Add a filter to the save post to inject out template into the page cache.
	 *
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doens't really exist.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $data An array of slashed post data.
	 * @return array $data An array of slashed post data.
	 */
	public function register_project_templates( $data ) {

		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list.
		// If it doesn't exist, or it's empty prepare an array.
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		}

		// Delete the old cache before saving new cache data.
		wp_cache_delete( $cache_key , 'themes');

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates.
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $data;
	}

	/**
	 * Add a filter to the template include to determine if the page has our
	 * template assigned and return it's path.
	 *
	 * @since 1.0.0
	 */
	public function view_project_template( $template ) {

		global $post;

		// Return template if post is empty.
		if ( ! $post ) {
			return $template;
		}

		$custom_template = get_post_meta( $post->ID, '_wp_page_template', true );

		// Return default template if we don't have a custom one defined.
		if ( ! isset( $this->templates[ $custom_template ] ) ) {
			return $template;
		}

		$file = $this->plugin->path . $custom_template;

		// Return the file if it exists.
		if ( file_exists( $file ) ) {
			return $file;
		}

		// Return template.
		return $template;
	}
}
