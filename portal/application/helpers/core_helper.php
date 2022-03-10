<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function getallheaders_values()
{
	//if (!function_exists('apache_request_headers')) { 
	//function apache_request_headers() { 
	if (!empty($_SERVER)) {
		foreach ($_SERVER as $key => $value) {
			if (substr($key, 0, 5) == "HTTP_") {
				$key = str_replace(" ", "-", strtolower(str_replace("_", " ", substr($key, 5))));
				$out[$key] = $value;
			} else {
				$out[$key] = $value;
			}
		}

		return $out;
	}

	//} 
	//}

}


function curlFunction($url, $post_fiels = null)
{
	// echo "Url: ".$url."<br/>";
	// echo "Curl data: <pre>";print_r($post_fiels);
	$str = http_build_query($post_fiels);
	//$str = $post_fiels;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	//curl_setopt($ch, CURLOPT_UPLOAD, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
	//curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));

	// execute!
	$errors = curl_error($ch);
	$response = curl_exec($ch);

	// close the connection, release resources used
	curl_close($ch);

	// var_dump($errors);
	// var_dump($response);
	// exit;

	return $response;
}

function curlFileFunction($url, $post_fiels = null)
{
	//echo "Url: ".$url."<br/>";
	//echo "Curl data: <pre>";print_r($post_fiels);
	//$str = http_build_query($post_fiels);
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	//curl_setopt($ch, CURLOPT_UPLOAD, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fiels);
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
	//curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));

	// execute!
	$errors = curl_error($ch);
	$response = curl_exec($ch);

	// close the connection, release resources used
	curl_close($ch);

	//var_dump($errors);
	//var_dump($response);
	//exit;

	return $response;
}

function getRolePermissions()
{
	if (!empty($_SESSION["webpanel"]['role_id'])) {
		$access_data = array();
		$access_data['role_id'] = $_SESSION["webpanel"]['role_id'];
		$access_data['utoken'] = $_SESSION['webpanel']['utoken'];

		$str = http_build_query($access_data);
		$ch = curl_init(SERVICE_URL . '/api/getLoginUserAccess');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
		//curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));

		// execute!
		$errors = curl_error($ch);
		$response = curl_exec($ch);

		// close the connection, release resources used
		curl_close($ch);

		//var_dump($errors);
		//var_dump($response);
		//exit;
		$response = json_decode($response, true);
		$permissions = array_column($response['Data'], 'perm_desc');
		return $permissions;
	}
}

function genderDropdown($selected = null)
{
	$option_array = array('Male' => 'Male', 'Female' => 'Female');
	return ArrayToHTMLOptions($option_array, $selected);
}

function nameTitleDropdown($selected = null)
{
	$option_array = array('Mr' => 'Mr', 'Mrs' => 'Mrs', 'Miss' => 'Miss', 'Dr' => 'Dr', 'Ms' => 'Ms');
	return ArrayToHTMLOptions($option_array, $selected);
}

function StrLeft($s1, $s2)
{
	return substr($s1, 0, strpos($s1, $s2));
}

/*function SelfURL(){
	$s = (empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on")) ? "s" : "";
	$protocol = Core::StrLeft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
	$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
	return $protocol."://".$_SERVER['SERVER_NAME'].$port;
}
*/

function GenRandomStr($length)
{
	$characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$string = '';
	for ($p = 0; $p < $length; $p++) {
		$string .= $characters[mt_rand(0, strlen($characters) - 1)];
	}
	return $string;
}


function seoUrl($string)
{
	//Lower case everything
	$string = strtolower($string);
	//Make alphanumeric (removes all other characters)
	$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
	//Clean up multiple dashes or whitespaces
	$string = preg_replace("/[\s-]+/", " ", $string);
	//Convert whitespaces and underscore to dash
	$string = preg_replace("/[\s_]/", "-", $string);

	return $string;
}


function ArrayToHTMLOptions($option_array, $selected = null)
{
	$options = "";
	foreach ($option_array as $key => $val) {

		$options .= (!is_null($selected) && $key == $selected) ? '<option value="' . $key . '" selected="selected">' . $val . '</option>' : '<option value="' . $key . '">' . $val . '</option>';
	}
	return $options;
}

function PrintArray($data = array())
{
	echo "<pre>";
	print_r($data);
	echo "</pre>";
}

