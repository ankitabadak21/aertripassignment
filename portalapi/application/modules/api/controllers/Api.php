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
			//$result = $this->apimodel->updateRecord('tbl_employee', $data, "emp_id='" . $_POST['id'] . "' ");
			$result = $this->apimodel->delrecord('tbl_employee',"emp_id",$_POST['id']);
			$result = $this->apimodel->delrecord('tbl_employee_contacts',"emp_id",$_POST['id']);
			$result = $this->apimodel->delrecord('tbl_employee_address',"emp_id",$_POST['id']);
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

	



	//Co Excel Upload




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
