<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Home extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		checklogin();
		// $this->RolePermission = getRolePermissions();
		$this->load->model('homemodel','',TRUE);
	}
 
	function index()
	{                     			              
		$this->load->view('template/header.php');
		$this->load->view('home/index');
		$this->load->view('template/footer.php');	
	}
 
	function logout()
	{
		if(!empty($_SESSION['webpanel'])){
	        unset($_SESSION['webpanel']);
	    }
	    redirect("login");
	}
}

?>