function PrependNullOption($option_list)
{
	return "<option value=''>----------Select-------</option>" . $option_list;
}

function DisplayMessage($msg, $msg_type = 0, $autohide = 1)
{
	$html = $class = $title = '';
	switch ($msg_type) {
		case 1;
			$title = "Success Message";
			$html = "<script type='text/javascript'>$(function(){ $.pnotify({type: 'success',title: '" . $title . "',text: '" . $msg . "',icon: 'picon icon16 iconic-icon-check-alt white',opacity: 0.95,hide:false,history: false,sticker: false});});</script>";
			break;
		case 2;
			$title = "Notice Message";
			$html = "<script type='text/javascript'>$(function(){ $.pnotify({type: 'info',title: '" . $title . "',text: '" . $msg . "',icon: 'picon icon16 brocco-icon-info white',opacity: 0.95,hide:false,history: false,sticker: false});});</script>";
			break;
		case 0;
		default:
			$title = "Error Message";
			$html = "<script type='text/javascript'>$(function(){ $.pnotify({type: 'error',title: '" . $title . "',text: '" . $msg . "',icon: 'picon icon24 typ-icon-cancel white',opacity: 0.95,hide:false,history: false,sticker: false});});</script>";
			break;
	}
	return $html;
}

function FilterNullValues($array = array(), $filter_zero = false)
{
	return ($filter_zero === true) ? array_filter($array) : array_filter($array, 'strlen');
}

function uploadFile($fieldname, $maxsize, $uploadpath, $extensions = false, $ref_name = false)
{
	$upload_field_name = $_FILES[$fieldname]['name'];
	if (empty($upload_field_name) || $upload_field_name == 'NULL') {
		return array('status' => 'error', 'msg' => 'Please upload the file ');
	}
	$value = explode(".", $upload_field_name);
	$file_extension = strtolower(end($value));
	//		$file_extension = strtolower(pathinfo($upload_field_name, PATHINFO_EXTENSION));
	if ($extensions !== false && is_array($extensions)) {
		if (!in_array($file_extension, $extensions)) {
			return array('status' => 'error', 'msg' => 'Please upload the valid file');
		}
	}

	$file_size = @filesize($_FILES[$fieldname]["tmp_name"]);

	if ($file_size > $maxsize) {
		return array('status' => 'error', 'msg' => 'File Exceeds maximum limit');
	}

	if (isset($upload_field_name)) {
		if ($_FILES[$fieldname]["error"] > 0) {
			return array('status' => 'error', 'msg' => 'Error: ' . $_FILES[$fieldname]['error']);
		}
	}

	if ($ref_name == false) {
		$file_name = time() . str_replace(" ", "_", $upload_field_name);
	} else {
		$file_name = str_replace(" ", "_", $ref_name) . "." . $file_extension;
	}
	if (!is_dir($uploadpath)) {
		mkdir($uploadpath, 0777);
	}
	if (move_uploaded_file($_FILES[$fieldname]["tmp_name"], $uploadpath . $file_name)) {
		return array('status' => 'true', 'msg' => $file_name);
	} else {
		return array('status' => 'error', 'msg' => 'Sorry unable to upload your file, Please try after some time.');
	}
}

