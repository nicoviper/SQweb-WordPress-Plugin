<?php

/**
 * Send an http request to the api
 * returns true if logged, 0 if not
 * @param null $site_id
 * @return int
 */

function sqweb_check_credentials( $site_id = null ) {
	if ( isset( $_COOKIE['sqw_z'] ) && null !== $site_id ) {
		$cookiez = $_COOKIE['sqw_z'];
	}
	if ( isset( $cookiez ) && defined( 'SQW_ENDPOINT' ) ) {
		$curl = curl_init();
		curl_setopt_array( $curl, array(
			CURLOPT_URL => SQW_ENDPOINT . 'token/check',
			CURLOPT_CONNECTTIMEOUT_MS => 1000,
			CURLOPT_TIMEOUT_MS => 1000,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POSTFIELDS => array(
				'token' => $cookiez,
				'site_id' => $site_id,
				),
			) );
		$response = curl_exec( $curl );
		curl_close( $curl );

		$response = json_decode( $response );
		if ( $response != false && $response->status === true && $response->credit > 0 ) {
			return ($response->credit);
		}
	}
	return ( 0 );
}

/**
 * @param $first_name
 * @param $last_name
 * @param $email
 * @param $newpass
 * @return int
 */
function sqweb_sign_up( $first_name, $last_name, $email, $newpass ) {
	if ( defined( 'SQW_ENDPOINT' ) ) {
		$curl = curl_init();
		curl_setopt_array( $curl, array(
			CURLOPT_URL => SQW_ENDPOINT . 'sqw_auth/new',
			CURLOPT_CONNECTTIMEOUT_MS => 1000,
			CURLOPT_TIMEOUT_MS => 1000,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POSTFIELDS => array(
				'role' => '1',
				'first_name' => $first_name,
				'last_name' => $last_name,
				'email' => $email,
				'password' => $newpass,
				),
			)
		);
		$response = curl_exec( $curl );
		curl_close( $curl );

		if ( ! json_decode( $response ) == false ) {
			return ( 1 );
		}
	}
	return ( 0 );
}

/**
 * @param $email
 * @param $password
 * @return int
 */
function sqweb_sign_in( $email, $password ) {
	if ( defined( 'SQW_ENDPOINT' ) ) {
		$curl = curl_init();

		curl_setopt_array( $curl, array(
			CURLOPT_URL => SQW_ENDPOINT . 'auth/login',
			CURLOPT_CONNECTTIMEOUT_MS => 1000,
			CURLOPT_TIMEOUT_MS => 1000,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_HTTPHEADER => array( 'Content-Type: application/x-www-form-urlencoded' ),
			CURLOPT_POSTFIELDS => 'email=' . $email . '&password=' . $password,
			) );
		$response = curl_exec( $curl );
		curl_close( $curl );

		$response = json_decode( $response );
		if ( isset( $response->token ) ) {
			setcookie( 'sqw_admin_token', $response->token, time() + 36000 );
			return ( $response->token );
		}
	}
	return ( 0 );
}

/**
 * @param $token
 * @return int
 */
function sqweb_check_token( $token ) {
	if ( defined( 'SQW_ENDPOINT' ) ) {
		$curl = curl_init();
		curl_setopt_array( $curl, array(
			CURLOPT_URL => SQW_ENDPOINT . 'sqw_auth/is_auth_t',
			CURLOPT_CONNECTTIMEOUT_MS => 1000,
			CURLOPT_TIMEOUT_MS => 1000,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POSTFIELDS => array(
				'token' => $token,
				),
			) );
		$response = curl_exec( $curl );
		curl_close( $curl );

		$res = json_decode( $response );
		if ( isset( $res->id ) ) {
			return ( $res->id );
		}
	}
	return ( 0 );
}

/**
 * @param $id
 * @return int
 */
function sqw_get_sites( $id ) {
	if ( defined( 'SQW_ENDPOINT' ) ) {
		$curl = curl_init();
		curl_setopt_array( $curl, array(
			CURLOPT_URL => SQW_ENDPOINT . 'websites/' . $id,
			CURLOPT_CONNECTTIMEOUT_MS => 1000,
			CURLOPT_TIMEOUT_MS => 1000,
			CURLOPT_RETURNTRANSFER => true,
			) );
		$response = curl_exec( $curl );
		curl_close( $curl );

		$response = json_decode( $response );
		if ( ! $response->status == false ) {
			return ( $response->websites );
		}
	}
	return ( 0 );
}

/**
 * @param $data
 * @param $token
 * @return int
 */
function sqw_add_website( $data, $token ) {
	if ( defined( 'SQW_ENDPOINT' ) ) {
		$curl = curl_init();
		curl_setopt_array( $curl, array(
			CURLOPT_URL => SQW_ENDPOINT . 'websites/add',
			CURLOPT_CONNECTTIMEOUT_MS => 1000,
			CURLOPT_TIMEOUT_MS => 1000,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POSTFIELDS => array(
				'token' => $token,
				'name' => $data['sqw-ws-name'],
				'url' => $data['sqw-ws-url'],
				),
			) );
		$response = curl_exec( $curl );
		curl_close( $curl );

		$res = json_decode( $response );
		if ( $res->status != 0 ) {
			return ( 1 );
		}
	}
	return ( 0 );
}
