<?php

require_once("external/OAuth.php");

class CurlRequest {

	public static function send($url, $method, $postBody = false, $headers = false, $ua = 'osapi 1.0') {

		$ch = curl_init();

		$request = array(
			'url' => $url,
			'method' => $method,
			'body' => $postBody,
			'headers' => $headers
		);

		// log here
	//	echo $request;
		curl_setopt($ch, CURLOPT_URL, $url);

		if ($postBody) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody);
		}

		// We need to set method even when we don't have a $postBody 'DELETE'
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $ua);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);

		if ($headers && is_array($headers)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}

		$data = @curl_exec($ch);


		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$errno = @curl_errno($ch);
		$error = @curl_error($ch);
		@curl_close($ch);

		if ($errno != CURLE_OK) {
			throw new Exception("HTTP Error: " . $error);
		}

            
        list($raw_response_headers, $response_body) = explode("\r\n\r\n", $data, 2);
        
        // fix proxy issue, sending http/1.1 100 continue, instead of giving a full header.
        if(strtolower($raw_response_headers)=='http/1.1 100 continue'){
            list($raw_response_headers, $response_body) = explode("\r\n\r\n", $response_body, 2);
        }
        
		$response_header_lines = explode("\r\n", $raw_response_headers);
		array_shift($response_header_lines);
		$response_headers = array();
		foreach($response_header_lines as $header_line) {
			list($header, $value) = explode(': ', $header_line, 2);
			if (isset($response_header_array[$header])) {
				$response_header_array[$header] .= "\n" . $value;
			} else $response_header_array[$header] = $value;
		}

		$response = array('http_code' => $http_code, 'data' => $response_body, 'headers' => $headers);

		//log here		
		//print_r($response);

		return $response;
	}
}

class OrkutAuth {

	const REQUEST_TOKEN_URL = 'https://www.google.com/accounts/OAuthGetRequestToken';
	const AUTHORIZE_URL = 'https://www.google.com/accounts/OAuthAuthorizeToken';
	const ACCESS_TOKEN_URL = 'https://www.google.com/accounts/OAuthGetAccessToken';
	const REST_ENDPOINT = 'http://sandbox.orkut.com/social/rest/';

	/* production */
	const RPC_ENDPOINT = 'http://www.orkut.com/social/rpc';
	protected $oauthRequestTokenParams = array('scope' => 'http://orkut.gmodules.com/social/');


	/** sandbox 
	const RPC_ENDPOINT = 'http://sandbox.orkut.com/social/rpc';
	protected $oauthRequestTokenParams = array('scope' => 'http://sandbox.orkut.gmodules.com/social');
	*/

	protected $consumerToken;
	protected $signature;
	protected $accessToken;

	public function __construct($consumerKey, $consumerSecret) {
		
		$this->consumerToken = new OAuthConsumer($consumerKey, $consumerSecret, NULL);
		$this->signature = new OAuthSignatureMethod_HMAC_SHA1();
	}
	
	/**
	 * How to describe 3-legged Oauth in a simple way ? Well, will try here, supposing we are not logged in
	 * 1- request a token from REQUEST_TOKEN url
	 * 2- redirect to AUTHORIZE_URL in order to get token and key
	 * 3- some parameters are passed on url, which we use to get an access token. This access token will allow us to call RPC methods.
	 * 4- after getting access token, redirect back to the url which originally sent the auth process.
	 */
	public function login() {
		// do we have an active token in the session ?
		if($this->getAccessToken()!=null) {	
			$this->accessToken = $this->getAccessToken();
		}
		else {

			// first step, nothing set, start dancing
			if(!isset($_GET['oauth_verifier']) && !isset($_GET['oauth_token'])) {

				//setup the callback url, so we can go back further
				$callbackUrl = 'http://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

				// get request token
        			$token = $this->obtainRequestToken($callbackUrl);
			        
				// change, instead of passing through url
				$_SESSION['orkut_key'] = $token->key;
				$_SESSION['orkut_secret'] = $token->secret;

				// now we can redirect
				$authorizeRedirect = self::AUTHORIZE_URL . "?oauth_token={$token->key}";
				header("Location: $authorizeRedirect");

				
			}
			// ok, now we are almost done, just upgrade request token and redirect
			else {

				if(!isset($_SESSION['orkut_key']))
					throw new Exception('session expired, login again.');

				$this->upgradeRequestToken($_SESSION['orkut_key'], $_SESSION['orkut_secret']);

				// ok, finally we are ready to go.
				$_SESSION['oauth_token'] = serialize($this->accessToken);

				// unset session stuff
				$_SESSION['orkut_key']=NULL;
				$_SESSION['orkut_secret']=NULL;

				unset($_SESSION['orkut_key']);
				unset($_SESSION['orkut_secret']);


				//header("Location: $originalUrl");
			}
		}

	}