/*function UploadSingleFile($fieldname, $maxsize, $uploadpath, $extensions=false, $ref_name=false) {
	$upload_field_name = $_FILES[$fieldname]['name'];
	if(empty($upload_field_name) || $upload_field_name == 'NULL' ) {			
		return array('file'=>$_FILES[$fieldname]["name"], 'status'=>false, 'msg'=>'Please upload a file');
	}
	//$file_extension = strtolower(end(explode(".",$upload_field_name)));
	$file_extension = strtolower(pathinfo($upload_field_name, PATHINFO_EXTENSION));
	
	if($extensions !== false && is_array($extensions) ) {
		if(!in_array($file_extension,$extensions) ) {
			return array('file'=>$_FILES[$fieldname]["name"], 'status'=>false, 'msg'=>'Please upload valid file');
		}			
	}
	$file_size = @filesize($_FILES[$fieldname]["tmp_name"]);
	if ($file_size > $maxsize) {
		return array('file'=>$_FILES[$fieldname]["name"], 'status'=>false, 'msg'=>'File Exceeds maximum limit');
	}
	if(isset($upload_field_name)) {
		if ($_FILES[$fieldname]["error"] > 0) {
			return array('file'=>$_FILES[$fieldname]["name"], 'status'=>false, 'msg'=>'Error: '.$_FILES[$fieldname]['error']);
		}
	}
	if($ref_name == false ) {
		//$file_name = time().'_'.str_replace(" ","_",$upload_field_name);
		
		$file_name_without_ext =  $this->FileNameWithoutExt($upload_field_name);
		$file_name = time().'_'.Core::RenameUploadFile($file_name_without_ext).".".$file_extension;
	} else {
		$file_name = str_replace(" ", "_",$ref_name).".".$file_extension;
	}
	if(!is_dir($uploadpath))
	{
		mkdir($uploadpath,0777);
	}
	if(move_uploaded_file($_FILES[$fieldname]["tmp_name"], $uploadpath.$file_name)) {			
		return array('file'=>$_FILES[$fieldname]["name"], 'status'=>true, 'msg'=>'File Uploaded Successfully!', 'filename'=>$file_name);
	} else {
		return array('file'=>$_FILES[$fieldname]["name"], 'status'=>false, 'msg'=>'Sorry unable to upload your file, Please try after some time.');			
	}
}
*/
/*function UploadMultipleFile($fieldname, $maxsize, $uploadpath, $index, $extensions=false, $ref_name=false) 
{
	$upload_field_name = $_FILES[$fieldname]['name'][$index];
	if(empty($upload_field_name) || $upload_field_name == 'NULL' ) {			
		return array('file'=>$_FILES[$fieldname]["name"][$index], 'status'=>false, 'msg'=>'Please upload a file');
	}
	
	//$file_extension = strtolower(end(explode(".",$upload_field_name)));
	$file_extension = strtolower(pathinfo($upload_field_name, PATHINFO_EXTENSION));
	
	if($extensions !== false && is_array($extensions) ) {
		if(!in_array($file_extension,$extensions) ) {
			return array('file'=>$_FILES[$fieldname]["name"][$index], 'status'=>false, 'msg'=>'Please upload valid file');
		}			
	}
	$file_size = @filesize($_FILES[$fieldname]["tmp_name"][$index]);
	if ($file_size > $maxsize) {
		return array('file'=>$_FILES[$fieldname]["name"][$index],'status'=>false, 'msg'=>'File Exceeds maximum limit');
	}
	if(isset($upload_field_name)) {
		if ($_FILES[$fieldname]["error"][$index] > 0) {
			return array('file'=>$_FILES[$fieldname]["name"][$index],'status'=>false, 'msg'=>'Error: '.$_FILES[$fieldname]['error']);
		}
	}
	$file_name = "";
	if($ref_name == false ) {
		$file_name_without_ext =  $this->FileNameWithoutExt($upload_field_name);
		$file_name = time().'_'.Core::RenameUploadFile($file_name_without_ext).".".$file_extension;
	} else {
		$file_name = Core::RenameUploadFile($ref_name).".".$file_extension;
	}
	if(!is_dir($uploadpath))
	{
		mkdir($uploadpath,0777);
	}
	if(move_uploaded_file($_FILES[$fieldname]["tmp_name"][$index], $uploadpath.$file_name)) {
		return array('file'=>$_FILES[$fieldname]["name"][$index], 'status'=>true, 'msg'=>'File Uploaded Successfully!', 'filename'=>$file_name);
	} else {
		return array('file'=>$_FILES[$fieldname]["name"][$index], 'status'=>false, 'msg'=>'Sorry unable to upload your file, Please try after some time.');			
	}
}
*/
/**
 * @author : Danish Akhtar
 * @desc: This function filters the uploaded file name and properly rename it 
 * @param: $data : data string
 * changes : Other 4 characters are added 
 */
function RenameUploadFile($data)
{
	$search = array("'", " ", "(", ")", ".", "&", "-", "\"", "\\", "?", ":", "/");
	$replace = array("", "_", "", "", "", "", "", "", "", "", "", "");
	$new_data = str_replace($search, $replace, $data);
	return strtolower($new_data);
}

function FileNameWithoutExt($filename)
{
	return substr($filename, 0, (strlen($filename)) - (strlen(strrchr($filename, '.'))));
}

