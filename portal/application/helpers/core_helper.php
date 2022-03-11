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



function getConfigValue($key)
{
	$ci = &get_instance();
	$ci->load->database();
	$sql = "SELECT * FROM `master_config` where code='$key'";
	$q = $ci->db->query($sql);
	return $q->result()[0]->value;
}



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

