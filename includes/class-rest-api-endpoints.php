<?php
/**
 * React App REST API Endpoints.
 *
 * @since   1.0.0
 * @package React_App
 */

/**
 * Register REST API Endpoints.
 *
 * @since   1.0.0
 * @package React_App
 */
if ( class_exists( 'WP_REST_Controller' ) ) {
	class RA_Rest_Api_Endpoints extends WP_REST_Controller {
		/**
		 * Parent plugin class.
		 *
		 * @var   React_App
		 * @since 1.0.0
		 */
		protected $plugin = null;

		/**
		 * The REST API endpoint version.
		 *
		 * @var   string
		 * @since 1.0.0
		 */
		protected $version = '1';

		/**
		 * The base URL for the REST API endpoints.
		 *
		 * @var   string
		 * @since 1.0.0
		 */
		protected $rest_base_url = '';

		/**
		 * Prefix to use for CPT post meta keys.
		 *
		 * @var   string
		 * @since 1.0.0
		 */
		protected $prefix = 'ra_';

		/**
		 * Magic getter for properties.
		 *
		 * @since  1.0.0
		 * @param  string    $field Field to get.
		 * @throws Exception        Throws an exception if the field is invalid.
		 * @return mixed            The field value.
		 */
		public function __get( $field ) {

			if ( property_exists( $this, $field ) ) {
				return $this->$field;
			}

			throw new Exception( 'Invalid '. __CLASS__ .' property: ' . $field );
		}

		/**
		 * Constructor.
		 *
		 * @since  1.0.0
		 *
		 * @param  React_App $plugin Main plugin object.
		 */
		public function __construct( $plugin ) {
			$this->plugin = $plugin;

			$this->set_namespace_property();
			$this->set_rest_base_property();
			$this->set_rest_base_url_property();
			$this->hooks();
		}

		/**
		 * Set the namespace property.
		 *
		 * @since  1.0.0
		 */
		public function set_namespace_property() {
			$this->namespace = 'react-app/v' . $this->version;
		}

		/**
		 * Set the REST base property.
		 *
		 * @since  1.0.0
		 */
		public function set_rest_base_property() {
			$this->rest_base = 'cpt-slug';
		}

		/**
		 * Set the REST base URL property.
		 *
		 * @since  1.0.0
		 */
		public function set_rest_base_url_property() {
			$this->rest_base_url = trailingslashit( rest_url( $this->namespace . '/' . $this->rest_base ) );
		}

		/**
		 * Add our hooks.
		 *
		 * @since  1.0.0
		 */
		public function hooks() {
			add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		}

		/**
		 * Register the routes for the objects of the controller.
		 *
		 * @since  1.0.0
		 */
		public function register_routes() {

			// Get items and create item routes.
			register_rest_route( $this->namespace, '/' . $this->rest_base, array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permission_check' ),
					'args'                => array(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( false ),
				),
			) );

			// Get, update and delete item routes.
			register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_item' ),
						'permission_callback' => array( $this, 'get_item_permissions_check' ),
						'args'                => array(
							'context' => array(
								'default' => 'view',
							),
						),
					),
					array(
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => array( $this, 'update_item' ),
						'permission_callback' => array( $this, 'update_item_permissions_check' ),
						'args'                => $this->get_endpoint_args_for_item_schema( false ),
					),
					array(
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => array( $this, 'delete_item' ),
						'permission_callback' => array( $this, 'delete_item_permissions_check' ),
						'args'                => array(
							'force' => array(
								'default' => false,
							),
						),
					),
				)
			);
		}

		/**
		 * Get items.
		 *
		 * @since  1.0.0
		 * @param  WP_REST_Request $request Full details about the request.
		 * @return                          The items.
		 */
		public function get_items( $request ) {

			$args = (array) $this->sanitize_recursively( $request->get_param( 'args' ) );

			return new WP_REST_Response( $this->get_posts_data( $args ), 200 );
		}

		/**
		 * Get CPT data.
		 *
		 * @since  1.0.0
		 * @param  array $args The arguments to use for the query.
		 * @return array       The CPT data.
		 */
		public function get_posts_data( $args = array() ) {

			$posts_data = array();

			foreach ( $this->get_cpt_posts( $args ) as $cpt_post ) {
				$posts_data[] = $this->get_post_data( $cpt_post );
			}

			return $posts_data;
		}

		/**
		 * Get CPT posts.
		 *
		 * @since  1.0.0
		 * @param  array $args The arguments to use for the query.
		 * @return array       The CPT posts.
		 */
		private function get_cpt_posts( $args ) {

			$defaults = array(
				'posts_per_page'         => 100,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			);

			$args = wp_parse_args( $args, $defaults );

			// Ensure that queries are limited to this post type only.
			$args['post_type'] = 'cpt_slug';

			$query = new WP_Query( $args );

			// If posts were found, return an array of the WP_Post objects.
			if ( $query->have_posts() ) {
				return $query->get_posts();
			}

			// Otherwise, return an empty array.
			return array();
		}

		/**
		 * Get a post's data.
		 *
		 * @since  1.0.0
		 * 
		 * @param  WP_Post $post The post.
		 * 
		 * @return array The post data.
		 */
		private function get_post_data( $post ) {

			// If a post ID was passed in, try to convert it to a WP_Post object.
			if ( is_scalar( $post ) ) {
				$post = get_post( $post );
			}

			if ( ! $post instanceof WP_Post ) {
				return array();
			}

			// Get the data needed for an individual post.
			return array(
				'ID'         => $post->ID,
				'title'      => get_the_title( $post ),
				'content'    => get_post_field( 'post_content', $post ),
				'test_field' => get_post_meta( $post->ID, $this->prefix . 'text_field', true ),
			);
		}

		/**
		 * Permission check for getting items.
		 *
		 * @since  1.0.0
		 * @param  WP_REST_Request $request Full details about the request.
		 * @return bool                     Whether the user can get items.
		 */
		public function get_items_permission_check( $request ) {
			return current_user_can( 'read' );
		}

		/**
		 * Create item.
		 *
		 * @since  1.0.0
		 * @param  WP_REST_Request $request   Full details about the request.
		 * @return array           $post_data The data for the newly created post, or error message.
		 */
		public function create_item( $request ) {

			$post_title   = sanitize_text_field( $request->get_param( 'postTitle' ) );
			$post_content = sanitize_text_field( $request->get_param( 'postContent' ) );

			$post_id = wp_insert_post( array(
				'post_title'   => $post_title,
				'post_content' => $post_content,
				'post_type'    => 'cpt_slug',
				'post_status'  => 'publish',
			) );

			if ( ! $post_id ) {
				return new WP_REST_Response( 'Unable to create new post using the parameters provided.', 400 );
			}

			$post_data = $this->get_post_data( $post_id );

			return new WP_REST_Response( $post_data, 201 );
		}

		/**
		 * Permission check for creating item.
		 *
		 * @since  1.0.0
		 * @param  WP_REST_Request $request Full details about the request.
		 * @return bool                     Whether this user can create CPT posts.
		 */
		public function create_item_permissions_check( $request ) {
			return current_user_can( 'publish_posts' );
		}

		/**
		 * Get item.
		 *
		 * @since  1.0.0
		 * @param  WP_REST_Request $request Full details about the request.
		 * @return                          The item details.
		 */
		public function get_item( $request ) {

			$post_id   = absint( $request->get_param( 'id' ) );
			$post_data = $this->get_post_data( $post_id );

			return new WP_REST_Response( $post_data, 200 );
		}

		/**
		 * Permission check for getting item.
		 *
		 * @since  1.0.0
		 * @param  WP_REST_Request $request Full details about the request.
		 * @return bool                     Whether this user can get items.
		 */
		public function get_item_permissions_check( $request ) {
			return current_user_can( 'read' );
		}

		/**
		 * Update item.
		 *
		 * @since  1.0.0
		 *
		 * @param  WP_REST_Request $request Full details about the request.
		 *
		 * @return string                   Success or failure message.
		 */
		public function update_item( $request ) {

			$post_id = absint( $request->get_param( 'id' ) );
			$key     = sanitize_text_field( $request->get_param( 'key' ) );
			$value   = $this->sanitize_recursively( $request->get_param( 'value' ) );

			if ( ! $post_id || ! $key || 'cpt_slug' !== get_post_type( $post_id ) ) {
				return new WP_REST_Response( 'Invalid data was provided to the update_item REST endpoint.', 400 );
			}

			switch ( $key ) {
				case 'title':
					$this->update_post_title( $post_id, $value );
					break;
				case 'content':
					$this->update_post_content( $post_id, $value );
					break;
				case 'test_field':
					$this->update_post_meta( $post_id, $key, $value );
			}

			return new WP_REST_Response( 'Post data was updated successfully.', 200 );
		}

		/**
		 * Update a post's title.
		 *
		 * @since  1.0.0
		 *
		 * @param  int $post_id The ID of the post.
		 *
		 * @param  string $new_title   The new title.
		 */
		private function update_post_title( $post_id, $new_title ) {

			wp_update_post( array(
				'ID'         => $post_id,
				'post_title' => $new_title,
			) );
		}

		/**
		 * Update a post's content.
		 *
		 * @since  1.0.0
		 *
		 * @param  int $post_id The post ID.
		 *
		 * @param  string $new_content The new content.
		 */
		private function update_post_content( $post_id, $new_content ) {

			wp_update_post( array(
				'ID'           => $post_id,
				'post_content' => $new_content,
			) );
		}

		/**
		 * Update a post's post meta.
		 *
		 * @since  1.0.0
		 *
		 * @param  int $post_id The post ID.
		 * @param  string $key         The key.
		 * @param  mixed  $value       The new value.
		 */
		private function update_post_meta( $post_id, $key, $value ) {

			update_post_meta( $post_id, $this->prefix . $key, $value );

			$this->update_date_modified( $post_id );
		}

		/**
		 * Update a post's date modified timestamp.
		 *
		 * @since  1.0.0
		 *
		 * @param  int $post_id The post ID.
		 */
		private function update_date_modified( $post_id ) {

			$time = current_time( 'mysql' );

			wp_update_post( array(
				'ID'                => $post_id,
				'post_modified'     => $time,
				'post_modified_gmt' => get_gmt_from_date( $time ),
			) );
		}

		/**
		 * Permission check for updating items.
		 *
		 * @since  1.0.0
		 * @param  WP_REST_Request $request Full details about the request.
		 * @return bool                     Whether the user can update items.
		 */
		public function update_item_permissions_check( $request ) {
			return current_user_can( 'edit_others_posts' );
		}

		/**
		 * Delete item.
		 *
		 * @since  1.0.0
		 * @param  WP_REST_Request $request Full details about the request.
		 * @return string                   Success or failure message.
		 */
		public function delete_item( $request ) {

			$post_id = absint( $request->get_param( 'id' ) );
			$post    = wp_delete_post( $post_id );

			if ( ! $post ) {
				return new WP_REST_Response( "Unable to delete the post with ID {$post_id}", 500 );
			}

			return new WP_REST_Response( "Successfuly deleted post {$post_id}", 200 );
		}

		/**
		 * Permission check for deleting items.
		 *
		 * @since  1.0.0
		 * @param  WP_REST_Request $request Full details about the request.
		 * @return bool                     Whether the user can delete items.
		 */
		public function delete_item_permissions_check( $request ) {
			return current_user_can( 'delete_others_posts' );
		}

		/**
		 * Sanitize a value recursively. Works with both arrays and scalar values.
		 *
		 * @since  1.0.0
		 * @param  array $value The value to sanitize.
		 * @return array $value The sanitized value.
		 */
		private function sanitize_recursively( $value ) {

			if ( ! is_array( $value ) ) {
				return sanitize_text_field( $value );
			}

			foreach ( $value as $key => $array_value ) {
				if ( is_array( $array_value ) ) {
					$value[ sanitize_text_field( $key ) ] = $this->sanitize_recursively( $array_value );
				} else {
					$value[ sanitize_text_field( $key ) ] = sanitize_text_field( $array_value );
				}
			}

			return $value;
		}
	}
}
