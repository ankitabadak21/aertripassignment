<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Login extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->RolePermission = getRolePermissions();
		
		if(!empty($_SESSION["webpanel"]))
		{
			redirect('home/index', 'refresh');
		}
	}
 
	function index()
	{
		$this->load->view('login');
	}
 
	function loginvalidate()
	{
		//echo "<pre>here";print_r($_POST);//exit;
		$data = array();
		$data['username'] = (!empty($_POST['username'])) ? $_POST['username'] : '';
		$data['password'] = (!empty($_POST['password'])) ? $_POST['password'] : '';
		// echo "yo".SERVICE_URL;exit;
		$checkDetails = curlFunction(SERVICE_URL.'/api/userLogin', $data );
		// echo "<pre>";print_r($checkDetails);exit;
		$checkDetails = json_decode($checkDetails, true);
		
		//echo "<pre>";print_r($checkDetails['Data']);exit;
		if(isset($checkDetails['status_code']) && $checkDetails['status_code'] == '200')
		{
			$_SESSION["webpanel"] = $checkDetails['Data'];
			echo json_encode(array("success"=>true, "msg"=>'Authenticated Successfully'));
			exit;		
		}
		else
		{
			echo json_encode(array("success"=>false, "msg"=>'Username or Password incorrect.'));
			exit;
		}
	}

	/*function forgotpassword()
	{
		if(!empty($_POST['email_id']))
		{
			$result = $this->loginmodel->forgotpass($_POST['email_id']);	
			if($result)
			{
				// $result_content = $this->loginmodel->getContentForgetPass(1);
				$result_content = $this->loginmodel->getEmailContent("ADMIN_FORGOT_PASSWORD");
				
				$url = base_url()."forgetchangepassword?text=".rtrim(strtr(base64_encode("eid=".$_POST['email_id']), '+/', '-_'), '=')."/".rtrim(strtr(base64_encode("uid=".$result[0]->user_id ), '+/', '-_'), '=')."/".rtrim(strtr(base64_encode("dt=".date("Y-m-d")), '+/', '-_'), '=');
				// echo $url; exit;
				
				$message = str_replace(array('{username}','{url}','{link}'), array($result[0]->user_name,$url,base_url()), $result_content['content']);
				
				$this->email->from(FROM_EMAIL); // change it to yours
				$this->email->to($_POST['email_id']);// change it to yours
				$this->email->subject($result_content['subject']);
				$this->email->message($message);		
				$checkemail = $this->email->send();
				
				if($checkemail)
				{
					echo json_encode(array("success"=>true, "msg"=>'Mail sent successfully, Please check your mail.'));
					exit;
				}
				else
				{
					echo json_encode(array("success"=>false, "msg"=>'Problem while sending mail..'));
					exit;
				}			
			}
			else
			{		
				echo json_encode(array("success"=>false, "msg"=>'Invalid Email ID.'));
				exit;
			}
		}
		else
		{		
			echo json_encode(array("success"=>false, "msg"=>'Invalid Email ID.'));
			exit;
		}	
	}
	*/

}

?>