function PadString($number, $total_length, $prefix_text = '', $postfix_text = '', $padding_char = "0", $pad_side = 'left')
{
	$string = '';
	switch ($pad_side) {
		case 'right':
			$string = str_pad($number, $total_length, $padding_char, STR_PAD_RIGHT);
			break;
		default:
		case 'left':
			$string = str_pad($number, $total_length, $padding_char, STR_PAD_LEFT);
			break;
	}
	return $prefix_text . $string . $postfix_text;
}

function PageRedirect($page)
{
	print "<script type='text/javascript'>";
	print "window.location = '$page'";
	print "</script>";
	@header("Location : $page");
	exit;
}

function RedirectTo($page)
{
	if (!headers_sent()) {
		header("Location: " . $page);
		exit;
	} else {
		echo '<script type="text/javascript">';
		echo 'window.location.href="' . $page . '";';
		echo '</script>';
		echo '<noscript>';
		echo '<meta http-equiv="refresh" content="0;url=' . $page . '" />';
		echo '</noscript>';
		exit;
	}
}

function array_diff_multidimensional($session, $post)
{
	$result = array();
	foreach ($session as $sKey => $sValue) {
		foreach ($post as $pKey => $pValue) {
			if ((string) $sKey == (string) $pKey) {
				$result[$sKey] = array_diff($sValue, $pValue);
			}
		}
	}
	return $result;
}

function array_search2d($needle, $haystack)
{
	for ($i = 0, $l = count($haystack); $i < $l; ++$i) {
		if (in_array($needle, $haystack[$i])) return $i;
	}
	return false;
}

function YMDToDMY($ymd, $show_his = false)
{
	return ($show_his) ? date('d-m-Y h:i:s A', strtotime($ymd)) : date('d-m-Y', strtotime($ymd));
}

function DMYToYMD($dmy, $show_his = false)
{
	return date('Y-m-d', strtotime($dmy));
}

function aasort(&$array, $key)
{
	$sorter = array();
	$ret = array();
	reset($array);
	foreach ($array as $ii => $va) {
		if (!empty($va[$key])) {
			$sorter[$ii] = $va[$key];
		} else {
			$sorter[$ii] = "";
		}
	}
	asort($sorter);
	foreach ($sorter as $ii => $va) {
		if (!empty($array[$ii])) {
			$ret[$ii] = $array[$ii];
		} else {
			$ret[$ii] = "";
		}
	}
	$array = $ret;
}

function getExcelColumns($obj, $collength)
{
	$colNumber = PHPExcel_Cell::columnIndexFromString($obj->getActiveSheet()->getHighestDataColumn());
	if ($collength != $colNumber) {
		return false;
	} else {
		return true;
	}
}
function getExcelRows($obj, $minrows)
{
	$rows = $obj->getActiveSheet()->getHighestRow();
	if ($rows < $minrows) {
		return false;
	} else {
		return true;
	}
}

/*function CreateWhereForSingleTable($search){
	
	$new_array_without_nulls = Core::FilterNullValues($search);
//	echo "<pre>";
//	print_r($new_array_without_nulls);
//	echo "</pre>";
	$condition = "";
	foreach ($new_array_without_nulls as $key => $val){
		$match_cond = (is_numeric($val)) ? "$key=$val" : ((strtotime($val)) ? "$key='$val'" : "$key like '%$val%'");
		$condition .= ($condition=='') ? " $match_cond" : " && $match_cond";	
	}
	return $condition;
}
*/

function DaysDiffFromToday($date)
{
	$now = time(); // or your date as well
	$your_date = strtotime($date);
	$datediff = $now - $your_date;
	return floor($datediff / (60 * 60 * 24));
}

function DaysDiffBetweenTwoDays($startdate, $enddate)
{
	$now = strtotime($startdate); // or your date as well
	$your_date = strtotime($enddate);
	$datediff = $now - $your_date;
	return floor($datediff / (60 * 60 * 24));
}

/* creates a compressed zip file */
function CreateZip($files = array(), $destination = '', $overwrite = false)
{
	//if the zip file already exists and overwrite is false, return false
	if (file_exists($destination) && !$overwrite) {
		return false;
	}
	//vars
	$valid_files = array();
	//if files were passed in...
	if (is_array($files)) {
		//cycle through each file
		foreach ($files as $file) {
			//make sure the file exists
			if (file_exists($file)) {
				$valid_files[] = $file;
			}
		}
	}
	//if we have good files...
	if (count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		//add the files
		foreach ($valid_files as $file) {
			$new_filename = substr($file, strrpos($file, '/') + 1);
			$zip->addFile($file, $new_filename);
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;

		//close the zip -- done!
		$zip->close();

		//check to make sure the file exists
		return file_exists($destination);
	} else {
		return false;
	}
}


function checklogin()
{
	if (empty($_SESSION["webpanel"])) {
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			echo json_encode(array('success' => false, 'msg' => 'redirect'));
			exit();
		} else {
			redirect('login', 'refresh');
			exit();
		}
	}
}



