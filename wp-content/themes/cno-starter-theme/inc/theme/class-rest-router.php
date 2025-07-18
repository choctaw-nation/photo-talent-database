<?php
/**
 * REST API Router Class
 *
 * @package ChoctawNation
 */

namespace ChoctawNation;

use DateTime;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Class Rest_Router
 *
 * This class registers REST API routes for the Choctaw Nation theme.
 */
class Rest_Router {
	/**
	 * The version of the REST API.
	 *
	 * @var int $version
	 */
	private int $version;

	/**
	 * The namespace for the REST API routes.
	 *
	 * @var string $namespace
	 */
	private string $namespace;

	/**
	 * Constructor to initialize the REST API routes.
	 */
	public function __construct() {
		$this->namespace = 'cno';
		$this->version   = 1;
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register REST API routes.
	 */
	public function register_routes() {
		register_rest_route(
			"{$this->namespace}/v{$this->version}",
			'/talent',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_posts' ),
				'permission_callback' => fn()=> current_user_can( 'edit_posts' ),
				'args'                => array(
					'talent-ids' => array(
						'required'          => true,
						'type'              => 'string',
						'description'       => 'Comma-separated list of talent post IDs to retrieve.',
						'sanitize_callback' => function ( $value ) {
							return implode( ',', wp_parse_id_list( $value ) );
						},
					),
					'images'     => array(
						'required'          => false,
						'type'              => 'array',
						'description'       => 'Comma-separated list of image types ("front","back","left","right","three_quarters")',
						'sanitize_callback' => function ( $value ) {
							$value = explode( ',', $value );
							$allowed_values = array( 'front', 'back', 'left', 'right', 'three_quarters', 'all' );
							$value = array_map( 'sanitize_text_field', $value );
							$value = array_intersect( $value, $allowed_values );
							if ( empty( $value ) ) {
								return new \WP_Error( 'invalid_image_types', 'No valid image types provided.', array( 'status' => 400 ) );
							}
							return array_values( $value );
						},
					),
					'fields'     => array(
						'required'          => false,
						'type'              => 'string',
						'description'       => 'Comma-separated list of fields to include in the response.',
						'sanitize_callback' => function ( $value ) {
							if ( ! is_string( $value ) ) {
								return new \WP_Error( 'invalid_fields', 'Fields must be a string.', array( 'status' => 400 ) );
							}
							$value = explode( ',', $value );
							$allowed_values = array( 'contact', 'lastUsed', 'isChoctaw' );
							$value = array_map( 'sanitize_text_field', $value );
							$value = array_intersect( $value, $allowed_values );
							if ( empty( $value ) ) {
								return new \WP_Error( 'invalid_fields', 'No valid fields provided.', array( 'status' => 400 ) );
							}
							return array_values( $value );
						},
					),
				),
			)
		);
		register_rest_route(
			"{$this->namespace}/v{$this->version}",
			'/talent-list',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'save_talent_list' ),
				'permission_callback' => fn()=> current_user_can( 'edit_talent-lists' ),
				'args'                => array(
					'ids'             => array(
						'required'          => true,
						'type'              => 'array',
						'description'       => 'Array of post IDs to save as a talent list.',
						'sanitize_callback' => 'wp_parse_id_list',
					),
					'listName'        => array(
						'required'          => true,
						'type'              => 'string',
						'description'       => 'Name of the talent list.',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'listDescription' => array(
						'required'          => false,
						'type'              => 'string',
						'description'       => 'Description of the talent list.',
						'sanitize_callback' => 'sanitize_textarea_field',
					),
					'listExpiry'      => array(
						'required'          => false,
						'type'              => 'number',
						'description'       => 'Expiry time for the talent list, formatted as PHP “Ymd”',
						'sanitize_callback' => 'absint',
					),
				),
			)
		);
		register_rest_route(
			"{$this->namespace}/v{$this->version}",
			'/talent-list/(?P<id>\d+)',
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'remove_selected_talent' ),
				'permission_callback' => fn()=> current_user_can( 'edit_others_talent-lists' ),
				'args'                => array(
					'talentId' => array(
						'required'          => true,
						'type'              => 'number',
						'description'       => 'ID of the talent list to remove.',
						'sanitize_callback' => 'absint',
					),
				),
			)
		);
	}

	/**
	 * Callback function to handle the GET request for talent posts.
	 *
	 * @param WP_REST_Request $request The REST request object.
	 * @return WP_REST_Response The response containing the posts.
	 */
	public function get_posts( WP_REST_Request $request ): WP_REST_Response {
		// Accept talent-ids as a comma-separated string
		$talent_ids_str = $request->get_param( 'talent-ids' );
		$ids            = wp_parse_id_list( $talent_ids_str );
		$args           = array(
			'post_type'   => 'post',
			'post__in'    => $ids,
			'post_status' => 'publish',
		);

		$posts = get_posts( $args );
		$data  = array(
			'success' => true,
			'posts'   => array(),
		);
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$post_data = array(
					'id'    => $post->ID,
					'title' => $post->post_title,
				);
				$fields    = $request->get_param( 'fields' );
				if ( ! empty( $fields ) && is_array( $fields ) ) {
					if ( in_array( 'isChoctaw', $fields, true ) ) {
						$post_data['isChoctaw'] = cno_get_is_choctaw( $post->ID ) === 'Choctaw';
					}
					if ( in_array( 'lastUsed', $fields, true ) ) {
						$post_data['lastUsed'] = get_field( 'last_used', $post->ID );
					}
					if ( in_array( 'contact', $fields, true ) ) {
						$post_data['contact'] = array(
							'email' => get_field( 'email', $post->ID ),
							'phone' => get_field( 'phone', $post->ID ),
						);
					}
				}
				$images = $request->get_param( 'images' );
				if ( ! empty( $images ) ) {
					$image_array = array();
					foreach ( $images as $image ) {
						$image_array[ $image ] = $this->get_image_array( $image, $post->ID );
					}
					$post_data['images'] = $image_array;
				}
				$data['posts'][] = $post_data;
			}
			$data['success'] = true;
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Get the image data for a specific image ID and post ID.
	 *
	 * @param string $image_id The image ID.
	 * @param int    $post_id The post ID.
	 * @return array The image data.
	 */
	private function get_image_array( string $image_id, int $post_id ): array {
		$image_data     = array();
		$allowed_values = array( 'front', 'back', 'left', 'right', 'three_quarters' );
		if ( 'all' === $image_id ) {
			$ids = $allowed_values;
		} else {
			$ids = array( $image_id );
		}
		foreach ( $ids as $current_image_id ) {
			$id = get_field( "image_{$current_image_id}", $post_id );
			if ( ! $id ) {
				return array();
			}
			$image_data[ $current_image_id ]['url']    = wp_get_attachment_url( $id, 'full', false );
			$image_data[ $current_image_id ]['srcset'] = wp_get_attachment_image_srcset( $id );
			$image_data[ $current_image_id ]['alt']    = wp_get_attachment_metadata( $id )['image_meta']['alt'];
			$image_data[ $current_image_id ]['sizes']  = wp_get_attachment_image_sizes( $id );
		}
		return 'all' === $image_id ? $image_data : $image_data[ $image_id ];
	}

	/**
	 * Callback function to handle the POST request for saving a talent list.
	 *
	 * @param WP_REST_Request $request The REST request object.
	 * @return WP_REST_Response The response indicating success or failure.
	 */
	public function save_talent_list( WP_REST_Request $request ): WP_REST_Response {
		$params        = $request->get_json_params();
		$post_title    = $params['listName'];
		$post_excerpt  = $params['listDescription'] ?? '';
		$ids           = $params['ids'];
		$date_prefixer = new DateTime( 'now', wp_timezone() );
		$post_id       = wp_insert_post(
			array(
				'post_type'    => 'talent-list',
				'post_title'   => $date_prefixer->format( 'Ymd' ) . ' — ' . $post_title,
				'post_excerpt' => $post_excerpt,
				'post_status'  => 'publish',
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Failed to create talent list.',
					'data'    => array(
						'error' => $post_id->get_error_message(),
					),
				),
				500
			);
		}
		$post_expiry     = new DateTime( $params['listExpiry'] ?? '+2 weeks', wp_timezone() );
		$selected_talent = update_field( 'selected_talent', $ids, $post_id );
		$post_expiry     = update_field( 'post_expiry', $post_expiry->format( 'Ymd' ), $post_id );

		if ( ! $selected_talent || ! $post_expiry ) {
			$talent_error      = ! $selected_talent ? 'Could not update selected talent field.' : null;
			$post_expiry_error = ! $post_expiry ? 'Could not update post expiry field.' : null;
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Failed to save talent list.',
					'data'    => array(
						'error' => array_filter(
							array(
								'talent'      => $talent_error,
								'post_expiry' => $post_expiry_error,
							)
						),
					),
				),
				500
			);
		}
		$post = get_post( $post_id );
		if ( ! $post ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Talent list not found.',
				),
				500
			);
		}
		$post_details = array(
			'id'         => $post->ID,
			'title'      => $post->post_title,
			'link'       => get_permalink( $post->ID ),
			'talent_ids' => get_field( 'selected_talent', $post->ID ),

		);
		if ( ! empty( $post_excerpt ) ) {
			$post_details['description'] = $post->post_excerpt;
		}
		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => 'Talent list saved successfully.',
				'data'    => array( 'post' => $post_details ),
			),
			200,
		);
	}

	/**
	 * Remove selected talent from a talent list.
	 *
	 * @param WP_REST_Request $request The REST request object.
	 * @return WP_REST_Response The response indicating success or failure.
	 */
	public function remove_selected_talent( WP_REST_Request $request ): WP_REST_Response {
		$params         = $request->get_json_params();
		$post_id        = (int) $request->get_param( 'id' );
		$talent_id      = (int) $params['talentId'];
		$current_talent = get_field( 'selected_talent', $post_id );
		if ( ! $current_talent || ! in_array( $talent_id, $current_talent, true ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Talent not found in the list.',
				),
				404
			);
		}

		$current_talent = array_diff( $current_talent, array( $talent_id ) );
		$success        = update_field( 'selected_talent', $current_talent, $post_id );
		$post_data      = get_post( $post_id );
		return new WP_REST_Response(
			array(
				'success' => $success,
				'message' => $success ? 'Talent removed successfully.' : 'Failed to remove talent.',
				'data'    => array(
					'id'                => $post_data->ID,
					'remainingSelected' => get_field( 'selected_talent', $post_data->ID ),
					'removedTalent'     => $talent_id,
				),
			),
			200
		);
	}
}