<?php
/*
 * Set your tab length to 2 spaces for this file to look correct.
 */
class curl {
	protected $_cookie_jar_path = 'curl-cookie-jar.txt';
	
	protected $_data;
	
	protected $_follow_location_redirect = true;
	
	protected $_http_auth_password = '';
	
	protected $_http_auth_user = '';
	
	protected $_http_headers = array();
	
	protected $_http_referer = '';
	
	protected $_http_user_agent = 'PHP cURL by jwandborg(http://jwandborg.github.com)';
	
	protected $_max_location_redirects = 3;
	
	protected $_no_body;
	
	protected $_post;
	
	protected $_post_string;
	
	protected $_request_url;
	
	protected $_request_timeout;
	
	protected $_response_code;
	
	protected $_return_binary_data;
	
	protected $_return_http_headers;
	
	protected $_return_transfer = true;
	
	protected $_session;
	
	protected $_use_http_auth = false;
	
	protected $_executed = false;
	
	public function __construct( $request_url, $params ) {
		$this->_request_url = $request_url;
		foreach ( $params as $key => $val ) {
			if ( preg_match( '/^[a-zA-Z_]+$/', $key ) ) {
				$var = '_' . $key;
				$this->$var = $val;
			} else {
				throw new Exception( 'Invalid $key: [' . $key . '] => ' . $val );
			}
		}
	}
	
	public function useHttpAuth( $use ){
	  if ( true == $use ) {
			$this->_use_http_auth = 1;
	  } else {
		  $this->_use_http_auth = 0;
	  }
	}
	
	public function setPostData( $data ) {
		if ( is_array( $data ) || is_object( $data ) ) {
			foreach ( $data as $key => $val ) {
				$value_pairs[] = urlencode( $key ) . '=' . urlencode( $val );
			}
			$this->_post_string = implode( '&', $value_pairs );
		} elseif ( is_string( $data ) ) {
			$this->_post_string = $data;
		}
		$this->_post = true;
	}

	public function setHttpAuthUser( $user ) {
		$this->useHttpAuth( true );
	  $this->_http_auth_user = $user;
	}
	
	public function setHttpAuthPassword( $password ) {
		$this->useHttpAuth( true );
	  $this->_http_auth_password = $password;
	}

	public function setHttpReferer( $referer ){
	  $this->_http_referer = $referer;
	}

	public function setCookieJar( $path )
	{
		$this->_cookie_jar_path = $path;
	}

	public function setUserAgent( $userAgent )
	{
		$this->_useragent = $userAgent;
	}

	public function exec() {
		$this->_executed = true;
		$s = curl_init();
		
		curl_setopt( $s, CURLOPT_URL, $this->_request_url );
		curl_setopt( $s, CURLOPT_HTTPHEADER, $this->_http_headers );
		curl_setopt( $s, CURLOPT_TIMEOUT, $this->_request_timeout );
		curl_setopt( $s, CURLOPT_MAXREDIRS, $this->_max_location_redirects );
		curl_setopt( $s, CURLOPT_RETURNTRANSFER, $this->_return_transfer );
		curl_setopt( $s, CURLOPT_FOLLOWLOCATION, $this->_follow_location_redirect );
		curl_setopt( $s, CURLOPT_COOKIEJAR, $this->_cookie_jar_path );
		curl_setopt( $s, CURLOPT_COOKIEFILE, $this->_cookie_jar_path );

		if( $this->_use_http_auth == true ) {
			curl_setopt( $s, CURLOPT_USERPWD, $this->_http_auth_user . ':' . $this->_http_auth_password );
		}
		
		if( $this->_post ) {
		   curl_setopt( $s, CURLOPT_POST, true );
		}
		
		if ( strlen( $this->_post_string ) ) { 
			curl_setopt( $s, CURLOPT_POSTFIELDS, $this->_post_string );
		}

		if( $this->_return_http_headers ) {
			curl_setopt( $s, CURLOPT_HEADER, true );
		}

		if( $this->_no_body ) {
		   curl_setopt( $s, CURLOPT_NOBODY, true );
		}
		
		if( $this->_return_binary_data )
		{
		   curl_setopt( $s ,CURLOPT_BINARYTRANSFER, true );
		}
		
		curl_setopt( $s, CURLOPT_USERAGENT, $this->_http_user_agent );
		curl_setopt( $s, CURLOPT_REFERER, $this->_http_referer );

		if ( ! $this->_data = curl_exec( $s ) ) {
			throw new Exception('cURL error: ' . curl_error( $s ) );
		}
		
		$this->_response_code = curl_getinfo( $s, CURLINFO_HTTP_CODE );
		
		curl_close( $s );
		
		return $this->_data;
	}
	
	public function getResponseCode()
	{
		if ( ! $this->_executed ) {
			throw new Exception('Run curl->exec() first.');
		}
		return $this->_response_code;
	}
	
	public function __toString() {
		if ( NULL == $this->_data && NULL == $this->_response_code ) {
			return $this->exec();
		} else {
			return (string)$this->_data;
		}
	}
}