function sendNotification($title = '', $notification = '', $email_content =  '', $fcm_token = '', $subject = '', $from_email = '', $to_email = '', $cc_email = '', $notification_type = array())
{
	$CI = &get_instance();
	if (!empty($fcm_token) && !empty($notification)) {
		// Send mobile notification
		// API access key from Google API's Console
		//$api_access_key =  'AIzaSyCBRMRXzMuuNLYmI2TxPF-ttOBWjlKfhVU' ;
		$api_access_key =  'AAAAvbZGaSY:APA91bExUJ2U-juY7olU808CVL7rQiTsv5LPJtRQhn46uL-_H6TWqsp3Nd_KYF02jINdu4sgvpsdGmjp5NABjIY5VxbxQzb4uXb4SNCtXvL5WMl6TdVR3WcdQdGPga7CAB9yYqw6jSLe';

		$data = array("from_notification" => true);
		$url = 'https://fcm.googleapis.com/fcm/send';
		$fields = array(
			'to' => $fcm_token,
			'notification' => array(
				"body" => $notification,
				"title" => $title,
				"icon" => "myicon",
				"sound" => "default"
			),
			'data' => $data
		);

		$fields = json_encode($fields);
		$headers = array('Authorization: key=' . $api_access_key, 'Content-Type: application/json');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

		$result = curl_exec($ch);
		curl_close($ch);
	}

	if (!empty($email_content) && !empty($from_email) && !empty($to_email) && !empty($subject)) {
		$CI->email->from($from_email); // change it to yours
		$CI->email->to($to_email); // change it to yours
		if (!empty($cc_email)) {
			$CI->email->cc($cc_email);
		}

		$CI->email->subject($subject);
		$CI->email->message($email_content);

		if ($CI->email->send()) {
		} else {
			show_error($CI->email->print_debugger());
		}
	}
}

/*
function sendNotification($title='',$notification = '',$email_content=  '',$fcm_token = '',$subject='',$from_email='',$to_email='',$cc_email = '',$notification_type = array()){
		
	$CI =& get_instance();
	if(!empty($fcm_token) && !empty($notification)){
		//echo $fcm_token;exit;
		$api_access_key =  'AAAAvbZGaSY:APA91bExUJ2U-juY7olU808CVL7rQiTsv5LPJtRQhn46uL-_H6TWqsp3Nd_KYF02jINdu4sgvpsdGmjp5NABjIY5VxbxQzb4uXb4SNCtXvL5WMl6TdVR3WcdQdGPga7CAB9yYqw6jSLe' ;
			
		$msg = array(
			'body' 	=> $notification,
			'title'	=> $title,
			'icon'	=> 'myicon',
			'sound' => 'default'
		);
			
		$fields = array(
				'to'		=> $fcm_token,
				'notification'	=> $msg,
				'data' => $notification_type
		);
				
			
		$headers = array('Authorization: key=' . $api_access_key,'Content-Type: application/json');
				
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		curl_close( $ch );
		#Echo Result Of FireBase Server
		//echo $result;	
	}
		
	if(!empty($email_content)&& !empty($from_email)&& !empty($to_email) && !empty($subject)){
						
		$CI->email->from($from_email); // change it to yours
		$CI->email->to($to_email); // change it to yours
		if(!empty($cc_email)){
			$CI->email->cc($cc_email);
		}
			
		$CI->email->subject($subject);
		$CI->email->message($email_content);
			
		if($CI->email->send()){
		} else {
			show_error($CI->email->print_debugger());                
		}
	}
		
		
    
}*/

function send_sms2($feed, $mobile, $message, $time, $jobname = null)
{

	$url = "http://bulkpush.mytoday.com/BulkSms/SingleMsgApi?async=1&username=9920097917&password=panav2015&feedid=$feed&To=$mobile&Text=" . urlencode($message) . "&time=$time";

	if (!empty($jobname)) $url .= "&jobname=$jobname";

	$bulkpush_response = call_url($url);

	$response = ((array)simplexml_load_string($bulkpush_response));
	return $response['@attributes']['REQID'];
}


