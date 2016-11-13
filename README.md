# Ionic Cloud Client Library for CodeIgniter
###### (Under Development)

This library is supposed to provide an easy code to use the ionic cloud HTTP API.
please visit [Ionic Cloud HTTP API Docs](http://docs.ionic.io/api/http.html) for more information.

### Supported Endpoints
The current development supports Auth Endpoint Only. the deploy and push endpoints are yet to come.

### Setup
1. Cloud Setup: 
  * [Generate your API token](http://docs.ionic.io/api/http.html#generating-your-api-token) from your ionic cloud dashboard.
2. Library Setup:
  * Copy 'application/config/ci\_ionic\_cloud.php' and 'application/libraries/Ci\_ionic\_cloud.php' to their respected locations.
  * Open 'application/config/ci\_ionic\_cloud.php' and paste in your APP\_ID and API\_TOKEN from your ionic cloud dashboard.

### Usage
First of all you need to load the library in you controller
`$this->load->library('ci_ionic_cloud');`

* [List:](http://docs.ionic.io/api/endpoints/auth.html#get-users)

  `$this->ci_ionic_cloud->auth_users_list(array(`

  `   'page_size'=>1,`

  `   'page'=>1`

  `));`

* [Create:](http://docs.ionic.io/api/endpoints/auth.html#post-users)

  `$this->ci_ionic_cloud->auth_users_create(array(`

	`   'email' => 'someone@something.com', //Required` 

	`   'password' => 'secretepassword', //Required` 

	`   'username' => 'somebody',` 

	`   'name' => 'john doe',`

	`   'image' => 'http://www.profcoaching.org/profile/blank.png',`

	`   'custom' => array(`

	`     'birthdate' => '22/10/1999',`

	`     'father name' => 'Mark' `

	`   )`

	`));`

* [Retrieve:](http://docs.ionic.io/api/endpoints/auth.html#get-users-user_uuid)

  `$this->ci_ionic_cloud->auth_users_retrieve(array(`

  `  'uuid'=> 'user_id_from_cloud' //Required` 

  `));`

* [Update:](http://docs.ionic.io/api/endpoints/auth.html#patch-users-user_uuid)

  `$this->ci_ionic_cloud->auth_users_update(array(`

  `   'uuid' => 'user_id_from_cloud', //Required`
	
  `   'email' => 'someone@something.com',` 
  
	`   'password' => 'secretepassword',` 

	`   'username' => 'somebody',` 

	`   'name' => 'john doe',`

	`   'image' => 'http://www.profcoaching.org/profile/blank.png',`

	`   'custom' => array(`

	`     'birthdate' => '22/10/1999',`

	`     'father name' => 'Mark' `

	`   )`

	`));`

* [Delete:](http://docs.ionic.io/api/endpoints/auth.html#delete-users-user_uuid)

  `$this->ci_ionic_cloud->auth_users_delete(array(`

  `  'uuid'=> 'user_id_from_cloud' //Required` 

  `));`

  ### License
  GNU General Public License v 3.0

  Sponsored by [Coders Web Solutions](http://codersme.com)