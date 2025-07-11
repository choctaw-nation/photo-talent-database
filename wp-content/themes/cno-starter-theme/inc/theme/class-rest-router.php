<?php
/**
 * REST API Router Class
 *
 * @package ChoctawNation
 */

namespace ChoctawNation;

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
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'get_posts' ),
				'permission_callback' => fn()=> current_user_can( 'edit_posts' ),
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
		$params = $request->get_json_params();
		if ( ! isset( $params['ids'] ) || ! is_array( $params['ids'] ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Invalid or missing "ids" parameter.',
				),
				400
			);
		}
		$ids  = array_map( 'intval', $params['ids'] );
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
				$data['posts'][] = array(
					'id'        => $post->ID,
					'title'     => $post->post_title,
					'isChoctaw' => cno_get_is_choctaw( $post->ID ) === 'Choctaw',
					'thumbnail' => wp_get_attachment_image(
						get_field( 'image_front', $post->ID ),
						'medium',
						false,
						array(
							'loading' => 'lazy',
							'class'   => 'w-100 h-100 object-fit-cover',
						)
					),
					'lastUsed'  => get_field( 'last_used', $post->ID ),
				);
			}
			$data['success'] = true;
		}

		return rest_ensure_response( $data );
	}
}
