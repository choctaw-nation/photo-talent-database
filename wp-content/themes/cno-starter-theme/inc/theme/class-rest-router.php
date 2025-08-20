<?php
/**
 * REST API Router Class
 *
 * @package ChoctawNation
 */

namespace ChoctawNation;

use DateTime;
use WP_HTML_Tag_Processor;
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
		$namespace = "{$this->namespace}/v{$this->version}";
		register_rest_route(
			$namespace,
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
						'sanitize_callback' => 'wp_parse_id_list',
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
			$namespace,
			'/talent/(?P<id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_talent_details' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'id' => array(
							'required'          => true,
							'type'              => 'number',
							'sanitize_callback' => 'absint',
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'edit_post' ),
					'permission_callback' => fn()=> current_user_can( 'edit_posts' ),
					'args'                => array(
						'id'       => array(
							'required'          => true,
							'type'              => 'number',
							'description'       => '',
							'sanitize_callback' => 'absint',
						),
						'lastUsed' => array(
							'required'          => false,
							'type'              => 'string',
							'description'       => 'The date to set as last used.',
							'sanitize_callback' => function ( $value ) {
								if ( ! preg_match( '/^\d{8}$/', $value ) ) {
									return new \WP_Error( 'invalid_date_format', 'Date must be in Ymd format.', array( 'status' => 400 ) );
								}
								$date_obj = DateTime::createFromFormat( 'Ymd', $value );
								$errors = DateTime::getLastErrors();
								if ( ! $date_obj || $errors['warning_count'] > 0 || $errors['error_count'] > 0 ) {
									return new \WP_Error( 'invalid_date', 'Invalid date provided.', array( 'status' => 400 ) );
								}
								return $value;
							},
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'trash_post' ),
					'permission_callback' => fn()=> current_user_can( 'edit_posts' ),
					'args'                => array(
						'id' => array(
							'required'          => true,
							'type'              => 'number',
							'description'       => '',
							'sanitize_callback' => 'absint',
						),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/talent-list',
			array(
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
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
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
				),
			)
		);

		register_rest_route(
			$namespace,
			'/talent-list/(?P<id>\d+)',
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'trash_post' ),
				'permission_callback' => fn()=> current_user_can( 'edit_others_talent-lists' ),
				'args'                => array(
					'id' => array(
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
		$ids  = $request->get_param( 'talent-ids' );
		$args = array(
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
	 * Set the last used date for a specific post.
	 *
	 * @param WP_REST_Request $request The REST request object.
	 * @return WP_REST_Response The response indicating success or failure.
	 */
	public function edit_post( WP_REST_Request $request ): WP_REST_Response {
		$id   = $request->get_param( 'id' );
		$date = $request->get_param( 'lastUsed' );
		if ( $date ) {
			return $this->set_last_used( $id, $date );
		} else {
			$post = get_post( $id );
			if ( ! $post ) {
				return new WP_REST_Response(
					array(
						'success' => false,
						'message' => 'Post not found.',
					),
					404
				);
			}
			$post->post_status = 'publish';
			$updated           = wp_update_post( $post, true );
			if ( is_wp_error( $updated ) ) {
				return new WP_REST_Response(
					array(
						'success' => false,
						'message' => 'Failed to update post status.',
					),
					500
				);
			} else {
				return new WP_REST_Response(
					array(
						'success' => true,
						'message' => $post->post_title . ' has been approved.',
						'data'    => array(
							'post' => get_post( $id ),
						),
					),
					200
				);
			}
		}
	}

	/**
	 * Delete a specific post.
	 *
	 * @param WP_REST_Request $request The REST request object.
	 * @return WP_REST_Response The response indicating success or failure.
	 */
	public function trash_post( WP_REST_Request $request ): WP_REST_Response {
		$id   = $request->get_param( 'id' );
		$post = get_post( $id );
		if ( ! $post ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Post not found.',
				),
				404
			);
		}
		$success = wp_trash_post( $id, true );
		if ( ! $success ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Failed to delete post.',
				),
				500
			);
		} else {
			return new WP_REST_Response(
				array(
					'success' => true,
					'message' => $post->post_title . ' was trashed.',
				),
				200
			);
		}
	}

	/**
	 * Update the post status to 'publish' for a specific post ID.
	 *
	 * @param string $id The post ID.
	 * @param string $date The “last used” date passed in the request as Ymd.
	 * @return WP_REST_Response The response indicating success or failure.
	 */
	private function set_last_used( string $id, string $date ): WP_REST_Response {
		$last_used = get_field( 'last_used', $id );
		if ( $date === $last_used ) {
			return new WP_REST_Response(
				array(
					'success' => true,
					'message' => 'Last used date is already set to ' . absint( $last_used ) . '!',
					'data'    => array(
						'post'      => get_post( $id ),
						'last_used' => get_field( 'last_used', $id ),
					),
				)
			);
		}
		$success = (bool) update_field( 'last_used', $date, $id );

		return new WP_REST_Response(
			array(
				'success' => $success,
				'message' => true === $success ? 'Last used date updated successfully.' : 'Failed to update last used date.',
				'data'    => array(
					'post'      => get_post( $id ),
					'last_used' => get_field( 'last_used', $id ),
				),
			)
		);
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
			$acf_image_array = get_field( "image_{$current_image_id}", $post_id );
			if ( ! $acf_image_array ) {
				return array();
			}
			if ( is_array( $acf_image_array ) ) {
				$image_data[ $current_image_id ]['url']    = $acf_image_array['url'];
				$image_data[ $current_image_id ]['srcset'] = wp_get_attachment_image_srcset( $acf_image_array['id'] );
				$image_data[ $current_image_id ]['alt']    = $acf_image_array['alt'];
				$image_data[ $current_image_id ]['sizes']  = $acf_image_array['sizes'];
			} else {
				$image_data[ $current_image_id ] = $this->get_image_data( $acf_image_array );
			}
		}
		return 'all' === $image_id ? $image_data : $image_data[ $image_id ];
	}

	/**
	 * Get image data for a specific image ID and post ID.
	 *
	 * @param string|int $image_id The image ID.
	 * @return array The image data.
	 */
	private function get_image_data( string|int $image_id ): array {
		if ( ! is_numeric( $image_id ) ) {
			return array();
		}
		$image_data = array();
		$image      = wp_get_attachment_image( $image_id, 'medium', false );
		$processor  = new WP_HTML_Tag_Processor( $image );
		if ( $processor->next_tag( 'img' ) ) {
			$image_data['url']    = $processor->get_attribute( 'src' );
			$image_data['alt']    = $processor->get_attribute( 'alt' );
			$image_data['srcset'] = $processor->get_attribute( 'srcset' );
			$image_data['sizes']  = $processor->get_attribute( 'sizes' );
		}
		return $image_data;
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

	/**
	 * Get Talent Details
	 *
	 * @param WP_REST_Request $request the request object
	 * @return WP_REST_Response the response
	 */
	public function get_talent_details( WP_REST_Request $request ): WP_REST_Response {
		$id   = $request['id'];
		$post = get_post( $id );
		if ( is_null( $post ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Post not found.',
				),
				404
			);
		}
		$html = ob_start();
		get_template_part( 'template-parts/content', 'talent-details', array( 'id' => $id ) );
		$html .= ob_get_contents();
		$html  = ob_get_clean();
		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => 'Talent details retrieved successfully.',
				'data'    => array(
					'html' => $html,
				),
			),
			200
		);
	}
}