	protected function upgradeRequestToken($requestToken, $requestTokenSecret) {

		$requestTokenSecret = str_replace(' ','+',$requestTokenSecret);
		$ret = $this->requestAccessToken($requestToken, $requestTokenSecret);

		if ($ret['http_code'] == '200') {

			$matches = array();
			@parse_str($ret['data'], $matches);

			if (!isset($matches['oauth_token']) || !isset($matches['oauth_token_secret'])) {
				throw new osapiException("Error authorizing access key (result was: {$ret['data']})");
			}

			// The token was upgraded to an access token, we can now continue to use it.
			$this->accessToken = new OAuthConsumer(urldecode($matches['oauth_token']), urldecode($matches['oauth_token_secret']));

			// normalize our token
			$this->accessToken->secret = str_replace(' ','+',OAuthUtil::urldecode_rfc3986($this->accessToken->secret));

			return $this->accessToken;
		} 
		else {
			throw new Exception("Error requesting oauth access token, code " . $ret['http_code'] . ", message: " . $ret['data']);
		}
	}

	protected function requestAccessToken($requestToken, $requestTokenSecret) {

		$accessToken = new OAuthConsumer($requestToken, $requestTokenSecret);
		$accessRequest = OAuthRequest::from_consumer_and_token($this->consumerToken, $accessToken, 'GET', self::ACCESS_TOKEN_URL, array());
		$accessRequest->set_parameter('oauth_verifier',$_GET['oauth_verifier']);
		$accessRequest->sign_request($this->signature, $this->consumerToken, $accessToken);

		$header = $this->getOAuthHeader($accessRequest);

		return CurlRequest::send(self::ACCESS_TOKEN_URL, 'GET', false, $header);
	}
	

	// need to improve this function, allowing to read from other sources.
	protected function getAccessToken() {
		if(isset($_SESSION["oauth_token"]))
			return unserialize($_SESSION["oauth_token"]);
		else
			return null;
	}

	protected function obtainRequestToken($callbackUrl) {

		$ret = $this->requestRequestToken($callbackUrl);

		if ($ret['http_code'] == '200') {

			parse_str($ret['data'], $str);
			
			if (count($str) != 3) {
				throw new Exception("Error retrieving request key ({$ret['data']})");	
			}

			return new OAuthToken(urldecode($str['oauth_token']), urldecode($str['oauth_token_secret']));

		} 
		else {
			throw new Exception("Error requesting oauth request token, code " . $ret['http_code'] . ", message: " . $ret['data']);	
		}
	}

	protected function requestRequestToken($callbackUrl) {
		$requestTokenRequest = OAuthRequest::from_consumer_and_token($this->consumerToken, NULL, "GET", self::REQUEST_TOKEN_URL, $this->oauthRequestTokenParams);

		foreach($this->oauthRequestTokenParams as $key => $value) {	
			$requestTokenRequest->set_parameter($key, $value);
		}

		// got this from oauth playground, if not present, shows a yellow message warning
		$requestTokenRequest->set_parameter('oauth_callback', $callbackUrl);

		$requestTokenRequest->sign_request($this->signature, $this->consumerToken, NULL);

		$header = $this->getOAuthHeader($requestTokenRequest);


		return CurlRequest::send(self::REQUEST_TOKEN_URL.'?scope='.$this->oauthRequestTokenParams['scope'], 'GET', false, $header);
	}

	protected function getOAuthHeader($request) {

		$header="Authorization: OAuth ";
		foreach($request->get_parameters() as $k=>$v) {

			$v = OAuthUtil::urlencode_rfc3986($v);

			if($k!='scope')			
				// concatenate oauth header
				$header.=$k."=\"".$v."\", ";
		}

		if(substr($header, strlen($header)-2, 2)==", ")
			$header=substr($header, 0, strlen($header)-2);

		return array($header);
		
	}
	
