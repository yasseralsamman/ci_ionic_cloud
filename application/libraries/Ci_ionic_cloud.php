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
  * @param array $params array containing page_siza and page parameters
	*
  * @return array list of user objects.
  */
	function auth_users_list($params) {
		if(isset($params['page_size']) && isset($params['page'])) {
			$reponse = json_decode($this->curlRequest($this->getRequestUrl('auth','users',$params),'GET'));
			if($reponse->meta->status == 200) {
				return $reponse->data;
			}
		}
		return null;
	}

	/**
  * Create User.
  *
  * Creates a single user as documented at http://docs.ionic.io/api/endpoints/auth.html#post-users
  *
  * @param array $params contains the request paremeters for the create function
	*
  * @return object Object contating user info.
  */
	function auth_users_create($params) {
		$params['app_id'] = $this->app_id;
		if(isset($params['email']) && isset($params['password'])) {
			$params = json_encode($params);
			$reponse = json_decode($this->curlRequest($this->getRequestUrl('auth','users') , 'POST' , $params));
			if($response->meta->status == 201) {
				return $response->data;
			}
		}	
		return null;
	}

	/**
  * Retrieve A Single User.
  *
  * Returns the retrieved user object as documented at http://docs.ionic.io/api/endpoints/auth.html#get-users-user_uuid .
  *
  * @param array $params contains the cloud id of the user uuid.
	*
  * @return object Object contating user info.
  */
	function auth_users_retrieve($params) {
		if(isset($params['uuid'])) {
			$reponse = json_decode($this->curlRequest($this->getRequestUrl('auth','users',$params['uuid']),'GET'));
			if($reponse->meta->status == 200) {
				return $reponse->data;
			}
		}
		return null;
	}

	/**
  * Update User.
  *
  * Update a single user as documented at http://docs.ionic.io/api/endpoints/auth.html#patch-users-user_uuid
  *
	* @param string $params contains the request paremeters for the update function
	*
  * @return object Object contating user info after update.
  */
	function auth_users_update($params) {
		if(isset($params['uuid'])) {
			$uuid = $params['uuid'];
			unset($params['uuid']);
			$params = json_encode($params);
			$response = json_decode($this->curlRequest($this->getRequestUrl('auth','users',$uuid) , 'PATCH' , $params));
			if($response->meta->status == 200) {
				return $response->data;
			}
		}
		return null;
	}

	/**
  * Delete A Single User.
  *
  * Returns 204 response as documented at http://docs.ionic.io/api/endpoints/auth.html#delete-users-user_uuid
  *
  * @param array $params contains the cloud id of the user uuid.
	*
  * @return boolean True if the user deleted successfully 
  */
	function auth_users_delete($params) {
		if(isset($params['uuid'])) {
			$response = json_decode($this->curlRequest($this->getRequestUrl('auth','users',$params['uuid']),'DELETE'));
			if(isset($response) && $response->meta->status == 404) {
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
  * Retrieve A Single Users Custom Data.
  *
  * Returns the retrieved user custom data object as documented at http://docs.ionic.io/api/endpoints/auth.html#get-users-user_uuid-custom .
  *
  * @param array $params contains the cloud id of the user uuid.
	*
  * @return object Object contating user custom data.
  */
	function auth_users_retrieve_custom_data($params) {
		if(isset($params['uuid'])) {
			$reponse = json_decode($this->curlRequest($this->getRequestUrl('auth','users',$params['uuid']."/custom"),'GET'));
			if($reponse->meta->status == 200) {
				return $reponse->data;
			}
		}
		return null;
	}

	/**
  * Replace User Custom Data.
  *
  * Replace a single user's custom data as documented at http://docs.ionic.io/api/endpoints/auth.html#put-users-user_uuid-custom
  *
	* @param string $params contains the request paremeters for the update function
	*
  * @return object Object contating custom user data after Replace.
  */
	function auth_users_replace_custom_data($params) {
		if(isset($params['uuid'])) {
			$uuid = $params['uuid'];
			unset($params['uuid']);
			$params = json_encode($params);
			$response = json_decode($this->curlRequest($this->getRequestUrl('auth','users',$uuid.'/custom') , 'PUT' , $params));
			if($response->meta->status == 200) {
				return $response->data;
			}
		}
		return null;
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
		$baseUrl = 'https://api.ionic.io';
		if(isset($data) && !empty($data)) {
			if(is_array($data)) {
				$data = "?".http_build_query($data);
			}
			return "$baseUrl/$endpoint/$method/$data";
		}
		return "$baseUrl/$endpoint/$method";
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