//shiv code  on 29-11-2019
function send_SMS($sms_user, $sms_pass, $sender_id, $to_number, $message, $msg_type = null, $org_id = null, $provider_id = null)
{

	$username	= $sms_user;
	$password 	= $sms_pass;
	$number 	= $to_number;
	$sender 	= $sender_id;
	$message    = $message;
	//SHIV: Commented code on 29-1-2020 of BULKSMSGATEWAY
	/*$url="login.bulksmsgateway.in/unicodesmsapi.php?username=".urlencode($username)."&password=".urlencode($password)."&mobilenumber=".urlencode($number)."&message=".urlencode($message)."&senderid=".urlencode($sender)."&type=".urlencode('3'); 
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	return $curl_scraped_page = curl_exec($ch);
	curl_close($ch); */

	//SHIV: Added code 29-1-2020 TEXTLOCAl
	$apiKey = urlencode('MRDmSR3yXgs-0GSoxxpHaQpkz4h1hnHhmihSiYmBY7');
	$data = array('apikey' => $apiKey, 'numbers' => $number, "sender" => $sender, "message" => $message, "unicode" => true);
	// Send the POST request with cURL
	$ch = curl_init('https://api.textlocal.in/send/');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	return $response = curl_exec($ch);
	curl_close($ch);
}


function getConfigValue($key)
{
	$ci = &get_instance();
	$ci->load->database();
	$sql = "SELECT * FROM `master_config` where code='$key'";
	$q = $ci->db->query($sql);
	return $q->result()[0]->value;
}


/*
function sendBulkSMS($to_number,$message){
	$username	= SMS_USER;
	$password 	= SMS_PASSWORD;
	$number 	= $to_number;
	$sender 	= SMS_SYSTEM_TYPE;
	$message    = $message;
	$url="login.bulksmsgateway.in/unicodesmsapi.php?username=".urlencode($username)."&password=".urlencode($password)."&mobilenumber=".urlencode($number)."&message=".urlencode($message)."&senderid=".urlencode($sender)."&type=".urlencode('3'); 
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	return $curl_scraped_page = curl_exec($ch);
	curl_close($ch); 
}
*/

function allowCrossOrgin()
{

	// Allow from any origin
	if (isset($_SERVER['HTTP_ORIGIN'])) {
		// Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
		// you want to allow, and if so:
		header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
		header('Access-Control-Allow-Credentials: true');
		header("Access-Control-Allow-Headers: *");
		header('Access-Control-Max-Age: 86400');    // cache for 1 day
	}

	// Access-Control headers are received during OPTIONS requests
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
			header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
			header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

		exit(0);
	}
}

