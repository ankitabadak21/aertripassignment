<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use \Firebase\JWT\JWT;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

// session_start(); //we need to call PHP's session object to access it through CI
class Api extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('apimodel', '', TRUE);
		// Load these helper to create JWT tokens
		$this->load->helper(['core_helper', 'jwt', 'authorization_helper']);

		//$this->load->helper(['jwt', 'authorization']);

		ini_set('memory_limit', '25M');
		ini_set('upload_max_filesize', '25M');
		ini_set('post_max_size', '25M');
		ini_set('max_input_time', 3600);
		ini_set('max_execution_time', 3600);
		ini_set('memory_limit', '-1');
		allowCrossOrgin();
	}

	function getallheaders_new()
	{
		return $response_headers = getallheaders_values();
	}

	//For generating random string
	private function generateRandomString($length = 8, $charset = "")
	{
		if ($charset == 'N') {
			$characters = '0123456789';
		} else {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	private function verify_request($token)
	{
		// Get all the headers
		//$headers = $this->input->request_headers();
		// Extract the token
		//$token = $headers['Authorization'];
		// Use try-catch
		// JWT library throws exception if the token is not valid
		try {
			// Validate the token
			// Successfull validation will return the decoded user data else returns false
			$data = AUTHORIZATION::validateToken($token);
			if ($data === false) {
				return json_encode(array("status_code" => "401", "Metadata" => array("Message" => "Unauthorized Access!"), "Data" => NULL));
				exit;
			} else {
				return $data;
			}
		} catch (Exception $e) {

			// Token is invalid
			// Send the unathorized access message
			
			return json_encode(array("status_code" => "401", "Metadata" => array("Message" => "Unauthorized Access!"), "Data" => NULL));
			exit;
		}
	}

	/*function checkToken($token) {
		$check = $this->verify_request($token);
		if($check){
			return $check;
		}else{
			echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Unauthorized Access!" ), "Data" => NULL ));
			exit;
		}
	}*/

	function homeT()
	{
		$check = $this->verify_request($_POST['utoken']);
		//$check = $this->checkToken($_POST['utoken']);
		echo "<pre>";
		print_r($check);
		if (!empty($check->username)) {
			//kljkldsjflkdsjlkfj
		} else {
			return $check;
		}
		//echo $check->username;
		exit;
	}

	//For login all users
	function userLogin()
	{
		// echo json_encode("here");exit;
		//echo "<pre>";print_r($_POST);exit;
		if (!empty($_POST) && isset($_POST)) {

			if (empty($_POST['username'])) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Please enter username."), "Data" => NULL));
				exit;
			}

			if (!empty($_POST['password'])) {
				$password = md5($_POST['password']);
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Please enter password."), "Data" => NULL));
				exit;
			}

			//echo "<pre>";print_r($_POST);exit;

			$condition = "username='" . $_POST['username'] . "' &&  password='" . $password . "' ";

			$result_login = $this->apimodel->login_check($condition);
			// print_r($result_login);exit;
			$result_data = $result_login[0];


			$utoken = $result_data['id'];
			$success_msg = "Login Successfull. ";

			if (is_array($result_login) && count($result_login) > 0) {

				//JWT
				/*$kunci = $this->config->item('jwtkey');
				$token['id'] = $result_data['employee_id'];  //From here
				//$token['username'] = $u;
				$date = new DateTime();
				$token['iat'] = $date->getTimestamp();
				$token['exp'] = $date->getTimestamp() + 60*60*5; //To here is to generate token
				$output['token'] = JWT::encode($token,$kunci ); //This is the output token
				*/

				//$token = generateToken(['username' => $result_data['employee_id']]);
				$date = new DateTime();
				$tokenData = array('username' => $result_data['id'], 'iat' => $date->getTimestamp(), 'exp' => $date->getTimestamp() + 60 * 60 * 5);
				$token = AUTHORIZATION::generateToken($tokenData);

				//echo "<pre>";print_r($token);exit;

				$result_data['utoken'] = $token;

				//inserting last login
				$last_login = array();
				$last_login['last_login'] = date("Y-m-d H:i:s");
				$this->apimodel->updateRecord('tbl_admin', $last_login, "id='" . $result_data['id'] . "' ");

				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => $success_msg), "Data" => $result_data));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Incorrect Username or Password."), "Data" => NULL));
				exit;
			}
		} else {
			echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Hearder section empty."), "Data" => NULL));
			exit;
		}
	}

	//Get get user details
	function getLoginUserDetails()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getLoginUserDetails($_POST['id']);
			$get_user_locations = $this->apimodel->getSortedData("location_id", "user_locations", "user_id = '" . $_POST['id'] . "'");
			$result = array();
			$result['user_data'] = $get_result;
			$result['user_locations'] = $get_user_locations;
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Check duplicate creditor
	function checkDuplicateUser()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "(email_id='" . $_POST['email_id'] . "' || user_name='" . $_POST['user_name'] . "') ";
			if (!empty($_POST['employee_id'])) {
				$condition .= " && employee_id  !='" . $_POST['employee_id'] . "' ";
			}
			$get_result = $this->apimodel->getdata("master_employee", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit User
	function addEditUser()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			//echo "<pre>";print_r($_POST);exit;
			$data = array();
			$data['employee_fname'] = $_POST['employee_fname'];
			if (!empty($_POST['employee_mname'])) {
				$data['employee_mname'] = $_POST['employee_mname'];
			} else {
				$data['employee_mname'] = "";
			}

			$data['employee_lname'] = $_POST['employee_lname'];
			if (!empty($_POST['employee_code'])) {
				$data['employee_code'] = $_POST['employee_code'];
			}

			$full_name = "";
			if (!empty($_POST['employee_fname'])) {
				$full_name .= $_POST['employee_fname'];
			}
			if (!empty($_POST['employee_mname'])) {
				$full_name .= " " . $_POST['employee_mname'];
			}
			if (!empty($_POST['employee_lname'])) {
				$full_name .= " " . $_POST['employee_lname'];
			}

			$data['employee_full_name'] = $full_name;

			if (!empty($_POST['date_of_joining'])) {
				$data['date_of_joining'] = date("Y-m-d", strtotime($_POST['date_of_joining']));
			}
			$data['email_id'] = $_POST['email_id'];
			$data['mobile_number'] = $_POST['mobile_number'];
			if (!empty($_POST['user_name'])) {
				$data['user_name'] = $_POST['user_name'];
			}
			if (!empty($_POST['password'])) {
				$data['employee_password'] = $_POST['password'];
			}
			if (!empty($_POST['role_id'])) {
				$data['role_id'] = $_POST['role_id'];
			}
			if (!empty($_POST['company_id'])) {
				$data['company_id'] = $_POST['company_id'];
			}
			if (!empty($_POST['isactive'])) {
				$data['isactive'] = $_POST['isactive'];
			}

			if (!empty($_POST['employee_id'])) {
				$result = $this->apimodel->updateRecord('master_employee', $data, "employee_id='" . $_POST['employee_id'] . "' ");

				//location
				if (!empty($_POST['location_id'])) {
					$this->apimodel->delrecord("user_locations", "user_id", $_POST['employee_id']);
					for ($i = 0; $i < sizeof($_POST['location_id']); $i++) {
						$loc_data = array();
						$loc_data['user_id'] = $_POST['employee_id'];
						$loc_data['location_id'] = (!empty($_POST['location_id'][$i])) ? $_POST['location_id'][$i] : '';

						$rs = $this->apimodel->insertData('user_locations', $loc_data, '1');
					}
				}

				if(!empty($_POST['creditor_id'])){

					$this->apimodel->delrecord("sm_creditor_mapping", "sm_id", $_POST['employee_id']);
					//for ($i = 0; $i < sizeof($_POST['location_id']); $i++) {
						$loc_data = array();
						$loc_data['sm_id'] = $_POST['employee_id'];
						$loc_data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';

						$rs = $this->apimodel->insertData('sm_creditor_mapping', $loc_data, '1');
					//}
				}
			} else {
				$result = $this->apimodel->insertData('master_employee', $data, 1);
				//location
				if (!empty($_POST['location_id'])) {
					for ($i = 0; $i < sizeof($_POST['location_id']); $i++) {
						$loc_data = array();
						$loc_data['user_id'] = $result;
						$loc_data['location_id'] = (!empty($_POST['location_id'][$i])) ? $_POST['location_id'][$i] : '';

						$rs = $this->apimodel->insertData('user_locations', $loc_data, '1');
					}
				}
				if(!empty($_POST['creditor_id'])){

					//for ($i = 0; $i < sizeof($_POST['location_id']); $i++) {
						$loc_data = array();
						$loc_data['sm_id'] = $result;
						$loc_data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';

						$rs = $this->apimodel->insertData('sm_creditor_mapping', $loc_data, '1');
					//}
				}
			}

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For creditor listing
	function EmployeeListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getEmployeeList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get creditor form data
	function getCreditorFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getCreditorFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Check duplicate creditor
	function checkDuplicateEmployee()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "employee_name='" . $_POST['employee_name'] . "' AND department_id ='" . $_POST['department_id'] . "'  ";
			if (!empty($_POST['emp_id'])) {
				$condition .= " && emp_id !='" . $_POST['emp_id'] . "' ";
			}
			$get_result = $this->apimodel->getdata("tbl_employee", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit creditor
	function addEditCreditor()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['creaditor_name'] = (!empty($_POST['creaditor_name'])) ? $_POST['creaditor_name'] : '';
			$data['creditor_code'] = (!empty($_POST['creditor_code'])) ? $_POST['creditor_code'] : '';
			$data['ceditor_email'] = (!empty($_POST['ceditor_email'])) ? $_POST['ceditor_email'] : '';
			$data['creditor_mobile'] = (!empty($_POST['creditor_mobile'])) ? $_POST['creditor_mobile'] : '';
			$data['creditor_phone'] = (!empty($_POST['creditor_phone'])) ? $_POST['creditor_phone'] : '';
			$data['creditor_pancard'] = (!empty($_POST['creditor_pancard'])) ? $_POST['creditor_pancard'] : '';
			$data['creditor_gstn'] = (!empty($_POST['creditor_gstn'])) ? $_POST['creditor_gstn'] : '';
			$data['creditor_logo'] = (!empty($_POST['creditor_logo'])) ? $_POST['creditor_logo'] : '';
			$data['address'] = (!empty($_POST['address'])) ? $_POST['address'] : '';
			$data['isactive'] = (!empty($_POST['isactive'])) ? $_POST['isactive'] : 0;

			if (!empty($_POST['creditor_id'])) {
				$result = $this->apimodel->updateRecord('master_ceditors', $data, "creditor_id='" . $_POST['creditor_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('master_ceditors', $data, 1);
			}

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Delete creditor
	function delEmployee()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->updateRecord('tbl_employee', $data, "emp_id='" . $_POST['id'] . "' ");
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For permission listing
	function permissionListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getPermissionList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get permission form data
	function getPermissionFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getPermissionFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Check duplicate permission
	function checkDuplicatePermission()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "perm_desc='" . $_POST['perm_desc'] . "' ";
			if (!empty($_POST['perm_id'])) {
				$condition .= " && perm_id !='" . $_POST['perm_id'] . "' ";
			}
			$get_result = $this->apimodel->getdata("permissions", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}


	//Add Edit permission
	function addEditPermission()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['perm_desc'] = (!empty($_POST['perm_desc'])) ? $_POST['perm_desc'] : '';
			if (empty($_POST['perm_id'])) {
				$data['created_on'] = date("Y-m-d H:i:s");
				$data['created_by'] = $_POST['login_user_id'];
				$data['updated_by'] = $_POST['login_user_id'];
			} else {
				$data['updated_by'] = $_POST['login_user_id'];
			}

			if (!empty($_POST['perm_id'])) {
				$result = $this->apimodel->updateRecord('permissions', $data, "perm_id='" . $_POST['perm_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('permissions', $data, 1);
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}


	//For role listing
	function roleListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getRoleList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get permissions
	function getPermissionsData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSortedData("perm_id,perm_desc", "permissions", "", "perm_desc", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get roles form data
	function getRoleFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$result = array();
			$get_result = $this->apimodel->getRoleFormData($_POST['id']);
			$get_role_perms = $this->apimodel->getSortedData("perm_id", "role_perm", "role_id = '" . $_POST['id'] . "'");
			$result['role_data'] = $get_result;
			$result['role_perms'] = $get_role_perms;
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}


	//Check duplicate permission
	function checkDuplicateRole()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "role_name='" . $_POST['role_name'] . "' ";
			if (!empty($_POST['role_id'])) {
				$condition .= " && role_id !='" . $_POST['role_id'] . "' ";
			}
			$get_result = $this->apimodel->getdata("roles", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit roles
	function addEditRoles()
	{
		//echo "<pre>post";print_r($_POST);
		//for($i=0;$i<sizeof($_POST['role_permissions']);$i++){
		//echo $_POST['role_permissions'][$i];
		//}
		//exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['role_name'] = (!empty($_POST['role_name'])) ? $_POST['role_name'] : '';

			if (!empty($_POST['role_id'])) {
				$result = $this->apimodel->updateRecord('roles', $data, "role_id='" . $_POST['role_id'] . "' ");
				if (!empty($result)) {
					if (!empty($_POST['role_permissions'])) {
						$this->apimodel->delrecord("role_perm", "role_id", $_POST['role_id']);
						for ($i = 0; $i < sizeof($_POST['role_permissions']); $i++) {
							$perm_data = array();
							$perm_data['role_id'] = $_POST['role_id'];
							$perm_data['perm_id'] = $_POST['role_permissions'][$i];
							$rs = $this->apimodel->insertData('role_perm', $perm_data, '1');
						}
					}
				}
			} else {
				$result = $this->apimodel->insertData('roles', $data, 1);
				if (!empty($result)) {
					if (!empty($_POST['role_permissions'])) {
						for ($i = 0; $i < sizeof($_POST['role_permissions']); $i++) {
							$perm_data = array();
							$perm_data['role_id'] = $result;
							$perm_data['perm_id'] = $_POST['role_permissions'][$i];
							$rs = $this->apimodel->insertData('role_perm', $perm_data, '1');
						}
					}
				}
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For user listing
	function userListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getUserList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get roles
	function getRolesData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSortedData("role_id,role_name", "roles", "", "role_name", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Delete user
	function delUser()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->updateRecord('master_employee', $data, "employee_id ='" . $_POST['id'] . "' ");
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For location listing
	function locationListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getlocationList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get location form data
	function getLocationFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getLocationFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Check duplicate location
	function checkDuplicateLocation()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "location_name='" . $_POST['location_name'] . "' ";
			if (!empty($_POST['location_id'])) {
				$condition .= " && location_id !='" . $_POST['location_id'] . "' ";
			}
			//echo $condition;
			//exit;
			$get_result = $this->apimodel->getdata("master_location", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}


	//Add Edit location
	function addEditLocation()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['location_id'] = (!empty($_POST['location_id'])) ? $_POST['location_id'] : '';
			$data['location_name'] = (!empty($_POST['location_name'])) ? $_POST['location_name'] : '';

			if (!empty($_POST['location_id'])) {
				$result = $this->apimodel->updateRecord('master_location', $data, "location_id='" . $_POST['location_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('master_location', $data, 1);
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For branches listing
	function branchListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->branchListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get creditors
	function getEmployeeFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			if(!empty($_POST['id'])){
				$condition = "isactive='1' && emp_id='".$_POST['id']."' ";
			}else{
				$condition = "isactive='1' ";
			}
			$get_result['emp_details'] = $this->apimodel->getSortedData("*", "tbl_employee", $condition, "emp_id", "asc");
			$get_result['contact_details'] = $this->apimodel->getSortedData("*", "tbl_employee_contacts", "emp_id='".$_POST['id']."'", "id", "asc");
			$get_result['address_details'] = $this->apimodel->getSortedData("*", "tbl_employee_address", "emp_id='".$_POST['id']."' ", "id", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get locations
	function getLocationsData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSortedData("location_id,location_name", "master_location", "", "location_name", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get branches form data
	function getBranchesFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getBranchesFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Check duplicate branch
	function checkDuplicateBranch()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "branch_name='" . $_POST['branch_name'] . "' ";
			if (!empty($_POST['branch_id'])) {
				$condition .= " && branch_id !='" . $_POST['branch_id'] . "' ";
			}
			$get_result = $this->apimodel->getdata("creditor_branches", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit branches
	function addEditBranches()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['branch_name'] = (!empty($_POST['branch_name'])) ? $_POST['branch_name'] : '';
			$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			$data['location_id'] = (!empty($_POST['location_id'])) ? $_POST['location_id'] : '';
			$data['contact_no'] = (!empty($_POST['contact_no'])) ? $_POST['contact_no'] : '';
			$data['email_id'] = (!empty($_POST['email_id'])) ? $_POST['email_id'] : '';
			$data['isactive'] = (!empty($_POST['isactive'])) ? $_POST['isactive'] : '';

			if (empty($_POST['branch_id'])) {
				$data['created_on'] = date("Y-m-d H:i:s");
				$data['created_by'] = $_POST['login_user_id'];
				$data['updated_by'] = $_POST['login_user_id'];
			} else {
				$data['updated_by'] = $_POST['login_user_id'];
			}

			if (!empty($_POST['branch_id'])) {
				$result = $this->apimodel->updateRecord('creditor_branches', $data, "branch_id='" . $_POST['branch_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('creditor_branches', $data, 1);
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Delete branch
	function delBranch()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->updateRecord('creditor_branches', $data, "branch_id='" . $_POST['id'] . "' ");
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get login user access
	function getLoginUserAccess()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getLoginUserAccess($_POST['role_id']);
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For SM and creditor mapping listing
	function smCreditorMappingListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->smCreditorMappingListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get SM
	function getSMData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			if(!empty($_POST['sm_id'])){
				$condition = "employee_id='".$_POST['sm_id']."' ";
			}else{
				$condition = "isactive='1' && role_id='3'";
			}
			$get_result = $this->apimodel->getSortedData("employee_id,employee_full_name", "master_employee", $condition, "employee_full_name", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get sm & creditor mapping form data
	function getSMCreditorFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSMCreditorFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get sm & creditor mapping by user id
	function getSMCreditorMappingByUserId()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSMCreditorMappingByUserId($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit SM Creditor mapping
	function addEditSMCreditorMapping()
	{
		//echo "<pre>post";print_r($_POST);
		//exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$result = 1;

			if (!empty($_POST['sm_creditor_id'])) {
			} else {
				/*if(!empty($_POST['creditor_id'])){
					$this->apimodel->delrecord_condition("sm_creditor_mapping","sm_id='".$_POST['sm_id']."' ");
					for($i=0;$i<sizeof($_POST['creditor_id']);$i++){
						$data = array();
						$data['sm_id']= $_POST['sm_id'];
						$data['creditor_id']= $_POST['creditor_id'][$i];
						$data['updated_by']= $_POST['login_user_id'];
						$rs = $this->apimodel->insertData('sm_creditor_mapping',$data,'1');
					}
				}*/

				if (!empty($_POST['sm_id'])) {
					//$this->apimodel->delrecord_condition("sm_creditor_mapping","creditor_id='".$_POST['creditor_id']."' ");
					for ($i = 0; $i < sizeof($_POST['sm_id']); $i++) {
						$chk_mapping = $this->apimodel->getSortedData("sm_id,creditor_id", "sm_creditor_mapping", "sm_id='" . $_POST['sm_id'][$i] . "' && creditor_id='" . $_POST['creditor_id'] . "' ", "sm_id", "asc");
						//echo "<pre>";print_r($get_result);exit;

						if (empty($chk_mapping)) {
							$data = array();
							$data['sm_id'] = $_POST['sm_id'][$i];
							$data['creditor_id'] = $_POST['creditor_id'];
							$data['updated_by'] = $_POST['login_user_id'];
							$rs = $this->apimodel->insertData('sm_creditor_mapping', $data, '1');
						}
					}
				}
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Delete SM Creditor mapping
	function delSMCreditor()
	{
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->delrecord('sm_creditor_mapping', 'sm_creditor_id', $_POST['id']);
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get states
	function getStateData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSortedData("state_id,state_name", "states", "isactive='1' ", "state_name", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For lead listing
	function leadListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->leadListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//For Lead Export
	function exportLeads(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->exportLeads($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}
	}

	//Get creditors
	function getRoleWiseCreditorsData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {


			$get_result = $this->apimodel->getRoleWiseCreditorsData($_POST['user_id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get SM locations
	function getSMLocations()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {


			$get_result = $this->apimodel->getSMLocations($_POST['user_id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get creditors plans
	function getCreditorsPlansData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSortedData("plan_id,plan_name", "master_plan", "creditor_id='" . $_POST['creditor_id'] . "' ", "plan_name", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get All SM except already map to selected creditor
	function getSMDataCreditorWise()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			//echo $_POST['creditor_id'];exit;
			// Get already mapped sm's
			$getAlreadySM = $this->apimodel->getSortedData("sm_id", "sm_creditor_mapping", "creditor_id='" . $_POST['creditor_id'] . "' ", "sm_creditor_id", "desc");
			//echo "<pre>";print_r($getAlreadySM);exit;
			//echo $getAlreadySM[0]->sm_id;
			//exit;

			$condition = "";
			if (!empty($getAlreadySM)) {
				$sm_id_arr = array();
				for ($i = 0; $i < sizeof($getAlreadySM); $i++) {
					$sm_id_arr[] = $getAlreadySM[$i]->sm_id;
				}
				$sm_ids = implode(",", $sm_id_arr);
				$condition = "isactive='1' && role_id='3' && employee_id NOT IN ($sm_ids) ";
				//echo $condition; exit;

			} else {
				$condition = "isactive='1' && role_id='3'";
			}

			$get_result = $this->apimodel->getSortedData("employee_id,employee_full_name", "master_employee", $condition, "employee_full_name", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Lead
	function addLead()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			//echo "<pre>";print_r($_POST);exit;
			$result = "";
			//check customer exist
			/*$cust_condition = "mobile_no='" . $_POST['mobile_number'] . "' || email_id='" . $_POST['email_id'] . "' ";
			$cust_result = $this->apimodel->getdata("master_customer", "customer_id", $cust_condition);
			//echo "<pre>";print_r($cust_result);exit;
			if (!empty($cust_result)) {
				$customer_id = $cust_result[0]['customer_id'];
				//echo $customer_id;exit;
				//check lead already present
				
				//Create Lead
				$lead_data = array();
				$timestamp = time();
				$lead_data['trace_id'] = $customer_id . $timestamp;
				$lead_data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
				$lead_data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
				$lead_data['sales_manager_id'] = (!empty($_POST['sm_id'])) ? $_POST['sm_id'] : '';
				$lead_data['primary_customer_id'] = $customer_id;
				$lead_data['mobile_no'] = (!empty($_POST['mobile_number'])) ? $_POST['mobile_number'] : '';
				$lead_data['email_id'] = (!empty($_POST['email_id'])) ? $_POST['email_id'] : '';

				$lead_data['lan_id'] = (!empty($_POST['lan_id'])) ? $_POST['lan_id'] : '';
				$lead_data['portal_id'] = (!empty($_POST['portal_id'])) ? $_POST['portal_id'] : 'Creditor Portal';
				$lead_data['vertical'] = (!empty($_POST['vertical'])) ? $_POST['vertical'] : 'Vertical';
				$lead_data['loan_amt'] = (!empty($_POST['loan_amt'])) ? $_POST['loan_amt'] : '';
				$lead_data['tenure'] = (!empty($_POST['tenure'])) ? $_POST['tenure'] : '';
				$lead_data['is_coapplicant'] = (!empty($_POST['is_coapplicant'])) ? $_POST['is_coapplicant'] : 'N';
				$lead_data['coapplicant_no'] = (!empty($_POST['coapplicant_no'])) ? $_POST['coapplicant_no'] : 0;

				$lead_data['lead_location_id'] = (!empty($_POST['lead_location_id'])) ? $_POST['lead_location_id'] : 0;

				$lead_data['createdon'] = date("Y-m-d H:i:s");
				$lead_data['createdby'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				//$lead_data['updatedby'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';

				$result = $this->apimodel->insertData('lead_details', $lead_data, 1);
				//$log = insert_lead_log($result, $_POST['login_user_id'], "New lead added.");

				$lead_id = $result;

				$this->apimodel->updateRecord('master_customer', [
					'lead_id' => $lead_id
				], "customer_id =" . $customer_id);

				//Add proposal
				$proposal_data = array();
				$proposal_data['trace_id'] = $lead_data['trace_id'];
				$proposal_data['lead_id'] = $result;
				$proposal_data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
				$proposal_data['customer_id'] = $customer_id;
				$proposal_data['status'] = 'Pending';

				$proposal_data['created_at'] = date("Y-m-d H:i:s");
				$proposal_data['created_by'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				//$proposal_data['updated_by'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';

				$proposal_details_id = $this->apimodel->insertData('proposal_details', $proposal_data, 1);
				
				//log entries
				$lead_id = $result;
				$created_on = date("Y-m-d H:i:s");
				$created_by = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				
				//customer
				//$customer_action = "create_customer";
				//$customer_request_data = json_encode($cust_data);
				//$customer_response_data = json_encode(array("response"=>"Customer added."));
				
				//Lead
				$lead_action = "create_lead";
				$lead_request_data = json_encode($lead_data);
				$lead_response_data = json_encode(array("response"=>"Lead added."));
				
				//Proposal
				$proposal_action = "create_proposal_entry";
				$proposal_request_data = json_encode($proposal_data);
				$proposal_response_data = json_encode(array("response"=>"Proposal entery added."));
				
				/*if(!empty($customer_id)){
					$customerlog = insert_application_log($lead_id, $customer_action, $customer_request_data, $customer_response_data, $created_by);
				}else{
					$customer_response_data = json_encode(array("response"=>"Error in customer insert."));
					$customerlog = insert_application_log($lead_id, $customer_action, $customer_request_data, $customer_response_data, $created_by);
				}*/
				
				/*if(!empty($result)){
					$leadlog = insert_application_log($lead_id, $lead_action, $lead_request_data, $lead_response_data, $created_by);
				}else{
					$customer_response_data = json_encode(array("response"=>"Error in lead insert."));
					$leadlog = insert_application_log($lead_id, $lead_action, $lead_request_data, $lead_response_data, $created_by);
				}
				
				if(!empty($proposal_details_id)){
					$proposallog = insert_application_log($lead_id, $proposal_action, $proposal_request_data, $proposal_response_data, $created_by);
				}else{
					$customer_response_data = json_encode(array("response"=>"Error in lead insert."));
					$leadlog = insert_application_log($lead_id, $proposal_action, $proposal_request_data, $proposal_response_data, $created_by);
				}

			} else { */
				//insert_proposal_log($proposal_details_id,$_POST['login_user_id'],$remark);
				//add customer and lead
				//exit;
				$cust_data = array();
				$cust_data['salutation'] = (!empty($_POST['salutation'])) ? $_POST['salutation'] : '';
				$cust_data['first_name'] = (!empty($_POST['first_name'])) ? $_POST['first_name'] : '';
				$cust_data['middle_name'] = (!empty($_POST['middle_name'])) ? $_POST['middle_name'] : '';
				$cust_data['last_name'] = (!empty($_POST['last_name'])) ? $_POST['last_name'] : '';
				$full_name = '';
				if (!empty($_POST['first_name'])) {
					$full_name .= $_POST['first_name'];
				}
				if (!empty($_POST['middle_name'])) {
					$full_name .= " " . $_POST['middle_name'];
				}
				if (!empty($_POST['last_name'])) {
					$full_name .= " " . $_POST['last_name'];
				}

				$cust_data['full_name'] = $full_name;
				$cust_data['gender'] = (!empty($_POST['gender'])) ? $_POST['gender'] : '';
				$cust_data['dob'] = (!empty($_POST['dob'])) ? date("Y-m-d", strtotime($_POST['dob'])) : '';
				$cust_data['email_id'] = (!empty($_POST['email_id'])) ? $_POST['email_id'] : '';
				$cust_data['mobile_no'] = (!empty($_POST['mobile_number'])) ? $_POST['mobile_number'] : '';
				$cust_data['isactive'] = 1;
				$cust_data['createdon'] = date("Y-m-d H:i:s");
				$cust_data['createdby'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				//$cust_data['updatedby'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';

				$customer_id = $this->apimodel->insertData('master_customer', $cust_data, 1);
				
				//Create Lead
				$lead_data = array();
				$timestamp = time();
				$lead_data['trace_id'] = $customer_id . $timestamp;
				$lead_data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
				$lead_data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
				$lead_data['sales_manager_id'] = (!empty($_POST['sm_id'])) ? $_POST['sm_id'] : '';
				$lead_data['primary_customer_id'] = $customer_id;
				$lead_data['mobile_no'] = (!empty($_POST['mobile_number'])) ? $_POST['mobile_number'] : '';
				$lead_data['email_id'] = (!empty($_POST['email_id'])) ? $_POST['email_id'] : '';

				$lead_data['lan_id'] = (!empty($_POST['lan_id'])) ? $_POST['lan_id'] : '';
				$lead_data['portal_id'] = (!empty($_POST['portal_id'])) ? $_POST['portal_id'] : 'Creditor Portal';
				$lead_data['vertical'] = (!empty($_POST['vertical'])) ? $_POST['vertical'] : 'Vertical';
				$lead_data['loan_amt'] = (!empty($_POST['loan_amt'])) ? $_POST['loan_amt'] : '';
				$lead_data['tenure'] = (!empty($_POST['tenure'])) ? $_POST['tenure'] : '';
				$lead_data['is_coapplicant'] = (!empty($_POST['is_coapplicant'])) ? $_POST['is_coapplicant'] : 'N';
				$lead_data['coapplicant_no'] = (!empty($_POST['coapplicant_no'])) ? $_POST['coapplicant_no'] : 0;
				$lead_data['lead_location_id'] = (!empty($_POST['lead_location_id'])) ? $_POST['lead_location_id'] : 0;

				$lead_data['createdon'] = date("Y-m-d H:i:s");
				$lead_data['createdby'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				//$lead_data['updatedby'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';


				$result = $this->apimodel->insertData('lead_details', $lead_data, 1);
				
				//$log = insert_lead_log($result, $_POST['login_user_id'], "New lead added.");

				$lead_id = $result;

				$this->apimodel->updateRecord('master_customer', [
					'lead_id' => $lead_id
				], "customer_id =" . $customer_id);
				
				//$log = insert_lead_log($result, $_POST['login_user_id'], "New lead added.");
				
				//Add proposal
				$proposal_data = array();
				$proposal_data['trace_id'] = $lead_data['trace_id'];
				$proposal_data['lead_id'] = $result;
				$proposal_data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
				$proposal_data['customer_id'] = $customer_id;
				$proposal_data['status'] = 'Pending';

				$proposal_data['created_at'] = date("Y-m-d H:i:s");
				$proposal_data['created_by'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				//$proposal_data['updated_by'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';

				$proposal_details_id = $this->apimodel->insertData('proposal_details', $proposal_data, 1);

				//insert_proposal_log($proposal_details_id, $_POST['login_user_id'], $remark);
				
				
				//log entries
				$lead_id = $result;
				$created_on = date("Y-m-d H:i:s");
				$created_by = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
				
				//customer
				$customer_action = "create_customer";
				$customer_request_data = json_encode($cust_data);
				$customer_response_data = json_encode(array("response"=>"Customer added."));
				
				//Lead
				$lead_action = "create_lead";
				$lead_request_data = json_encode($lead_data);
				$lead_response_data = json_encode(array("response"=>"Lead added."));
				
				//Proposal
				$proposal_action = "create_proposal_entry";
				$proposal_request_data = json_encode($proposal_data);
				$proposal_response_data = json_encode(array("response"=>"Proposal entery added."));
				
				if(!empty($customer_id)){
					$customerlog = insert_application_log($lead_id, $customer_action, $customer_request_data, $customer_response_data, $created_by);
				}else{
					$customer_response_data = json_encode(array("response"=>"Error in customer insert."));
					$customerlog = insert_application_log($lead_id, $customer_action, $customer_request_data, $customer_response_data, $created_by);
				}
				
				if(!empty($result)){
					$leadlog = insert_application_log($lead_id, $lead_action, $lead_request_data, $lead_response_data, $created_by);
				}else{
					$customer_response_data = json_encode(array("response"=>"Error in lead insert."));
					$leadlog = insert_application_log($lead_id, $lead_action, $lead_request_data, $lead_response_data, $created_by);
				}
				
				if(!empty($proposal_details_id)){
					$proposallog = insert_application_log($lead_id, $proposal_action, $proposal_request_data, $proposal_response_data, $created_by);
				}else{
					$customer_response_data = json_encode(array("response"=>"Error in lead insert."));
					$leadlog = insert_application_log($lead_id, $proposal_action, $proposal_request_data, $proposal_response_data, $created_by);
				}

			//}



			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Lead created successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For customer proposal listing
	function customerProposalListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->customerProposalListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//For discrepancy proposal listing
	function discrepancyProposalListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->discrepancyProposalListing($_POST);
			//echo "<pre>ddd";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//For bo proposal listing
	function boProposalListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->boProposalListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//reject proposals
	function rejectProposal()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			if (empty($_POST['login_user_id'])) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Please provide user id.'), "Data" => NULL));
				exit;
			}

			if (empty($_POST['login_user_name'])) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Please provide user name.'), "Data" => NULL));
				exit;
			}


			$data = array();
			$data['status'] = 'Rejected';
			$data['updatedby'] = $_POST['login_user_id'];

			$pdata = array();
			$pdata['status'] = 'Rejected';
			$pdata['updated_by'] = $_POST['login_user_id'];
			$result = $this->apimodel->updateRecord('lead_details', $data, "lead_id='" . $_POST['id'] . "' ");
			if (!empty($result)) {
				$this->apimodel->updateRecord('proposal_details', $pdata, "lead_id='" . $_POST['id'] . "' ");
				//get login user details
				$remark = "Proposal Rejected by " . $_POST['login_user_name'];
				insert_proposal_log($_POST['id'], $_POST['login_user_id'], $remark);
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Proposal rejected successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//move to underwriting
	function moveToUW()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			if (empty($_POST['login_user_id'])) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Please provide user id.'), "Data" => NULL));
				exit;
			}

			if (empty($_POST['login_user_name'])) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Please provide user name.'), "Data" => NULL));
				exit;
			}


			$data = array();
			$data['status'] = 'UW-Approval-Awaiting';
			$data['updatedby'] = $_POST['login_user_id'];

			$pdata = array();
			$pdata['status'] = 'UW-Approval-Awaiting';
			$pdata['updated_by'] = $_POST['login_user_id'];

			$result = $this->apimodel->updateRecord('lead_details', $data, "lead_id='" . $_POST['id'] . "' ");
			if (!empty($result)) {
				$this->apimodel->updateRecord('proposal_details', $pdata, "lead_id='" . $_POST['id'] . "' ");
				//get login user details
				$remark = "Proposal moved to underwriting by " . $_POST['login_user_name'];
				insert_proposal_log($_POST['id'], $_POST['login_user_id'], $remark);
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Proposal moved to underwriting successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}


	//accept proposals
	function acceptProposal()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			if (empty($_POST['login_user_id'])) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Please provide user id.'), "Data" => NULL));
				exit;
			}

			if (empty($_POST['login_user_name'])) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Please provide user name.'), "Data" => NULL));
				exit;
			}

			//get lead details
			$lead_details = $this->apimodel->getdata("lead_details", "lead_id, creditor_id, plan_id, status", "lead_id='" . $_POST['id'] . "' ");

			//Updating payment table
			$paymentdata = array();
			$paymentdata['payment_status'] = 'Success';
			$paymentdata['updated_at'] = date("Y-m-d H:i:s");
			$paymentdata['updated_by'] = $_POST['login_user_id'];
			$result = $this->apimodel->updateRecord('proposal_payment_details', $paymentdata, "lead_id ='" . $_POST['id'] . "' ");

			//get trigger data
			$getTriggerData = $this->db->query("select l.reject_reason, l.lan_id, l.plan_id, l.lead_id, l.trace_id, c.first_name, c.last_name, c.full_name, c.mobile_no as customer_mobile, c.email_id as customer_email, e.employee_fname, e.employee_lname, e.employee_full_name, e.mobile_number as sm_mobile, e.email_id as sm_email, p.plan_name, cr.creaditor_name from lead_details as l, master_customer as c, master_employee as e, master_plan as p, master_ceditors as cr where l.lead_id='".$_POST['id']."' and l.primary_customer_id = c.customer_id and l.createdby = e.employee_id and l.plan_id = p.plan_id and p.creditor_id = cr.creditor_id")->row_array();

			//check already in UW or UW user aproving the lead.
			if($lead_details[0]['status'] == 'UW-Approval-Awaiting'){
				//Updating Lead entry
				$leaddata = array();
				$leaddata['status'] = 'Customer-Payment-Received'; //'Approved';
				$leaddata['updatedby'] = $_POST['login_user_id'];
				$this->apimodel->updateRecord('lead_details', $leaddata, "lead_id='" . $_POST['id'] . "' ");

				//Trigger for if UW accept.
				//customer trigger
				$cus_alert_ids = ['A1668'];
				$cus_data['lead_id'] = $_POST['id'];
				$cus_data['mobile_no'] = $getTriggerData['customer_mobile'];
				$cus_data['plan_id'] = $getTriggerData['plan_id'];
				$cus_data['email_id'] = $getTriggerData['customer_email'];

				$cus_data['alerts'][] = $getTriggerData['full_name'];
				$cus_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
				$cus_data['alerts'][] = $getTriggerData['trace_id'];
				$cus_data['alerts'][] = "4";
				$cus_data['alerts'][] = date("d-m-Y");

				//customer trigger
				$cus_alert_ids = ['A1674'];
				$cus_data['alerts'] = [];
				$cus_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
				$cus_data['alerts'][] = $getTriggerData['lan_id'];
				$cus_data['alerts'][] = $getTriggerData['reject_reason'];
				$cus_data['alerts'][] = $getTriggerData['trace_id'];
				$cus_data['alerts'][] = ''; //Proposal number goes here
				

				$customer_response = triggerCommunication($cus_alert_ids, $cus_data);

				insert_application_log($_POST['id'], 'uw_accept_reject_customer', json_encode($cus_data), json_encode($customer_response), $paymentdata['updated_by']);

				//sm trigger
				$sm_alert_ids = ['A1669'];
				$sm_data['lead_id'] = $_POST['id'];
				$sm_data['mobile_no'] = $getTriggerData['sm_mobile'];
				$sm_data['plan_id'] = $getTriggerData['plan_id'];
				$sm_data['email_id'] = $getTriggerData['sm_email'];

				$sm_data['alerts'][] = $getTriggerData['full_name'];
				$sm_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
				$sm_data['alerts'][] = $getTriggerData['employee_full_name'];
				$sm_data['alerts'][] = "4";
				$sm_data['alerts'][] = $getTriggerData['trace_id'];

				$sm_response = triggerCommunication($sm_alert_ids, $sm_data);

				insert_application_log($_POST['id'], 'uw_accept_reject_agent', json_encode($sm_data), json_encode($sm_response), $paymentdata['updated_by']);

				$response = json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Proposal accepted successfully.'), "Data" => 2));

				insert_application_log($_POST['id'], 'uw_proposal_accept', json_encode($_POST), $response, $paymentdata['updated_by']);

				echo $response;
				exit;
			}else{
				//check UW condition
				$uwCheck = checkUWCase($lead_details[0]['lead_id'], $lead_details[0]['creditor_id'], $lead_details[0]['plan_id']);
				if($uwCheck){
					//move lead to UW
					$leaddata = array();
					$leaddata['status'] = 'UW-Approval-Awaiting';
					$leaddata['updatedby'] = $_POST['login_user_id'];
					$this->apimodel->updateRecord('lead_details', $leaddata, "lead_id='" . $_POST['id'] . "' ");

					//Trigger move to UW
					//customer trigger
					$cus_alert_ids = ['A1666'];
					$cus_data['lead_id'] = $_POST['id'];
					$cus_data['mobile_no'] = $getTriggerData['customer_mobile'];
					$cus_data['plan_id'] = $getTriggerData['plan_id'];
					$cus_data['email_id'] = $getTriggerData['customer_email'];

					$cus_data['alerts'][] = $getTriggerData['full_name'];
					$cus_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
					$cus_data['alerts'][] = "4";
					$cus_data['alerts'][] = $getTriggerData['trace_id'];

					$customer_response = triggerCommunication($cus_alert_ids, $cus_data);

					insert_application_log($_POST['id'], 'uw_bucket_movement_acceptance_customer', json_encode($cus_data), json_encode($customer_response), $_POST['login_user_id']);

					//sm trigger
					$sm_alert_ids = ['A1667'];
					$sm_data['lead_id'] = $_POST['id'];
					$sm_data['mobile_no'] = $getTriggerData['sm_mobile'];
					$sm_data['plan_id'] = $getTriggerData['plan_id'];
					$sm_data['email_id'] = $getTriggerData['sm_email'];

					$sm_data['alerts'][] = $getTriggerData['full_name'];
					$sm_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
					$sm_data['alerts'][] = $getTriggerData['employee_full_name'];
					$sm_data['alerts'][] = $getTriggerData['trace_id'];
					$sm_data['alerts'][] = "4";

					$sm_response = triggerCommunication($sm_alert_ids, $sm_data);
					
					insert_application_log($_POST['id'], 'uw_bucket_movement_acceptance_agent', json_encode($sm_data), json_encode($sm_response), $_POST['login_user_id']);

					//uw_trigger

					$uw_user_data = $this->apimodel->getdata("master_employee", "employee_fname,employee_lname, employee_full_name, mobile_number, email_id", "role_id = 7");
					
					$uw_alert_ids = ['A1673'];
					$uw_data['lead_id'] = $_POST['id'];
					$uw_data['plan_id'] = $getTriggerData['plan_id'];
					$uw_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
					$uw_data['alerts'][] = $getTriggerData['creaditor_name'];
					$uw_data['alerts'][] = $getTriggerData['lan_id'];
					$uw_data['alerts'][] = date('d-m-Y');
					$uw_data['alerts'][] = ''; //remarks goes here

					foreach($uw_user_data as $uw_user){

						$uw_data['mobile_no'] = $uw_user['mobile_number'];
						$uw_data['email_id'] = $uw_user['email_id'];

						$uw_response = triggerCommunication($uw_alert_ids, $uw_data);

						insert_application_log($_POST['id'], 'uw_bucket_movement_acceptance_uw', json_encode($uw_data), json_encode($uw_response), $_POST['login_user_id']);
					}

					$response = json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Proposal moved to UW successfully.'), "Data" => 1));

					insert_application_log($_POST['id'], 'uw_proposal_accept', json_encode($_POST), $response, $paymentdata['updated_by']);

					echo $response;
					exit;

				}else{
					//Updating Lead entry
					$leaddata = array();
					$leaddata['status'] = 'Customer-Payment-Received'; //'Approved';
					$leaddata['updatedby'] = $_POST['login_user_id'];
					$this->apimodel->updateRecord('lead_details', $leaddata, "lead_id='" . $_POST['id'] . "' ");

					$response = json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Proposal accepted successfully.'), "Data" => 2));
					insert_application_log($_POST['id'], 'uw_proposal_accept', json_encode($_POST), $response, $paymentdata['updated_by']);

					echo $response;
					exit;
				}
			}
			
			
		} else {
			echo $checkToken;
		}
	}




	//For Full Quote
	/*function get_full_quote_data($policy_details, $lead_id, $emp_id, $master_policy_id, $proposal_policy_id, $proposal_details, $policy_sub_type_id, $sum_insured)
	{

		$count2 = 1;
		$maxcount2 = count($policy_details);
		foreach ($policy_details as $proposal) {
			$full_qoute = $this->apimodel->get_full_quote_data($lead_id, $emp_id, $master_policy_id, $proposal_policy_id, $nominees, $proposal_details, $policy_sub_type_id, $sum_insured);
			//echo "<pre>";print_r($full_qoute);exit;
		}

		if ($full_qoute['status'] == 'error') {
			echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => $full_qoute['msg']), "Data" => NULL));
			exit;
		} else {

			if ($maxcount2 == $count2) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => $full_qoute['msg']), "Data" => NULL));
				exit;
			}
			$count2++;
		}
	}
*/

	//dummy full code function
	/*function dummyFullQuote()
	{

		$member = array(
			0 => array(
				"MemberNo" => "1",
				"Salutation" => "Mr",
				"First_Name" => "Danish Akhtar",
				"Middle_Name" => null,
				"Last_Name" => "Shaikh",
				"Gender" => "M",
				"DateOfBirth" => "04/11/1986",
				"Relation_Code" => "R001",
				"Marital_Status" => null,
				"height" => "0.00",
				"weight" => "0",
				"occupation" => "O553",
				"PrimaryMember" => "Y",
				"MemberproductComponents" => array(
					0 => array(
						"PlanCode" => "4211",
						"MemberQuestionDetails" => array(
							0 => array(
								"QuestionCode" => null,
								"Answer" => null,
								"Remarks" => null
							)

						)

					)

				),
				"MemberPED" => array(
					"PEDCode" => null,
					"Remarks" => null,
				),
				"exactDiagnosis" => null,
				"dateOfDiagnosis" => null,
				"lastDateConsultation" => null,
				"detailsOfTreatmentGiven" => null,
				"doctorName" => null,
				"hospitalName" => null,
				"phoneNumberHosital" => null,
				"Nominee_First_Name" => "s",
				"Nominee_Last_Name" => "d",
				"Nominee_Contact_Number" => null,
				"Nominee_Home_Address" => null,
				"Nominee_Relationship_Code" => "R001"
			)


		);

		//echo "<pre>";print_r($member);exit;


		$fqrequest = ["ClientCreation" => ["Member_Customer_ID" => "1000", "salutation" => "Mr", "firstName" => "Danish", "middleName" => "", "lastName" => "Ak", "dateofBirth" => date('m/d/Y', strtotime("1986-04-11")), "gender" => "M", "educationalQualification" => null, "pinCode" => "425001", "uidNo" => null, "maritalStatus" => null, "nationality" => "Indian", "occupation" => "O553", "primaryEmailID" => "infodanish@gmail.com", "contactMobileNo" => "8149212749", "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => null, "remarks" => null, "startDate" => date('Y-m-d'), "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => "kalyan", "homeAddressLine2" => null, "homeAddressLine3" => null, "homePinCode" => "425001", "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null, "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null], "PolicyCreationRequest" => ["Quotation_Number" => "IPB100130770", "MasterPolicyNumber" => "61-20-00040-00-00", "GroupID" => "GRP001", "Product_Code" => "4211", "SumInsured_Type" => null, "Policy_Tanure" => "1", "Member_Type_Code" => "M209", "intermediaryCode" => "2108233", "AutoRenewal" => 'Y', "intermediaryBranchCode" => "10MHMUM01", "agentSignatureDate" => null, "Customer_Signature_Date" => null, "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => "1", "Source_Name" => "abc", "SPID" => "0", "TCN" => null, "CRTNO" => null, "RefCode1" => "0", "RefCode2" => "0", "Employee_Number" => "1000", "enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => 1, "PaymentMode" => "online", "PolicyproductComponents" => [["PlanCode" => "4211", "SumInsured" => "300000", "SchemeCode" => "4112000003"]]], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "Mumbai", "modeOfEntry" => "Direct", "cdAcNo" => null, "expiryDate" => null, "payerType" => "Customer", "payerCode" => null, "paymentBy" => "Customer", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => "457", "collectionRcvdDate" => "2020-10-20", "collectionMode" => "online", "remarks" => null, "instrumentNumber" => "pay_FrAaDQjzQFtQWG", "instrumentDate" => "2020-10-20", "bankName" => null, "branchName" => null, "bankLocation" => null, "micrNo" => null, "chequeType" => null, "ifscCode" => null, "PaymentGatewayName" => "ABC_GFB", "TerminalID" => "EuxJCz8cZV9V63", "CardNo" => null]];

		$req_json = json_encode($fqrequest);

		//echo $req_json;exit;


		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://bizpre.adityabirlahealth.com/ABHICL_NB/Service1.svc/GHI",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($fqrequest),
			CURLOPT_HTTPHEADER => array(
				"Cache-Control: no-cache",
				"Connection: keep-alive",
				"Content-Length: " . strlen(json_encode($fqrequest)),
				"Content-Type: application/json",
				"Host: bizpre.adityabirlahealth.com"
			),
		));

		$response = curl_exec($curl);


		$err = curl_error($curl);
		echo "<pre>";
		print_r($response);
		echo $err;
		exit;
	}
*/

	//Add Discrepancy
	function addDiscrepancy()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			//echo "<pre>";print_r($_POST);exit;
			$data = array();
			$data['created_by'] = $_POST['login_user_id'];
			$data['lead_id'] = $_POST['lead_id'];
			$data['discrepancy_type'] = $_POST['discrepancy_type'];
			$data['discrepancy_subtype'] = $_POST['discrepancy_subtype'];
			$data['remark'] = $_POST['remark'];

			$result = $this->apimodel->insertData('proposal_discrepancies', $data, 1);

			if (!empty($result)) {
				$remark = "Discrepancy Added with remark: " . $_POST['remark'];
				insert_proposal_log($result, $_POST['login_user_id'], $remark);

				$ldata = array();
				$ldata['status'] = 'Discrepancy';
				$ldata['updatedby'] = $_POST['login_user_id'];
				$this->apimodel->updateRecord('lead_details', $ldata, "lead_id='" . $_POST['lead_id'] . "' ");

				$udata = array();
				$udata['status'] = 'Discrepancy';
				$udata['updated_by'] = $_POST['login_user_id'];
				$this->apimodel->updateRecord('proposal_details', $udata, "lead_id='" . $_POST['lead_id'] . "' ");

				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Discrepancy added successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Reject Lead
	function rejectLead()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			//echo "<pre>";print_r($_POST);exit;
			$data = array();
			$data['updatedby'] = $_POST['login_user_id'];
			$ldata['status'] = 'Rejected';
			$data['lead_id'] = $_POST['lead_id'];
			$data['reject_reason'] = $_POST['reject_reason'];

			$result = $this->apimodel->updateRecord('lead_details', $data, "lead_id='" . $_POST['lead_id'] . "' ");

			if (!empty($result)) {

				$udata = array();
				$udata['status'] = 'Rejected';
				$udata['updated_by'] = $_POST['login_user_id'];
				$this->apimodel->updateRecord('proposal_details', $udata, "lead_id='" . $_POST['lead_id'] . "' ");

				//Trigger Rejected Proposals
				//get trigger data
				$getTriggerData = $this->db->query(" select l.lead_id, l.lan_id, l.trace_id, c.first_name, c.last_name, c.full_name, c.mobile_no as customer_mobile, c.email_id as customer_email, e.employee_fname, e.employee_lname, e.employee_full_name, e.mobile_number as sm_mobile, e.email_id as sm_email, p.plan_name from lead_details as l, master_customer as c, master_employee as e, master_plan as p where l.lead_id='".$_POST['lead_id']."' and l.primary_customer_id = c.customer_id and l.createdby = e.employee_id and l.plan_id = p.plan_id " )->row_array();

				//customer trigger
				$cus_alert_ids = ['A1670'];
				$cus_data['lead_id'] = $_POST['lead_id'];
				$cus_data['mobile_no'] = $getTriggerData['customer_mobile'];
				$cus_data['plan_id'] = $getTriggerData['plan_id'];
				$cus_data['email_id'] = $getTriggerData['customer_email'];

				$cus_data['alerts'][] = $getTriggerData['full_name'];
				$cus_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
				$cus_data['alerts'][] = $getTriggerData['trace_id'];
				$cus_data['alerts'][] = $getTriggerData['full_name'];
				$cus_data['alerts'][] = $_POST['reject_reason'];
				$cus_data['alerts'][] = '';
				$cus_data['alerts'][] = '';
				$cus_data['alerts'][] = '';
				$cus_data['alerts'][] = '';
				$cus_data['alerts'][] = '';
				$cus_data['alerts'][] = '';
				
				$customer_response = triggerCommunication($cus_alert_ids, $cus_data);
				insert_application_log($_POST['lead_id'], 'alert_uw_reject_customer', json_encode($cus_data), json_encode($customer_response), $_POST['login_user_id']);

				$cus_alert_ids = ['A1675'];
				$cus_data['alerts'] = [];

				$cus_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
				$cus_data['alerts'][] = $getTriggerData['lan_id'];
				$cus_data['alerts'][] = $_POST['reject_reason'];
				$cus_data['alerts'][] = $getTriggerData['trace_id'];

				$customer_response = triggerCommunication($cus_alert_ids, $cus_data);
				insert_application_log($_POST['lead_id'], 'alert_uw_reject_customer_2', json_encode($cus_data), json_encode($customer_response), $_POST['login_user_id']);


				//sm trigger
				$sm_alert_ids = ['A1671'];
				$sm_data['lead_id'] = $_POST['lead_id'];
				$sm_data['mobile_no'] = $getTriggerData['sm_mobile'];
				$sm_data['plan_id'] = $getTriggerData['plan_id'];
				$sm_data['email_id'] = $getTriggerData['sm_email'];

				$sm_data['alerts'][] = $getTriggerData['full_name'];
				$sm_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
				$sm_data['alerts'][] = $getTriggerData['trace_id'];
				$sm_data['alerts'][] = $getTriggerData['full_name'];
				$sm_data['alerts'][] = $_POST['reject_reason'];
				$sm_data['alerts'][] = '';
				$sm_data['alerts'][] = '';
				$sm_data['alerts'][] = '';
				$sm_data['alerts'][] = '';
				$sm_data['alerts'][] = $getTriggerData['employee_full_name'];

				$sm_response = triggerCommunication($sm_alert_ids, $sm_data);
				insert_application_log($_POST['lead_id'], 'alert_uw_reject_sm', json_encode($sm_data), json_encode($sm_response), json_encode($_POST['login_user_id']));

				$response = json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Lead rejected successfully.'), "Data" => $result));

				insert_application_log($_POST['lead_id'], 'uw_reject', json_encode($_POST), $response, $_POST['login_user_id']);

				echo $response;
				exit;
			} else {

				$response = json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				insert_application_log($_POST['lead_id'], 'uw_reject', json_encode($_POST), $response, $_POST['login_user_id']);

				echo $response;
				exit;
			}
		} else {

			insert_application_log($_POST['lead_id'], 'uw_reject', json_encode($_POST), $checkToken, $_POST['login_user_id']);

			echo $checkToken;
			exit;
		}
	}

	//For CO proposal listing
	function coProposalListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->coProposalListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get Co Listing in excel
	function getproposalpolicybylead()
	{
		//echo "<pre>";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getproposalpaymentdetailsbylead($_POST['leads']);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//For uw proposal listing
	function uwProposalListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->uwProposalListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//For assignment declaration listing
	function assignmentDeclarationListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->assignmentDeclarationListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Add Edit Assignment Declaration
	function addEditAssignmentDeclaration()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			//echo "<pre>";print_r($_POST);exit;
			$data = array();
			$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			$data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
			$data['label'] = (!empty($_POST['label'])) ? $_POST['label'] : '';
			$data['content'] = (!empty($_POST['content'])) ? $_POST['content'] : '';
			$data['is_active'] = (!empty($_POST['is_active'])) ? $_POST['is_active'] : '';
			$data['created_at'] = date("Y-m-d H:i:s");

			if (!empty($_POST['assignment_declaration_id'])) {
				$result = $this->apimodel->updateRecord('assignment_declaration', $data, "assignment_declaration_id='" . $_POST['assignment_declaration_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('assignment_declaration', $data, 1);
			}

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get get assignment declaration
	function getAssignmentDeclarationDetails()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getAssignmentDeclarationDetails($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Delete assignment declaration
	function delAssignmentDeclaration()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$data['is_active'] = 0;
			$result = $this->apimodel->updateRecord('assignment_declaration', $data, "assignment_declaration_id ='" . $_POST['id'] . "' ");
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For ghd declaration listing
	function ghdDeclarationListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->ghdDeclarationListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get get GHD declaration
	function getGHDDeclarationDetails()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getGHDDeclarationDetails($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit GHD Declaration
	function addEditGHDDeclaration()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			//echo "<pre>";print_r($_POST);exit;
			$data = array();
			$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			$data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
			$data['label'] = (!empty($_POST['label'])) ? $_POST['label'] : '';
			$data['content'] = (!empty($_POST['content'])) ? $_POST['content'] : '';
			$data['is_active'] = (!empty($_POST['is_active'])) ? $_POST['is_active'] : '';
			$data['created_at'] = date("Y-m-d H:i:s");

			if (!empty($_POST['declaration_id'])) {
				$result = $this->apimodel->updateRecord('ghd_declaration', $data, "declaration_id='" . $_POST['declaration_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('ghd_declaration', $data, 1);
			}

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Delete GHD declaration
	function delGHDDeclaration()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$data = array();
			$data['is_active'] = 0;
			$result = $this->apimodel->updateRecord('ghd_declaration', $data, "declaration_id ='" . $_POST['id'] . "' ");
			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//multi images uploader
	function getImages()
	{

		//echo "<pre>";print_r($_FILES);exit;

	}

	//Co Excel Upload

	function uploadcoexcel()
	{
		$data = array();
		$this->load->library('excel');

		//$path = $_POST['path'];
		//echo "path: ".$path;exit;

		$path = DOC_ROOT . 'assets' . DIRECTORY_SEPARATOR . 'coimportexcel';

		if (!file_exists($path)) {

			mkdir($path, 0777, true);
		}

		$path_info = pathinfo($_FILES['path']['name']);
		$ext = $path_info['extension'];
		$temp_name = $_FILES['path']['tmp_name'];
		$path_filename_ext = $path."/".$_FILES['path']['name'].".".$ext;

		if (file_exists($path_filename_ext)) {
			unlink($path_filename_ext);
		}

		move_uploaded_file($temp_name,$path_filename_ext);
		
		//move_uploaded_file($_FILES['path']['tmp_name'], $path);

		$object = PHPExcel_IOFactory::load($path_filename_ext);
		//Get only the Cell Collection
		$sheetData = $object->getActiveSheet()->toArray(null, false, false, true);
		//echo "data: ".$sheetData[2]['E'];
		//echo "<pre>eee";print_r($sheetData);exit;

		//Check Amount should not less than premium
		foreach ($object->getWorksheetIterator() as $worksheet) {
			$highestRow = $worksheet->getHighestRow();
			$highestColumn = $worksheet->getHighestColumn();
			//echo $highestRow;exit;
			for ($row = 2; $row <= $highestRow; $row++) {

				$trace_id_val = $worksheet->getCellByColumnAndRow(1, $row)->getValue();

				$premium_val = $worksheet->getCellByColumnAndRow(4, $row)->getValue();

				$ref_no_val = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
				$amount_val = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
				$payment_date_val = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
				$remark_val = $worksheet->getCellByColumnAndRow(11, $row)->getValue();

				//echo $trace_id_val;exit;
				//echo $payment_date_val;exit;


				if (empty($ref_no_val)) {
					echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Reference No. value is blank at row no. ' . $row), "Data" => NULL));
					exit;
				}

				if (empty($amount_val)) {
					echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Amount value is blank at row no. ' . $row), "Data" => NULL));
					exit;
				}

				if (empty($payment_date_val)) {
					echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Payment date value is blank at row no. ' . $row), "Data" => NULL));
					exit;
				}

				if (empty($remark_val)) {
					echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Remark value is blank at row no. ' . $row), "Data" => NULL));
					exit;
				}

				if ($amount_val < $premium_val) {
					echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Amount value should not less than premium at row no. ' . $row), "Data" => NULL));
					exit;
				}
			}
		}

		//exit;

		$lead_id = array();
		foreach ($object->getWorksheetIterator() as $worksheet) {
			$highestRow = $worksheet->getHighestRow();
			//echo $highestRow;exit;
			$highestColumn = $worksheet->getHighestColumn();
			for ($row = 2; $row <= $highestRow; $row++) {

				//$proposal_policy_id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
				$trace_id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
				//echo $trace_id;exit;
				$hb_receipt_number = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
				$reference_no = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
				$amount = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
				$payment_date = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
				//echo $payment_date;
				$payment_date = str_replace('/', '-', $payment_date);
				//$payment_date_val = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($payment_date));
				$payment_date_val = date("Y-m-d", strtotime($payment_date));
				//echo $payment_date_val;exit;
				//$status = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
				$remark = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
				//echo $payment_date;exit;
				if (!empty($trace_id)) {
					//array('transaction_date'=>$payment_date,'hb_receipt_number'=>$hb_receipt_number,'payment_remark'=>$remark,'transaction_number'=>$reference_no)
					$data = array();
					$data['transaction_date'] = $payment_date_val;
					$data['payment_date'] = $payment_date_val;
					//echo $data['transaction_date'];exit;
					$data['hb_receipt_no'] = $hb_receipt_number;
					$data['remark'] = $remark;
					$data['transaction_number'] = $reference_no;
					$data['payment_status'] = 'Success';
					$data['updated_at'] = date("Y-m-d H:i:s");
					$data['updated_by'] = $_POST['login_user_id'];

					$result = $this->apimodel->updateRecord('proposal_payment_details', $data, "trace_id ='" . $trace_id . "' ");

					//Get lead id from trace id.
					$getLeadID = $this->apimodel->getSortedData("lead_id, plan_id,creditor_id", "lead_details", "trace_id='" . $trace_id . "' ", "lead_id", "asc");
					//echo "<pre>";print_r($getLeadID);
					//echo $getLeadID[0]->lead_id;
					//exit;

					//get trigger data
					$getTriggerData = $this->db->query(" select l.lan_id, l.plan_id, l.lead_id, l.trace_id, c.first_name, c.last_name, c.full_name, c.mobile_no as customer_mobile, c.email_id as customer_email, e.employee_fname, e.employee_lname, e.employee_full_name, e.mobile_number as sm_mobile, e.email_id as sm_email, p.plan_name, cr.creaditor_name from lead_details as l, master_customer as c, master_employee as e, master_plan as p, master_ceditors as cr where l.lead_id='".$getLeadID[0]->lead_id."' and l.primary_customer_id = c.customer_id and l.createdby = e.employee_id and l.plan_id = p.plan_id and p.creditor_id = cr.creditor_id " )->row_array();

					//check UW condition
					$uwCheck = checkUWCase($getLeadID[0]->lead_id, $getLeadID[0]->creditor_id, $getLeadID[0]->plan_id);
					if($uwCheck){
						//move lead to UW
						$leaddata = array();
						$leaddata['status'] = 'UW-Approval-Awaiting';
						$leaddata['updatedby'] = $_POST['login_user_id'];
						$this->apimodel->updateRecord('lead_details', $leaddata, "trace_id='" . $trace_id . "' ");

						//Trigger move to UW
						//customer trigger
						$cus_alert_ids = ['A1666'];
						$cus_data['lead_id'] = $getLeadID[0]->lead_id;
						$cus_data['mobile_no'] = $getTriggerData['customer_mobile'];
						$cus_data['plan_id'] = $getTriggerData['plan_id'];
						$cus_data['email_id'] = $getTriggerData['customer_email'];

						$cus_data['alerts'][] = $getTriggerData['full_name'];
						$cus_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
						$cus_data['alerts'][] = "4";
						$cus_data['alerts'][] = $getTriggerData['trace_id'];

						$customer_response = triggerCommunication($cus_alert_ids, $cus_data);

						insert_application_log($cus_data['lead_id'], 'uw_bucket_movement_co_upload_customer', json_encode($cus_data), json_encode($customer_response), $_POST['login_user_id']);

						//sm trigger
						$sm_alert_ids = ['A1667'];
						$sm_data['lead_id'] = $getLeadID[0]->lead_id;
						$sm_data['mobile_no'] = $getTriggerData['sm_mobile'];
						$sm_data['plan_id'] = $getTriggerData['plan_id'];
						$sm_data['email_id'] = $getTriggerData['sm_email'];

						$sm_data['alerts'][] = $getTriggerData['full_name'];
						$sm_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
						$sm_data['alerts'][] = $getTriggerData['employee_full_name'];
						$sm_data['alerts'][] = $getTriggerData['trace_id'];
						$sm_data['alerts'][] = "4";
						

						$sm_response = triggerCommunication($sm_alert_ids, $sm_data);

						insert_application_log($sm_data['lead_id'], 'uw_bucket_movement_co_upload_agent', json_encode($sm_data), json_encode($sm_response), $_POST['login_user_id']);

						//uw_trigger

						$uw_user_data = $this->apimodel->getdata("master_employee", "employee_fname,employee_lname, employee_full_name, mobile_number, email_id", "role_id = 7");
						
						$uw_alert_ids = ['A1673'];
						$uw_data['lead_id'] = $getLeadID[0]->lead_id;
						$uw_data['plan_id'] = $getTriggerData['plan_id'];
						$uw_data['alerts'][] = $getTriggerData['plan_name'] ?? '';
						$uw_data['alerts'][] = $getTriggerData['creaditor_name'];
						$uw_data['alerts'][] = $getTriggerData['lan_id'];
						$uw_data['alerts'][] = date('d-m-Y');
						$uw_data['alerts'][] = ''; //remarks goes here

						foreach($uw_user_data as $uw_user){

							$uw_data['mobile_no'] = $uw_user['mobile_number'];
							$uw_data['email_id'] = $uw_user['email_id'];

							$uw_response = triggerCommunication($uw_alert_ids, $uw_data);

							insert_application_log($getLeadID[0]->lead_id, 'uw_bucket_movement_acceptance_uw', json_encode($uw_data), json_encode($uw_response), $_POST['login_user_id']);
						}

					}else{
						//Updating Lead entry
						$leaddata = array();
						$leaddata['status'] = 'Customer-Payment-Received'; //'Approved';
						$leaddata['updatedby'] = $_POST['login_user_id'];
						$this->apimodel->updateRecord('lead_details', $leaddata, "trace_id='" . $trace_id . "' ");
						
						//echo "<pre>";print_r($getLeadID);exit;
						$lead_id[] = $getLeadID[0]->lead_id;
					}
					
				}
			}

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Records updated successfully.'), "Data" => $lead_id));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		}
	}


	//Get Discrepancy Type
	function getDiscrepancyTypeData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSortedData("discrepancy_type_id,discrepancy_type", "discrepancy_type", "", "discrepancy_type", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get Discrepancy Subtype
	function getDiscrepancySubType()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSortedData("discrepancy_subtype_id,discrepancy_subtype", "discrepancy_subtype", "discrepancy_type_id='" . $_POST['discrepancy_type_id'] . "' ", "discrepancy_subtype", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For Discrepancy Type listing
	function discrepancyTypeListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->discrepancyTypeListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get discrepancy type form data
	function getDiscrepancyTypeFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getDiscrepancyTypeFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Check duplicate discrepancy type
	function checkDuplicateDiscrepancyType()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "discrepancy_type='" . $_POST['discrepancy_type'] . "' ";
			if (!empty($_POST['discrepancy_type_id'])) {
				$condition .= " && discrepancy_type_id !='" . $_POST['discrepancy_type_id'] . "' ";
			}
			$get_result = $this->apimodel->getdata("discrepancy_type", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit discrepancy type
	function addEditDiscrepancyType()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['discrepancy_type'] = (!empty($_POST['discrepancy_type'])) ? $_POST['discrepancy_type'] : '';
			if (empty($_POST['discrepancy_type_id'])) {
				$data['created_on'] = date("Y-m-d H:i:s");
				$data['created_by'] = $_POST['login_user_id'];
				//$data['updated_by'] = $_POST['login_user_id'];
			} else {
				//$data['updated_by'] = $_POST['login_user_id'];
			}

			if (!empty($_POST['discrepancy_type_id'])) {
				$result = $this->apimodel->updateRecord('discrepancy_type', $data, "discrepancy_type_id='" . $_POST['discrepancy_type_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('discrepancy_type', $data, 1);
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For Discrepancy SubType listing
	function discrepancySubTypeListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->discrepancySubTypeListing($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get discrepancy subtype form data
	function getDiscrepancySubTypeFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getDiscrepancySubTypeFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Check duplicate discrepancy subtype
	function checkDuplicateDiscrepancySubType()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "discrepancy_subtype='" . $_POST['discrepancy_subtype'] . "' ";
			if (!empty($_POST['discrepancy_subtype_id'])) {
				$condition .= " && discrepancy_subtype_id !='" . $_POST['discrepancy_subtype_id'] . "' ";
			}
			$get_result = $this->apimodel->getdata("discrepancy_subtype", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit discrepancy subtype
	function addEditDiscrepancySubType()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['discrepancy_type_id'] = (!empty($_POST['discrepancy_type_id'])) ? $_POST['discrepancy_type_id'] : '';
			$data['discrepancy_subtype'] = (!empty($_POST['discrepancy_subtype'])) ? $_POST['discrepancy_subtype'] : '';
			if (empty($_POST['discrepancy_subtype_id'])) {
				$data['created_on'] = date("Y-m-d H:i:s");
				$data['created_by'] = $_POST['login_user_id'];
				//$data['updated_by'] = $_POST['login_user_id'];
			} else {
				//$data['updated_by'] = $_POST['login_user_id'];
			}

			if (!empty($_POST['discrepancy_subtype_id'])) {
				$result = $this->apimodel->updateRecord('discrepancy_subtype', $data, "discrepancy_subtype_id='" . $_POST['discrepancy_subtype_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('discrepancy_subtype', $data, 1);
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For Sale Admin Dashboard
	function saleAdminDashBorad()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->saleAdminDashBorad($_POST);
			//echo "<pre>";print_r($get_result);
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//For Sale Admin Dashboard Export
	function exportSaleAdminDashBorad()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->exportSaleAdminDashBorad($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//For Sale Admin Dashboard Details
	function adminDashBoradDetails()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->adminDashBoradDetails($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//For Dashboard Details Export
	function exportDashBoradDetails(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->exportDashBoradDetails($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}
	}

	//For SM Dashboard
	function smDashBorad()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->smDashBorad($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}
	
	//For SM Dashboard Export
	function exportSMDashBorad(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->exportSMDashBorad($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}
	}

	//Get SM locations
	function getLogActions()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getLogActions();
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For Application Logs
	function applicationLogs()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->applicationLogs($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}
	
	//For Application Export
	function exportApplicationLogs(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->exportApplicationLogs($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
			exit;
		}else{
			echo $checkToken;
		}
	}

	//For Enrollment Form listing
	function enrollmentformListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getEnrollmentFormList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get enrollment form data
	function getEnrollmentFormsData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getEnrollmentFormsData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit enrollment form
	function addEditEnrollmentForm()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['form_title'] = (!empty($_POST['form_title'])) ? $_POST['form_title'] : '';
			$data['form_file'] = (!empty($_POST['form_file'])) ? $_POST['form_file'] : '';
			if (empty($_POST['enrollmentforms_id'])) {
				$data['created_on'] = date("Y-m-d H:i:s");
				$data['created_by'] = $_POST['login_user_id'];
				$data['updated_by'] = $_POST['login_user_id'];
			} else {
				$data['updated_by'] = $_POST['login_user_id'];
			}

			if (!empty($_POST['enrollmentforms_id'])) {
				$result = $this->apimodel->updateRecord('enrollmentforms', $data, "enrollmentforms_id='" . $_POST['enrollmentforms_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('enrollmentforms', $data, 1);
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//For company listing
	function companyListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getCompanyList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get company form data
	function getCompanyFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getCompanyFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Check duplicate company
	function checkDuplicateCompany()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "company_name='" . $_POST['company_name'] . "' ";
			if (!empty($_POST['company_id'])) {
				$condition .= " && company_id !='" . $_POST['company_id'] . "' ";
			}
			//echo $condition;
			//exit;
			$get_result = $this->apimodel->getdata("master_company", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit company
	function addEditEmployee()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['emp_id'] = (!empty($_POST['emp_id'])) ? $_POST['emp_id'] : '';
			$data['department_id'] = (!empty($_POST['department_id'])) ? $_POST['department_id'] : '';
			$data['employee_name'] = (!empty($_POST['employee_name'])) ? $_POST['employee_name'] : '';
			

			if (!empty($_POST['emp_id'])) {
				$result = $this->apimodel->updateRecord('tbl_employee', $data, "emp_id='" . $_POST['emp_id'] . "' ");
				if(isset($_POST['phone_no'])){
					$this->db->where('emp_id', $_POST['emp_id']); 
					$this->db->delete('tbl_employee_contacts'); 
					foreach ($_POST['phone_no'] as $key => $value) {
						if($value != ''){
							$d['emp_id'] = $_POST['emp_id'];
							$d['contact_no'] = $value;
							$result = $this->apimodel->insertData('tbl_employee_contacts', $d, 1);
						}
						
					}
				}
				if(isset($_POST['address'])){
					$this->db->where('emp_id', $_POST['emp_id']); 
					$this->db->delete('tbl_employee_address'); 
					foreach ($_POST['address'] as $key => $value) {
						if($value != ''){
							$d1['emp_id'] = $_POST['emp_id'];
							$d1['address'] = $value;
							$result = $this->apimodel->insertData('tbl_employee_address', $d1, 1);
							// echo $this->db->last_query();exit;
						}
						
					}
				}
			} else {
				$data['created_date'] = date("Y-m-d H:i:s");
				$result = $this->apimodel->insertData('tbl_employee', $data, 1);
				$emp_id = $this->db->insert_id();
				if(isset($_POST['phone_no'])){
					foreach ($_POST['phone_no'] as $key => $value) {
						if($value != ''){
							$d['emp_id'] = $emp_id;
							$d['contact_no'] = $value;
							$result = $this->apimodel->insertData('tbl_employee_contacts', $d, 1);
						}
						
					}
				}
				if(isset($_POST['address'])){
					// echo "hiii";
					foreach ($_POST['address'] as $key => $value) {
						if($value != ''){
							$d1['emp_id'] = $emp_id;
							$d1['address'] = $value;
							$result = $this->apimodel->insertData('tbl_employee_address', $d1, 1);
							// echo $this->db->last_query();exit;
						}
						
					}
				}
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get companies
	function getCompanyData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSortedData("company_id,company_name", "master_company", "", "company_name", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Import User
	function importUsers()
	{
		$data = array();
		$this->load->library('excel');

		//echo "<pre>";print_r($_FILES);exit;
		//Read file from path
		$objPHPExcel = PHPExcel_IOFactory::load($_FILES['import_file']['tmp_name']);

		//Get only the Cell Collection
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,false,false,true);
		//echo "<pre>";print_r($sheetData);exit;
		if(array_key_exists(2,$sheetData)){
			$errorArr = array();
			for($i = 2; $i < count($sheetData)+1; $i++){
				//echo $sheetData[$i]['A'];
				if(empty($sheetData[$i]['A'])){
					$errorArr[] = "First name value is blank at line no.A".$i."<br/>";
				}

				if(empty($sheetData[$i]['C'])){
					$errorArr[] = "Last name value is blank at line no.C".$i."<br/>";
				}

				if(empty($sheetData[$i]['D'])){
					$errorArr[] = "Employee code value is blank at line no.D".$i."<br/>";
				}

				if(empty($sheetData[$i]['E'])){
					$errorArr[] = "Email ID value is blank at line no.E".$i."<br/>";
				}

				if(empty($sheetData[$i]['F'])){
					$errorArr[] = "Mobile NO. value is blank at line no.F".$i."<br/>";
				}

				if(empty($sheetData[$i]['G'])){
					$errorArr[] = "Username value is blank at line no.G".$i."<br/>";
				}

				//Check duplicate user from email and mobile number
				$checkUserDup = $this->apimodel->getdata("master_employee", "employee_id"," (email_id='".$sheetData[$i]['E']."' || mobile_number='".$sheetData[$i]['F']."' ) ");

				if(!empty($checkUserDup)){
					$errorArr[] = "User Email ID OR Mobile No. already present at line no.".$i."<br/>";
				}

				//Check duplicate username
				$checkUsername = $this->apimodel->getdata("master_employee", "employee_id","user_name='".$sheetData[$i]['G']."' ");

				if(!empty($checkUsername)){
					$errorArr[] = "Duplicate Username ".$sheetData[$i]['G']. " at line no.G".$i."<br/>";
				}

				if(empty($sheetData[$i]['H'])){
					$errorArr[] = "Joining Date value is blank at line no.H".$i."<br/>";
				}

				if(empty($sheetData[$i]['I'])){
					$errorArr[] = "Role value is blank at line no.I".$i."<br/>";
				}

				//check role present or not if not then create and assign to user.
				$checkRole = $this->apimodel->getdata("roles", "role_id","role_name='".$sheetData[$i]['I']."' ");
				//$role_id = "";
				if(!empty($checkRole)){
					//echo $checkRole[0]['role_id'];
					$role_id = $checkRole[0]['role_id'];
				}else{
					//Create new role
					$role_data = array();
					$role_data['role_name'] = $sheetData[$i]['I'];
					$role_id = $this->apimodel->insertData('roles', $role_data, '1');
					$role_perm = array(1,6,22);
					
					for ($r = 0; $r < sizeof($role_perm); $r++) {
						//echo $role_perm[$r];
						$perm_data = array();
						$perm_data['role_id'] = $role_id;
						$perm_data['perm_id'] = $role_perm[$r];
						$rs = $this->apimodel->insertData('role_perm', $perm_data, '1');
						
					}
				}

				//check company present or not if not then create and assign to user.
				$checkCompany = $this->apimodel->getdata("master_company", "company_id","company_name='".$sheetData[$i]['J']."' ");
				if(!empty($checkCompany)){
					//echo $checkCompany[0]['company_id'];
					$company_id = $checkCompany[0]['company_id'];
				}else{
					//Create New Company
					$company_data = array();
					$company_data['company_name'] = $sheetData[$i]['J'];
					$company_id = $this->apimodel->insertData('master_company', $company_data, '1');
					
				}

				$data = array();
				$data['employee_fname'] = (!empty($sheetData[$i]['A'])) ? $sheetData[$i]['A'] : '';
				$data['employee_mname'] = (!empty($sheetData[$i]['B'])) ? $sheetData[$i]['B'] : '';
				$data['employee_lname'] = (!empty($sheetData[$i]['C'])) ? $sheetData[$i]['C'] : '';
				$data['employee_full_name'] = $data['employee_fname']." ".$data['employee_mname']." ".$data['employee_lname'];
				$data['employee_code'] = (!empty($sheetData[$i]['D'])) ? $sheetData[$i]['D'] : '';
				$data['date_of_joining'] = (!empty($sheetData[$i]['H'])) ? date("Y-m-d",strtotime($sheetData[$i]['H'])) : '';
				$data['email_id'] = (!empty($sheetData[$i]['E'])) ? $sheetData[$i]['E'] : '';
				$data['mobile_number'] = (!empty($sheetData[$i]['F'])) ? $sheetData[$i]['F'] : '';
				$data['user_name'] = (!empty($sheetData[$i]['G'])) ? $sheetData[$i]['G'] : '';
				$data['employee_password'] = md5(123456);
				$data['role_id'] = (!empty($role_id)) ? $role_id : '';
				$data['company_id'] = (!empty($company_id)) ? $company_id : '';
				$data['isactive'] = '1';
				$data['createdon'] = date("Y-m-d H:i:s");

				//Insert New User
				$user_id = $this->apimodel->insertData('master_employee', $data, '1');

				if(!empty($user_id)){
					//check location if SM user.
					if(!empty($sheetData[$i]['K'])){
						$locations = explode(",", $sheetData[$i]['K']);
						if(!empty($locations)){
							for ($loc = 0; $loc < sizeof($locations); $loc++) {
								//check already present or not
								$checkLocation = $this->apimodel->getdata("master_location", "location_id","location_name='".$locations[$loc]."' ");
								$location_id = "";
								if(!empty($checkLocation)){
									//echo $checkLocation[0]['location_id'];
									$location_id = $checkLocation[0]['location_id'];
								}else{
									//Create new location
									$location_data = array();
									$location_data['location_name'] = $locations[$loc];
									$location_id = $this->apimodel->insertData('master_location', $location_data, '1');
									
								}

								//insert user location
								$user_locations = array();
								$user_locations['user_id'] = $user_id;
								$user_locations['location_id'] = $location_id;
								$userlocation_id = $this->apimodel->insertData('user_locations', $user_locations, '1');
								
							}
						}
					}
				}

			}

			if(!empty($errorArr)){
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Error in input file. '), "Data" => $errorArr));
				exit;
			}else{
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Excel File Imported Successfully.. '), "Data" => NULL));
				exit;
			}

		}else{
			echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Empty file cannot be imported. '), "Data" => NULL));
			exit;
		}

		
		
	}

	//For payment work flow listing
	function paymentworkflowmasterListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getpaymentworkflowmasterList($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get payment workflow master form data
	function getPaymentWorkflowFormData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getPaymentWorkflowFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Check duplicate payment workflow
	function checkDuplicatePaymentWorkflow()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "workflow_name='" . $_POST['workflow_name'] . "' ";
			if (!empty($_POST['payment_workflow_master_id'])) {
				$condition .= " && payment_workflow_master_id !='" . $_POST['payment_workflow_master_id'] . "' ";
			}
			$get_result = $this->apimodel->getdata("payment_workflow_master", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Add Edit payment workflow
	function addEditPaymentWorkFlow()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['workflow_name'] = (!empty($_POST['workflow_name'])) ? $_POST['workflow_name'] : '';
			if (empty($_POST['perm_id'])) {
				$data['created_on'] = date("Y-m-d H:i:s");
				$data['created_by'] = $_POST['login_user_id'];
				$data['updated_by'] = $_POST['login_user_id'];
				$data['updated_on'] = date("Y-m-d H:i:s");
			} else {
				$data['updated_by'] = $_POST['login_user_id'];
				$data['updated_on'] = date("Y-m-d H:i:s");
			}

			if (!empty($_POST['payment_workflow_master_id'])) {
				$result = $this->apimodel->updateRecord('payment_workflow_master', $data, "payment_workflow_master_id='" . $_POST['payment_workflow_master_id'] . "' ");
			} else {
				$result = $this->apimodel->insertData('payment_workflow_master', $data, 1);
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//Get lead COI numbers
	function getCOINumbers()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSortedData("certificate_number", "api_proposal_response", "lead_id='" . $_POST['lead_id'] . "' ", "pr_api_id", "asc");
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	//get COI's
	function getProposalCOI()
	{
		//Danish: get COI with other details to display on thankyou page
		$coi_data = $this->db->query("SELECT ar.certificate_number,ar.lead_id, s.code, pm.policy_member_first_name, pm.policy_member_last_name,f.member_type, l.trace_id
		FROM api_proposal_response AS ar, master_policy_sub_type AS s, proposal_policy_member_details AS pm,family_construct AS f, lead_details AS l
		WHERE ar.lead_id = '" . $_POST['lead_id'] . "' AND ar.policy_sub_type_id=s.policy_sub_type_id 
		AND (ar.customer_id = pm.customer_id AND ar.lead_id=pm.lead_id) AND pm.relation_with_proposal=f.id AND ar.lead_id = l.lead_id" )->result();

		//echo "<pre>";print_r($get_result);exit;
		if (!empty($coi_data)) {
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $coi_data));
			exit;
		} else {
			echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
			exit;
		}
	}

	//For Sale Admin Dashboard Export
	function exportSMCreditors()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->exportSMCreditors($_POST);
			//echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	//Get customer table enum values
	function getCustomerEnumValues()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$result = array();
			$result['get_customer_salutation'] = $this->apimodel->getEnumValues('master_customer', 'salutation');

			$result['get_customer_gender'] = $this->apimodel->getEnumValues('master_customer', 'gender');

			//echo "<pre>";print_r($result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $result));
			exit;
		} else {
			echo $checkToken;
		}
	}


	function checkUWCaseTest(){
		var_dump(checkUWCase(138,11,406));
	}

	//Test Api
	function testApi()
	{
		$send_message = send_message("8149212749", $mail_to = "", $mail_cc = "", $mail_bcc = "", $data = array(), "sendOTP");

		echo "<pre>";
		print_r($send_message);
		exit;
	}

	function singlejourneyListing()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getsinglejourneyList($_POST);
			// echo "<pre>";print_r($get_result);exit;
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
			exit;
		} else {
			echo $checkToken;
		}
	}

	function getCreditorsDetails(){
		$res = $this->db->get_where("master_ceditors",["isactive" => 1])->result_array();
		echo json_encode($res);
	}

	function checkDuplicateSingleJourney()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$condition = "creditor_id='" . $_POST['creditor_id'] . "' ";
			if (!empty($_POST['id'])) {
				$condition .= " && id !='" . $_POST['id'] . "' ";
			}
			//echo $condition;
			//exit;
			$get_result = $this->apimodel->getdata("master_single_journey", "*", $condition);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	function addEditSingleJourney()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {

			$data = array();
			$data['id'] = (!empty($_POST['id'])) ? $_POST['id'] : '';
			$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			$data['created_by'] = (!empty($_POST['login_user_id'])) ? $_POST['login_user_id'] : '';
			$data['URL'] = (!empty($_POST['creditor_id'])) ? 'partner='.encrypt_decrypt_password($_POST['creditor_id']) : '';
			$data['is_active'] = (!empty($_POST['isactive'])) ? $_POST['isactive'] : '';
			
			// echo json_encode($data);exit;
			if (!empty($_POST['id'])) {
				$result = $this->apimodel->updateRecord('master_single_journey', $data, "id='" . $_POST['id'] . "' ");
			} else {
				// echo json_encode(1212);exit;
				$data['created_at'] = date("Y-m-d H:i:s");
				$result = $this->apimodel->insertData('master_single_journey', $data, 1);
				// echo json_encode($this->db->last_query());exit;
			}

			//echo "<pre>";print_r($result);exit;

			if (!empty($result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'), "Data" => $result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}

	function getSingleJourneyData()
	{
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getSingleJourneyData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}
	}


	function getDepartmentDetails(){
		$checkToken = $this->verify_request($_POST['utoken']);

		if (!empty($checkToken->username)) {
			$get_result = $this->apimodel->getDepartmentDetails();
			//echo "<pre>";print_r($get_result);exit;
			if (!empty($get_result)) {
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => $get_result));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
				exit;
			}
		} else {
			echo $checkToken;
		}

	}
}
