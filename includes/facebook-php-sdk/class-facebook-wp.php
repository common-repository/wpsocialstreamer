<?php
// parent class
if ( ! class_exists( 'Facebook' ) )
	require_once( dirname( __FILE__ ) . '/facebook.php' );

/**
 * Override default Facebook PHP SDK behaviors with WordPress-friendly features
 *
 * @since 1.0
 */
class Facebook_WP_Extend extends Facebook {

	/**
	 * Override Facebook PHP SDK cURL function with WP_HTTP
	 * Facebook PHP SDK is POST-only
	 *
	 * @since 1.0
	 * @todo add file upload support if we care
	 * @param string $url request URL
	 * @param array $params parameters used in the POST body
	 * @param CurlHandler $ch Initialized curl handle. unused: here for compatibility with parent method parameters only
	 * @return string HTTP response body
	 */
	protected function makeRequest( $url, $params, $ch=null ) {
		global $wp_version;

		if ( empty( $url ) || empty( $params ) )
			throw new FacebookApiException( array( 'error_code' => 400, 'error' => array( 'type' => 'makeRequest', 'message' => 'Invalid parameters and/or URI passed to makeRequest' ) ) );

		$params = array(
			'redirection' => 0,
			'httpversion' => '1.1',
			'timeout' => 60,
			'user-agent' => apply_filters( 'http_headers_useragent', 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ) . '; facebook-php-' . self::VERSION . '-wp' ),
			'headers' => array( 'Connection' => 'close' , 'Content-type' => 'application/x-www-form-urlencoded'),
			'sslverify' => false, // warning: might be overridden by 'https_ssl_verify' filter
			'body' => http_build_query( $params, '', '&' )
		);

		$response = wp_remote_post( $url, $params );

		if ( is_wp_error( $response ) ) {
			throw new FacebookApiException( array( 'error_code' => $response->get_error_code(), 'error_msg' => $response->get_error_message() ) );
		} else if ( wp_remote_retrieve_response_code( $response ) != '200' ) {
			$fb_response = json_decode( $response['body'] );

			$error_subcode = '';

			if ( isset( $fb_response->error->error_subcode ) ) {
				$error_subcode = $fb_response->error->error_subcode;
			}

			throw new FacebookApiException( array(
				'error_code' => $fb_response->error->code,
				'error' => array(
					'message' => $fb_response->error->message,
					'type' => $fb_response->error->type
				)
			) );
		}

		return wp_remote_retrieve_body( $response );
	}
}
?>
