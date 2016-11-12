<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Ionic Cloud Client Library for Codeigniter
*
* A library to interface with the Ionic Cloud API. For more information see http://docs.ionic.io/api/http.html
*
* @package CodeIgniter
* @author Yasser AlSamman | https://www.linkedin.com/in/yasseralsamman | y.samman@codersme.com
* @copyright Copyright (c) 2016, Coders Web Solutions.
* @link http://codersme.com
* @version Version 1.0
*/

class Ci_ionic_cloud {
  protected 	$CI;
  private 		$token;
	private 		$app_id;

	/**
  * Class Constructor.
  *
  * The constructor is responsible for loading the library configurations and 
  * initilaize the CI instance and other library variables.
  *
  * @return void
  */
  function __construct() {
    $this->CI =& get_instance();
		$this->CI->config->load('ci_ionic_cloud');
    $this->token = $this->CI->config->item('cloud_api_token');
		$this->app_id = $this->CI->config->item('cloud_app_id');
		if($this->isNullOrEmptyString($this->token)) {
			show_error("The ionic cloud api token is not set", 500);
		}
		if($this->isNullOrEmptyString($this->app_id)) {
			show_error("The ionic cloud app id is not set", 500);
		}
  }

  /**
  * List All Users.
  *
  * Returns a paginated collection of Users as documented at http://docs.ionic.io/api/endpoints/auth.html#get-users .
  *
  * @param integer $page_size the number of items to return in paginated endpoints.
  *
  *	@param integer $page the page number for paginated endpoints.
	*
  * @return string JSON Object contating user list.
  */
	function auth_users_list($page_size = null , $page = null) {
		$data = array ();
		if(isset($page_size) && isset($page)) {
			$data = array (
				'page_size' => $page_size,
				'page'			=> $page
			);
		}
		return $this->curlRequest($this->getRequestUrl('auth','users',$data),'GET');
	}

	/**
  * Create User.
  *
  * Creates a single user as documented at http://docs.ionic.io/api/endpoints/auth.html#post-users
  *
  * @param integer $page_size the number of items to return in paginated endpoints
  *
  *	@param integer $page the page number for paginated endpoints
	*
  * @return void
  */
	function auth_users_create($email, $password, $username='', $name='', $image='', $custom = array()) {
		$reflect = new ReflectionClass(__CLASS__);
		$params = $reflect->getMethod(__FUNCTION__)->getParameters();
		$data['app_id'] = $this->app_id;
		foreach($params as $param) {
			if(isset($param->name) && !$this->isNullOrEmptyString($param->name)) {
				$data[$param->name] = ${$param->name};
			}
			else if(is_array($param->name) && !empty($param->name)){
				$data[$param->name] = json_encode(${$param->name});
			}
		}
		$data = json_encode($data);
		return $this->curlRequest($this->getRequestUrl('auth','users') , 'POST' , $data);
	}

	/**
  * Retrieve A Single User.
  *
  * Returns the retrieved user object as documented at http://docs.ionic.io/api/endpoints/auth.html#get-users .
  *
  * @param string $uuid the cloud id of the user.
	*
  * @return string JSON Object with the user info.
  */
	function auth_users_retrieve($uuid) {
		if(isset($uuid)) {
			return $this->curlRequest($this->getRequestUrl('auth','users',$uuid),'GET');
		}
	}

	/**
  * Generate Proper Request to Ionic Cloud.
  *
  * Returns a concatinated string for the requested endpoint and method for ionic cloud.
  *
  * @param string $endpoint the name of the service endpoint
  *
  *	@param string $method the requested method name
	*
	* @param string $data the params for GET requests
	*
  * @return void
  */
	private function getRequestUrl($endpoint,$method,$data = null) {
		if(isset($data) && !empty($data)) {
			if(is_array($data)) {
				$data = "?".http_build_query($data);
			}
			print_r("https://api.ionic.io/$endpoint/$method/$data");
			return "https://api.ionic.io/$endpoint/$method/$data";
		}
		return "https://api.ionic.io/$endpoint/$method";
	}

	/**
	* Perform Call Request to Ionic Cloud.
	*
	* Returns the exact response from the ionic cloud.
	*
	* @param string $url the endpoint URL.
	*
	*	@param string $method the requested method type (GET , POST)
	*
	* @param string $data the params for POST requests
	*
	* @return string the response from curl request 
	*/
  private function curlRequest($url,$method,$data = null) {
    $curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer $this->token",
				"Content-Type: application/json"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return $err;
		} else {
			return $response;
		}
  }

	/**
	* Check String for Errors.
	*
	* Returns the exact response from the ionic cloud.
	*
	* @param string $str the string to check
	*
	* @return boolean
	*/
	private function isNullOrEmptyString($str){
    return (!isset($str) || trim($str)==='');
	}
}