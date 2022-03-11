<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Login extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
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
		
		$data = array();
		$data['username'] = (!empty($_POST['username'])) ? $_POST['username'] : '';
		$data['password'] = (!empty($_POST['password'])) ? $_POST['password'] : '';
	
		$checkDetails = curlFunction(SERVICE_URL.'/api/userLogin', $data );
		
		$checkDetails = json_decode($checkDetails, true);
		
	
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

	

}

?>