<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Employee extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		checklogin();
		//$this->RolePermission = getRolePermissions();
		ini_set('upload_max_filesize', '20M');  
		ini_set('post_max_size', '25M');  
	}

	function index()
	{
		$this->load->view('template/header.php');
		$this->load->view('employee/index');
		$this->load->view('template/footer.php');
	}

	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		//echo "<pre>GET ";print_r($_GET);exit;
		$creditorListing = curlFunction(SERVICE_URL.'/api/EmployeeListing',$_GET);
		$creditorListing = json_decode($creditorListing, true);
		//echo "<pre>";print_r($creditorListing);exit;
		if($creditorListing['status_code'] == '401'){
			//echo "in condition";
			redirect('login');
			exit();
		}
		
		
		//$get_result = $this->adcategorymodel->getRecords($_GET);

		$result = array();
		$result["sEcho"]= $_GET['sEcho'];

		$result["iTotalRecords"] = $creditorListing['Data']['totalRecords'];	//iTotalRecords get no of total recors
		$result["iTotalDisplayRecords"]= $creditorListing['Data']['totalRecords']; //iTotalDisplayRecords for display the no of records in data table.

		$items = array();
		
		if(!empty($creditorListing['Data']['query_result']) && count($creditorListing['Data']['query_result']) > 0)
		{
			for($i=0;$i<sizeof($creditorListing['Data']['query_result']);$i++)
			{
				$temp = array();
				array_push($temp, $creditorListing['Data']['query_result'][$i]['name'] );
				array_push($temp, $creditorListing['Data']['query_result'][$i]['employee_name'] );
				array_push($temp, $creditorListing['Data']['query_result'][$i]['created_date'] );
				/*if($creditorListing['Data']['query_result'][$i]['isactive'] == 1){
					array_push($temp, 'Active' );
				}else{
					array_push($temp, 'In-Active' );
				}*/
				
				$actionCol = "";
				
					$actionCol .='<a href="employee/addEdit?text='.rtrim(strtr(base64_encode("id=".$creditorListing['Data']['query_result'][$i]['emp_id'] ), '+/', '-_'), '=').'" title="Edit">Edit</a>';
				
					if($creditorListing['Data']['query_result'][$i]['isactive'] == 1){
						$actionCol .='&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\''.$creditorListing['Data']['query_result'][$i]['emp_id'] .'\');" title="Delete">Delete</a>';
					}
				
			
				array_push($temp, $actionCol);
				array_push($items, $temp);
			}
		}

		$result["aaData"] = $items;
		echo json_encode($result);
		exit;
	}
	
	function addEdit($id=NULL)
	{
		$record_id = "";
		//print_r($_GET);
		if(!empty($_GET['text']) && isset($_GET['text'])){
			$varr=base64_decode(strtr($_GET['text'], '-_', '+/'));	
			parse_str($varr,$url_prams);
			$record_id = $url_prams['id'];
		}
		
		$result = array();
		
		if(!empty($record_id)){
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['id'] = $record_id;
			$checkDetails = curlFunction(SERVICE_URL.'/api/getEmployeeFormData',$data);
			$checkDetails = json_decode($checkDetails, true);
			// echo "<pre>";print_r($checkDetails);exit;
			$result['getDetails'] = $checkDetails['Data'];
			
		}else{
			$result['getDetails'] = array();
		}
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];

		$data = json_decode(curlFunction(SERVICE_URL.'/api/getDepartmentDetails',$data),TRUE);
		$result['department_details'] = $data['Data'];
		// echo "<PRE>";print_r($result);exit;
		//echo $user_id;
		$this->load->view('template/header.php');
		$this->load->view('employee/addEdit',$result);
		$this->load->view('template/footer.php');
	}
	 
	function submitForm()
	{
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			
			//check duplicate record.
			$checkdata = array();
			$checkdata['department_id'] = $_POST['department_id'];
			$checkdata['employee_name'] = $_POST['employee_name'];
			$checkdata['utoken'] = $_SESSION['webpanel']['utoken'];
			if(isset($_POST['emp_id']) && $_POST['emp_id'] > 0){
				$checkdata['emp_id'] = $_POST['emp_id'];
			}
			
			$checkDetails = curlFunction(SERVICE_URL.'/api/checkDuplicateEmployee',$checkdata);
			//echo "<pre>";print_r($checkDetails);exit;
			$checkDetails = json_decode($checkDetails, true);
			
			if($checkDetails['status_code'] == '200')
			{
				echo json_encode(array("success"=>false, 'msg'=>'Record Already Present!'));
				exit;
			}
			
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['emp_id'] = (!empty($_POST['emp_id'])) ? $_POST['emp_id'] : '';
			$data['department_id'] = (!empty($_POST['department_id'])) ? $_POST['department_id'] : '';
			$data['employee_name'] = (!empty($_POST['employee_name'])) ? $_POST['employee_name'] : '';
			$data['phone_no'] = (!empty($_POST['phone_no'])) ? $_POST['phone_no'] : '';
			$data['address'] = (!empty($_POST['address'])) ? $_POST['address'] : '';
			
			$addEdit = curlFunction(SERVICE_URL.'/api/addEditEmployee',$data);
			// echo "<pre>";print_r($addEdit);exit;
			$addEdit = json_decode($addEdit, true);
			
			if($addEdit['status_code'] == '200'){
				echo json_encode(array('success'=>true, 'msg'=>$addEdit['Metadata']['Message']));
				exit;
			}else{
				echo json_encode(array('success'=>false, 'msg'=>$addEdit['Metadata']['Message']));
				exit;
			}
			
		}
		else
		{
			echo json_encode(array('success'=>false, 'msg'=>'Problem While Add/Edit Data.'));
			exit;
		}
	}
		
	function delRecord($id)
	{
		//$appdResult = $this->adcategorymodel->delrecord("tbl_categories","category_id ",$id);
		$data = array();
		$data['id'] = $id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$delRecord = curlFunction(SERVICE_URL.'/api/delEmployee',$data);
		//echo "<pre>";print_r($checkDetails);exit;
		$delRecord = json_decode($delRecord, true);
		 
		if($delRecord['status_code'] == '200'){
			echo "1";
		}else{
			echo "2";
		}	
	}
}

?>