	protected function sign2($postBody, $method='POST', $url='', $params=array()) {
		if($url=='')		
			$url = self::RPC_ENDPOINT;

		$headers = array("Content-Type: application/json");
		
		// add some parameters used to sign the request
		$params['oauth_nonce'] = md5(microtime() . mt_rand());
		$params['oauth_version'] = OAuthRequest::$version;
		$params['oauth_timestamp'] = time();
		$params['oauth_consumer_key'] = $this->consumerToken->key;

		if ($this->accessToken != null) {
			$params['oauth_token'] = $this->accessToken->key;
		}
		
		if($method=='POST') {		
			// compute our body hash, base64 + sha1
			$bodyHash = base64_encode(sha1($postBody, true));
			$params['oauth_body_hash'] = $bodyHash;
		}
		
		// create the oauth request
		$oauthRequest = OAuthRequest::from_request($method, $url, $params);
		$oauthRequest->sign_request($this->signature, $this->consumerToken, $this->accessToken);
		
		// return the signed url
		return $oauthRequest->to_url();
	}

	protected function sign($postBody, $method='POST', $url='', $params=array()) {
		if($url=='')		
			$url = self::RPC_ENDPOINT;

		$headers = array("Content-Type: application/json");
		
		// add some parameters used to sign the request
		$params['oauth_nonce'] = md5(microtime() . mt_rand());
		$params['oauth_version'] = OAuthRequest::$version;
		$params['oauth_timestamp'] = time();
		$params['oauth_consumer_key'] = $this->consumerToken->key;

		if ($this->accessToken != null) {
			$params['oauth_token'] = $this->accessToken->key;
		}
		
		if($method=='POST') {		
			// compute our body hash, base64 + sha1
			$bodyHash = base64_encode(sha1($postBody, true));
			$params['oauth_body_hash'] = $bodyHash;
		}
		
		// create the oauth request
		$oauthRequest = OAuthRequest::from_request($method, $url, $params);
		$oauthRequest->sign_request($this->signature, $this->consumerToken, $this->accessToken);
		
		// return the signed url
		return $this->getOAuthHeader($oauthRequest);
	}

}

class Orkut extends OrkutAuth {

	private $message;
	private $messageKeys;
	
	public function __construct($consumerKey, $consumerSecret) {
		parent::__construct($consumerKey, $consumerSecret);
	}
	
	public function addRequest(Array $request, $id) {
		if(isset($this->messageKeys[$id]))
			throw new Exception('A key with name '.$id.' already exists');
		else {
			$this->messageKeys[$id]=1;
			$request['id'] = $id;

			// multiple slashes bug when json-encoding further
			// characters like ' and " gets \\\ when encoding. Kill it now.
			if(get_magic_quotes_gpc())
				@$this->dropSlashes($request);

			$this->message[] = $request;
	
		}
	}
	
	private function dropSlashes(Array &$process) {
		while (list($key, $val) = each($process)) {
			foreach ($val as $k => $v) {
				unset($process[$key][$k]);
				if (is_array($v)) {
					$process[$key][stripslashes($k)] = $v;
					$process[] = &$process[$key][stripslashes($k)];
				} 
				else {
					$process[$key][stripslashes($k)] = stripslashes($v);
				}
			}
		}

	}
	
	public function execute() {
	

		$request = json_encode($this->message);

		//$signedUrl = $this->sign2($request);
		//$headers = array("Content-Type: application/json", $signedUrl[0]);
		//$ret = CurlRequest::send($signedUrl, 'POST', $request, $headers);

		$headers = $this->sign($request);
		$headers[] = "Content-Type: application/json";
		$ret = CurlRequest::send(self::RPC_ENDPOINT, 'POST', $request, $headers);
		
		//fix return bug
		$data = explode('}]{"',$ret['data']);
		if(count($data)==2)
			$data[0]=$data[0].'}]';

		
		// log data- TODO

		return $this->mapData( json_decode($data[0],true) );
	}
	
	private function mapData(Array $ret) {
		
		$response = Array();
		
		foreach($ret as $r) {
			$response[ $r['id'] ] = $r;
		}
		
		return $response;
		
	}

	public function executeCaptcha($captchaPage, $token) {

		preg_match('/[0-9]+/',$captchaPage,$match);
		$params = array('xid'=>$match[0]);

		$signedUrl = $this->sign2('','GET','http://www.orkut.com'.$captchaPage);
		
		$ret = CurlRequest::send($signedUrl, 'GET', false, false);
		return $ret;
		//print_r($ret);
	
	}
}

?>