function call_url($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

// Function to get all the dates in given range 
function getDatesFromRange($start, $end, $format = 'Y-m-d')
{
	// Declare an empty array 
	$array = array();
	// Variable that store the date interval 
	// of period 1 day 
	$interval = new DateInterval('P1D');

	$realEnd = new DateTime($end);
	$realEnd->add($interval);

	$period = new DatePeriod(new DateTime($start), $interval, $realEnd);

	// Use loop to store date into array 
	foreach ($period as $date) {
		$array[] = $date->format($format);
	}

	// Return the array elements 
	return $array;
}

function timeDiff($start_time, $end_time)
{
	$start = strtotime($start_time);
	$end = strtotime($end_time);
	$seconds = $end - $start;
	return $hours = floor($seconds / 3600) . ":" . floor($seconds / 60 % 60) . ":" . floor($seconds % 60);
}

function timeDiffSecond($start_time, $end_time)
{
	$start = strtotime($start_time);
	$end = strtotime($end_time);
	return $seconds = $end - $start;
}

function getTime($seconds)
{
	return $hours = floor($seconds / 3600) . ":" . floor($seconds / 60 % 60) . ":" . floor($seconds % 60);
}

function get_enum_values($table, $field)
{
	$CI = &get_instance();
	$type = $CI->db->query("SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'")->row(0)->Type;
	preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
	$enum = explode("','", $matches[1]);
	return $enum;
}

//insert lead logs
function insert_lead_log($lead_id, $user_id, $comment)
{
	$CI = &get_instance();
	//$insert = $CI->db->query("");
	$data_array = array();
	$data_array['lead_id'] = $lead_id;
	$data_array['user_id'] = $user_id;
	$data_array['comment'] = $comment;


	$CI->db->insert("lead_logs", $data_array);
	$result_id = $CI->db->insert_id();
	return $result_id;
}

//insert application logs
function insert_application_log($lead_id, $action, $request_data, $response_data, $user_id)
{
	$CI = &get_instance();
	//$insert = $CI->db->query("");
	$data_array = array();
	$data_array['lead_id'] = $lead_id;
	$data_array['action'] = $action;
	$data_array['request_data'] = $request_data;
	$data_array['response_data'] = $response_data;
	$data_array['created_on'] = date("Y-m-d H:i:s");
	$data_array['created_by'] = $user_id;


	$CI->db->insert("application_logs", $data_array);
	$result_id = $CI->db->insert_id();
	return $result_id;
}

//insert proposal logs
function insert_proposal_log($lead_id, $user_id, $comment)
{
	$CI = &get_instance();
	//$insert = $CI->db->query("");
	$data_array = array();
	$data_array['lead_id'] = $lead_id;
	$data_array['user_id'] = $user_id;
	$data_array['remark'] = $comment;


	$CI->db->insert("proposal_logs", $data_array);
	$result_id = $CI->db->insert_id();
	return $result_id;
}


//Send SMS and EMail
/*function send_message($mobile_no="", $mail_to="", $mail_cc="", $mail_bcc="",$data=array(), $action=""){
	
		if(empty($mobile_no) && empty($mail_to) ){
			return array("msg"=>"Please provide mobile or email id.", "data"=>null);
			exit;
		}
		
		if(empty($action)){
			return array("msg"=>"Please provide action value.", "data"=>null);
			exit;
		}
		
		return array("msg"=>"Email & SMS information pending for client.", "data"=>null);
		exit;
		
		$senderID = 1;
		$AlertV1 = $query_check['emp_firstname']." ".$query_check['emp_lastname'];
		$AlertV2 = '1';
		$AlertV3 = $query_check['product_name'];
		$AlertV4 = '';
		$AlertV5 = '';
		
		$alertID = '';
		
		if($type == 'success'){
			
			if($query_check['Registrationmode'] == 'EMNB' || $query_check['Registrationmode'] == 'EMDC'){
				$alertID = 'A1407';
			}
			
			if($query_check['Registrationmode'] == 'SICC' || $query_check['Registrationmode'] == 'SIEMI'){
				$alertID = 'A1408';
			}
			
		}
		
		if($type == 'fail'){
			
			if($query_check['Registrationmode'] == 'EMNB' || $query_check['Registrationmode'] == 'EMDC'){
				$alertID = 'A1409';
			}
			
			if($query_check['Registrationmode'] == 'SICC' || $query_check['Registrationmode'] == 'SIEMI'){
				$alertID = 'A1411';
			}
			
			$AlertV4 = $query_check['EMandateFailureReason'];
			$AlertV5 = 'link';
		}
			
		
		
		$parameters =[
			"RTdetails" => [
		   
				"PolicyID" => '',
				"AppNo" => 'HD100017934',
				"alertID" => $alertID,
				"channel_ID" => 'Axis Freedom Plus',
				"Req_Id" => 1,
				"field1" => '',
				"field2" => '',
				"field3" => '',
				"Alert_Mode" => 2,
				"Alertdata" => 
					[
						"mobileno" => substr(trim($query_check['mob_no']), -10),
						"emailId" => $query_check['email'],
						"AlertV1" => $AlertV1,
						"AlertV2" => $AlertV2,
						"AlertV3" => $AlertV3,
						"AlertV4" => $AlertV4,
						"AlertV5" => $AlertV5,
					]

				]

			];
			 $parameters = json_encode($parameters);
			 $curl = curl_init();
			
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $query_check['click_pss_url'],
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => $parameters,
			  CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: application/json",
			   
			  ),
			));

		$response = curl_exec($curl);
		
		curl_close($curl);
		
		$request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($parameters),"res" => json_encode($response) ,"product_id"=> $query_check['product_code'], "type"=>"sms_logs_emandate".$type];
		
		$dataArray['tablename'] = 'logs_docs'; 
		$dataArray['data'] = $request_arr; 
		//$this->Logs_m->insertLogs($dataArray);
	
	}
	*/