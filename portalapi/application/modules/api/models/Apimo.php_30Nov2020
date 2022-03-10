<?PHP
class Apimodel extends CI_Model
{
	function insertData($tbl_name,$data_array,$sendid = NULL)
	{
	 	$this->db->insert($tbl_name,$data_array);
	 	$result_id = $this->db->insert_id();
	 	
	 	/*echo $result_id;
	 	exit;*/
	 	
	 	if($sendid == 1)
	 	{
	 		//return id
	 		return $result_id;
	 	}
	}
	
	/*
		 * Fetch records from multiple tables [Join Queries] with multiple condition, Sorting, Limit, Group By
	*/
	function getdata_join($main_table = array(), $join_tables = array(), $condition = null, $sort_by = null, $group_by = null) {
		$columns = isset($main_table[1]) ? $main_table[1] : array();
		$main_table = $main_table[0];

		$join_str = "";
		foreach ($join_tables as $join_table) {
			$join_str .= $join_table[0] . " join " . $join_table[1] . " on (" . $join_table[2] . ") ";
			if (isset($join_table[3])) {
				$columns = array_merge($columns, $join_table[3]);
			}
		}

		$columns = (sizeof($columns) > 0) ? implode(", ", $columns) : "*";

		if (is_null($condition) || $condition == "") {
			$condition = "1=1";
		}

		$sort_order = "";
		if (is_array($sort_by) && $sort_by != null) {
			foreach ($sort_by as $key => $val) {
				$sort_order .= ($sort_order == "") ? "order by $key $val" : ", $key $val";
			}
		}

		if ($group_by != null) {
			$group_by = "group by " . $group_by;
		}

		//$this->db->query($this->set_timezone_query);
		$sql = trim("select $columns from $main_table $join_str where $condition $group_by $sort_order");
		// echo $sql.'<br/><br/><br/>';
		// exit;
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	
	
	
	
	function insertBatchData($tbl_name,$data_array,$sendid = NULL)
	{
	 	$this->db->insert_batch($tbl_name,$data_array);
	 	$result_id = $this->db->insert_id();
	 	
	 	/*echo $result_id;
	 	exit;*/
	 	
	 	if($sendid == 1)
	 	{
	 		//return id
	 		return $result_id;
	 	}
	}
	
	function login_check($condition) {
		$this -> db -> select('i.employee_id , i.employee_fname, i.employee_mname,i.employee_lname,i.employee_code,i.email_id,i.mobile_number,i.role_id');
		$this -> db -> from('master_employee as i');
		$this -> db -> where("($condition)");
		
		$query = $this -> db -> get();
		//print_r($this->db->last_query());
		//exit;
	 
		if($query -> num_rows() >= 1)
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}
    
	function fetch_otp($mobile,$country_code){
		
		//valid check 15 minutes
		$current_date_time = date("Y-m-d H:i:s");
		$current_timstamp = strtotime($current_date_time);
		$current_timstamp = $current_timstamp - (15 * 60);
		$from_date_time = date("Y-m-d H:i:s", $current_timstamp);

		$otp_condition = "";
		$sql = $this->db->query("select * from tbl_otp_check where  mobile = ".$this->db->escape($mobile)." && country_code = ".$this->db->escape($country_code)." && (time_stamp >='".$from_date_time."' && time_stamp <='".$current_date_time."' ) ");
		if($sql->num_rows()>0){
			return $sql->result();
		}else{
			return false;
		}
	}
  
	function generate_otp($mobile,$country_code,$otp){
		$sql = $this->db->query("select * from tbl_otp_check where mobile = ".$this->db->escape($mobile).' And country_code ='.$this->db->escape($country_code) );
		$data = array(
               "mobile"=>$mobile,
               "country_code"=>$country_code,
               "otp" => $otp,
               "time_stamp"=>date('Y-m-d H:i:s')
		);
    
		if($sql->num_rows() > 0){
			$this->updatedataotp($data, "tbl_otp_check", "mobile", $mobile,"country_code", $country_code);
		}else{
			$this->insertData("tbl_otp_check",$data,1);
		}
		
		return true;
	}
  
	function updatedataotp($data,$table,$column1,$value1,$column2,$value2){
		$this->db->where($column1,$value1);
		$this->db->where($column2,$value2);
		$this->db->update($table,$data);
	}
	
	function runquery($sql_query = ''){
		$query = $this->db->query($sql_query);
		if($query->num_rows() > 0){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function runquery_array($sql_query = ''){
		$query = $this->db->query($sql_query);
		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return false;
		}
	}
	
	function getdata($table, $fields, $condition = '1=1'){
		//echo "Select $fields from $table where $condition";exit;
		$sql = $this->db->query("Select $fields from $table where $condition");
		if($sql->num_rows() > 0){
			return $sql->result_array();
		}else{
			return false;
		}
	}
	
	function getSortedData($select, $table, $condition = "", $sort_col = "", $sort_order = "")
	{	
		$this->db->select($select);
		$this->db->from($table);
		
		if(!empty($condition)){
			$this->db->where($condition);
		}
		
		if(!empty($sort_col) && !empty($sort_order)){
			$this->db->order_by($sort_col, $sort_order);
		}
		
		$query = $this->db->get();
		
		// echo "<br/>";
		// print_r($this->db->last_query());
		// exit;
	   
		if($query->num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}
	}
	
	function getdataCount($table, $fields, $condition = '1=1'){
		
		$sql = $this->db->query("Select count(*) as total  from $table where $condition");
		if($sql->num_rows() > 0){
		    $cnt = $sql->result_array();
			return $cnt[0]['total'];
		}else{
			return 0;
		}
	}
	
	function getdata_orderby($table, $fields, $condition = '1=1', $order_by){
		//echo "Select $fields from $table where $condition";exit;
		$sql = $this->db->query("Select $fields from $table where $condition $order_by");
		if($sql->num_rows() > 0){
			return $sql->result_array();
		}else{
			return false;
		}
	}
	
	function getdata_groupby_orderby($table, $fields, $condition = '1=1', $group_by="", $order_by=""){
		//echo "Select $fields from $table where $condition";exit;
		$sql = $this->db->query("Select $fields from $table where $condition $group_by $order_by");
		if($sql->num_rows() > 0){
			return $sql->result_array();
		}else{
			return false;
		}
	}
	
	function getdata_groupby_orderby_limit($table, $fields, $condition = '1=1', $group_by="", $order_by="", $page ){
		$rows = 10;
		$page = $rows * $page;
		$limit = $page.",".$rows;
		
		$sql = $this->db->query("Select $fields from $table where $condition $group_by $order_by limit $limit");
		//print_r($this->db->last_query());
		//exit;
		
		if($sql->num_rows() > 0){
			return $sql->result_array();
		}else{
			return false;
		}
	}
	
	function getdata_orderby_limit($table, $fields, $condition = '1=1', $order_by, $page){
	
		$rows = 10;
		$page = $rows * $page;
		$limit = $page.",".$rows;
		
		$sql = $this->db->query("Select $fields from $table where $condition $order_by limit $limit");
		//print_r($this->db->last_query());
		//exit;
		
		if($sql->num_rows() > 0){
			return $sql->result_array();
		}else{
			return false;
		}
	}
	
	function getdata_orderby1($table, $fields, $condition = '1=1', $order_by){
		//echo "Select $fields from $table where $condition";exit;
		$sql = $this->db->query("Select $fields from $table where $condition order by $order_by desc limit 1");
		if($sql->num_rows() > 0){
			return $sql->result_array();
		}else{
			return false;
		}
	}

	function getdata_orderby2($table, $fields, $condition = '1=1', $order_by){
		//echo "Select $fields from $table where $condition";exit;
		$sql = $this->db->query("Select $fields from $table where $condition order by $order_by limit 1");
		if($sql->num_rows() > 0){
			return $sql->result_array();
		}else{
			return false;
		}
	}
    
    
    
	function updateRecord($tbl_name,$datar,$condition)
	{
		//$this -> db -> where($comp_col, $eid);
		$this -> db -> where("($condition)");
		$this -> db -> update($tbl_name,$datar);
		 
		if ($this->db->affected_rows() > 0){
			return true;
		}else{
			return true;
		} 
	}	
	
	function delrecord($tbl_name,$tbl_id,$record_id)
	{
		$this->db->where($tbl_id, $record_id);
	    $this->db->delete($tbl_name);
		if($this->db->affected_rows() >= 1)
		{
			return true;
	    }
	    else
	    {
			return false;
	    }
	}
	
	function delrecord_condition($tbl_name,$condition)
	{
		//$this->db->where($tbl_id, $record_id);
		$this->db->where("($condition)");
		$this->db->delete($tbl_name);
		if($this->db->affected_rows() >= 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function check_utoken($decode_utoken,$usertype,$apptype,$login_userid) {
		$this -> db -> select('u.*');
		$this -> db -> from('tbl_users_master as u');
		
		if ($apptype != 'W'){
			if($usertype == 'P' ){
				$this -> db -> where('concat(u.user_master_id,provider_device_id)',$decode_utoken);
			}
			else if($usertype == 'C'){
				$this -> db -> where('concat(u.user_master_id,consumer_device_id)',$decode_utoken);
			}
		}
		// if called via web check decode_utoken only with user id
		else{
			$this -> db -> where('u.user_master_id',$decode_utoken);
		}

		//also check with login user id	- bascially user id within the decode_utoken should be same as loginuser id
		$this -> db -> where('u.user_master_id',$login_userid); 
		$this -> db -> where('u.status',"Active");	

		
		$query = $this -> db -> get();
	   
		//print_r($this->db->last_query());
		//exit;
	   
		if($query -> num_rows() >= 1)
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}
	
	function check_registered_utoken($utoken) {
		$this -> db -> select('u.*');
		$this -> db -> from('tbl_users as u');	
		$this -> db -> where('u.email',$utoken);
		$query = $this -> db -> get();
	
		if($query -> num_rows() >= 1)
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
		
	}
	
	
	function getRecords($tbl_name, $condition, $page=null, $default_sort_column=null, $default_sort_order=null, $group_by=null){
		
		$table = $tbl_name;
		$default_sort_column = $default_sort_column;
		$default_sort_order = $default_sort_order;
		if($page != null){
			$rows = 10;
			$page = $rows * $page;
		}
		
		// sort order by column
		$sort = $default_sort_column;  
		$order = $default_sort_order;

		$this -> db -> select('*');
		$this -> db -> from($tbl_name);
		
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		if($page != null){
			$this->db->limit($rows,$page);
		}
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
				
		if($query -> num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}
	}
	
	
	function getCreditorList($post)
	{
		$table = "master_ceditors";
		$table_id = 'creditor_id ';
		$default_sort_column = 'creditor_id ';
		$default_sort_order = 'desc';
		$condition = "1=1";
		
		$colArray = array('i.creaditor_name');
		$sortArray = array('i.creaditor_name','i.creditor_code','i.ceditor_email','i.creditor_mobile','i.creditor_phone','i.creditor_pancard','i.creditor_gstn','i.isactive');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		$sort = isset($post['iSortCol_0']) ? strval($sortArray[$post['iSortCol_0']]) : $default_sort_column;  
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<1;$i++)
		{
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('*');
		$this -> db -> from('master_ceditors as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('*');
		$this -> db -> from('master_ceditors as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0);
		}
	}
	
	function getCreditorFormData($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('master_ceditors as i');
		$this -> db -> where('i.creditor_id', $ID);
	
		$query = $this -> db -> get();
	   
		//print_r($this->db->last_query());
		//exit;
	   
		if($query -> num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}	
	}
	
	function getLoginUserDetails($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('master_employee as i');
		$this -> db -> where('i.employee_id', $ID);
	
		$query = $this -> db -> get();
	   
		//print_r($this->db->last_query());
		//exit;
	   
		if($query -> num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}	
	}
	
	
	function getPermissionList($post)
	{
		$table = "permissions";
		$table_id = 'perm_id';
		$default_sort_column = 'perm_desc';
		$default_sort_order = 'asc';
		$condition = "1=1";
		
		$colArray = array('i.perm_desc');
		$sortArray = array('i.perm_desc');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		$sort = isset($post['iSortCol_0']) ? strval($sortArray[$post['iSortCol_0']]) : $default_sort_column;  
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<1;$i++)
		{
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('*');
		$this -> db -> from('permissions as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('*');
		$this -> db -> from('permissions as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0);
		}
	}
	
	function getPermissionFormData($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('permissions as i');
		$this -> db -> where('i.perm_id', $ID);
	
		$query = $this -> db -> get();
	   
		//print_r($this->db->last_query());
		//exit;
	   
		if($query -> num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}	
	}
	
	function getRoleList($post)
	{
		$table = "roles";
		$table_id = 'role_id';
		$default_sort_column = 'role_name';
		$default_sort_order = 'asc';
		$condition = "1=1";
		
		$colArray = array('i.role_name');
		$sortArray = array('i.role_name');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		$sort = isset($post['iSortCol_0']) ? strval($sortArray[$post['iSortCol_0']]) : $default_sort_column;  
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<1;$i++)
		{
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('*');
		$this -> db -> from('roles as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('*');
		$this -> db -> from('roles as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0);
		}
	}
	
	function getRoleFormData($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('roles as i');
		$this -> db -> where('i.role_id', $ID);
	
		$query = $this -> db -> get();
	   
		//print_r($this->db->last_query());
		//exit;
	   
		if($query -> num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}	
	}
	
	function getUserList($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "master_employee";
		$table_id = 'employee_id';
		$default_sort_column = 'employee_id';
		$default_sort_order = 'desc';
		$condition = "1=1";
		
		$colArray = array('i.user_name','i.employee_fname','i.employee_lname','i.employee_code','i.email_id','i.mobile_number','i.role_id','i.isactive');
		$sortArray = array('i.user_name','i.employee_fname','i.employee_lname','i.employee_code','i.email_id','i.mobile_number','i.role_id','i.isactive');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		//echo $post['iSortCol_0'];exit;
		//$s = $post['iSortCol_0'];
		$sort = isset($post['iSortCol_0']) ? strval($post['iSortCol_0']) : $default_sort_column; 
		//echo $sort;exit;		
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<8;$i++)
		{
			if($i==6)
			{
				if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
				{
					$condition .= " AND $colArray[$i] = '".$_POST['sSearch_'.$i]."'";
				}
			}
			else
			{
				if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
				{
					$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
				}
			}
			
			
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('*');
		$this -> db -> from('master_employee as i');
		$this -> db -> join('roles as r', 'i.role_id  = r.role_id', 'left');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('*');
		$this -> db -> from('master_employee as i');
		$this -> db -> join('roles as r', 'i.role_id  = r.role_id', 'left');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0);
		}
	}
	
	function getlocationList($post)
	{
		$table = "cities";
		$table_id = 'city_id';
		$default_sort_column = 's.state_name';
		$default_sort_order = 'asc';
		$condition = "1=1";
		
		$colArray = array('s.state_name','i.city_name');
		$sortArray = array('s.state_name','i.city_name');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		$sort = isset($post['iSortCol_0']) ? strval($sortArray[$post['iSortCol_0']]) : $default_sort_column;  
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<5;$i++)
		{
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.city_id,i.city_name, i.isactive,s.state_name');
		$this -> db -> from('cities as i');
		$this -> db -> join('states as s', 'i.city_state_id  = s.state_id ', 'left');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.city_id,i.city_name, i.isactive,s.state_name');
		$this -> db -> from('cities as i');
		$this -> db -> join('states as s', 'i.city_state_id  = s.state_id ', 'left');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0);
		}
	}
	
	function getLocationFormData($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('cities as i');
		$this -> db -> where('i.city_id', $ID);
	
		$query = $this -> db -> get();
	   
		//print_r($this->db->last_query());
		//exit;
	   
		if($query -> num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}	
	}
	
	function branchListing($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "creditor_branches";
		$table_id = 'branch_id';
		$default_sort_column = 'branch_name';
		$default_sort_order = 'asc';
		$condition = "1=1";
		
		$colArray = array('i.branch_name','c.creaditor_name','l.location_name','i.contact_no','i.email_id','i.isactive');
		$sortArray = array('i.branch_name','c.creaditor_name','l.location_name','i.contact_no','i.email_id','i.isactive');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		//echo $post['iSortCol_0'];exit;
		$sort = isset($post['iSortCol_0']) ? strval($post['iSortCol_0']) : $default_sort_column; 
		//echo $sort;exit;		
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<8;$i++)
		{
			if($i==6)
			{
				if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
				{
					$condition .= " AND $colArray[$i] = '".$_POST['sSearch_'.$i]."'";
				}
			}
			else
			{
				if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
				{
					$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
				}
			}
			
			
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.*, c.creaditor_name, l.location_name');
		$this -> db -> from('creditor_branches as i');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> join('master_location as l', 'i.location_id  = l.location_id', 'left');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('*');
		$this -> db -> from('creditor_branches as i');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> join('master_location as l', 'i.location_id  = l.location_id', 'left');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0);
		}
	}
	
	function getBranchesFormData($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('creditor_branches as i');
		$this -> db -> where('i.branch_id', $ID);
	
		$query = $this -> db -> get();
	   
		//print_r($this->db->last_query());
		//exit;
	   
		if($query -> num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}	
	}
	
	function getLoginUserAccess($role_id)
	{	
		$condition = "i.role_id='".$role_id."' ";
		$this -> db -> select('p.perm_desc');
		$this -> db -> from('role_perm as i');
		$this -> db -> join('permissions as p', 'i.perm_id   = p.perm_id ', 'left');
		$this->db->where("($condition)");
		$this->db->order_by('i.perm_id', 'asc');
		
		$query = $this->db->get();
		
		// echo "<br/>";
		// print_r($this->db->last_query());
		// exit;
	   
		if($query->num_rows() >= 1)
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}
	
	function smCreditorMappingListing($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "sm_creditor_mapping";
		$table_id = 'sm_creditor_id';
		$default_sort_column = 'e.employee_full_name';
		$default_sort_order = 'asc';
		$condition = "1=1";
		
		$colArray = array('e.employee_full_name','c.creaditor_name','i.isactive');
		$sortArray = array('e.employee_full_name','c.creaditor_name','i.isactive');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		//echo $post['iSortCol_0'];exit;
		$sort = isset($post['iSortCol_0']) ? strval($post['iSortCol_0']) : $default_sort_column; 
		//echo $sort;exit;		
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<8;$i++)
		{
			if($i==6)
			{
				if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
				{
					$condition .= " AND $colArray[$i] = '".$_POST['sSearch_'.$i]."'";
				}
			}
			else
			{
				if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
				{
					$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
				}
			}
			
			
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.*, e.employee_full_name, c.creaditor_name');
		$this -> db -> from('sm_creditor_mapping as i');
		$this -> db -> join('master_employee as e', 'i.sm_id  = e.employee_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.*, e.employee_full_name, c.creaditor_name');
		$this -> db -> from('sm_creditor_mapping as i');
		$this -> db -> join('master_employee as e', 'i.sm_id  = e.employee_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0);
		}
	}
	
	function getSMCreditorFormData($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('sm_creditor_mapping as i');
		$this -> db -> where('i.sm_creditor_id', $ID);
	
		$query = $this -> db -> get();
	   
		//print_r($this->db->last_query());
		//exit;
	   
		if($query -> num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}	
	}
	
	function leadListing($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "lead_details";
		$table_id = 'lead_id';
		$default_sort_column = 'lead_id';
		$default_sort_order = 'desc';
		if($_POST['role_id'] == 3){
			$condition = "i.createdby='".$_POST['user_id']."' ";
		}else{
			$condition = "1=1";
		}
		
		$colArray = array('i.trace_id','p.plan_name','c.creaditor_name','s.employee_full_name','cust.full_name','i.mobile_no','i.email_id','i.status');
		$sortArray = array('i.trace_id','p.plan_name','c.creaditor_name','s.employee_full_name','cust.full_name','i.mobile_no','i.email_id','i.status');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		//echo $post['iSortCol_0'];exit;
		//$s = $post['iSortCol_0'];
		$sort = isset($post['iSortCol_0']) ? strval($post['iSortCol_0']) : $default_sort_column; 
		//echo $sort;exit;		
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<9;$i++)
		{
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
			
			
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.lead_id, i.trace_id, i.plan_id, i.creditor_id, i.sales_manager_id, i.primary_customer_id, i.mobile_no, i.email_id, p.plan_name, c.creaditor_name, cust.full_name, s.employee_full_name, i.status');
		$this -> db -> from('lead_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> join('master_employee as s', 'i.sales_manager_id  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.primary_customer_id  = cust.customer_id', 'left');
		
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.lead_id, i.trace_id, i.plan_id, i.creditor_id, i.sales_manager_id, i.primary_customer_id, i.mobile_no, i.email_id, p.plan_name, c.creaditor_name, cust.full_name, s.employee_full_name, i.status');
		$this -> db -> from('lead_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> join('master_employee as s', 'i.sales_manager_id  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.primary_customer_id  = cust.customer_id', 'left');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0);
		}
	}
	
	
	function getRoleWiseCreditorsData($user_id)
	{	
		$condition = "i.sm_id='".$user_id."' ";
		
		$this -> db -> select('i.creditor_id, c.creaditor_name');
		$this -> db -> from('sm_creditor_mapping as i');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> where("($condition)");
		
		$query = $this->db->get();
		
		// echo "<br/>";
		// print_r($this->db->last_query());
		// exit;
	   
		if($query->num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}
	}
	
	function discrepancyProposalListing($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "lead_details";
		$table_id = 'lead_id';
		$default_sort_column = 'lead_id';
		$default_sort_order = 'desc';
		if($_POST['role_id'] == 3){
			$condition = "i.createdby='".$_POST['user_id']."' ";
		}else{
			$condition = "1=1";
		}
		
		$condition .= " && i.status='Discrepancy' ";
		
		$colArray = array('i.trace_id','p.plan_name','c.creaditor_name','s.employee_full_name','cust.full_name','i.mobile_no','i.email_id','i.status');
		$sortArray = array('i.trace_id','p.plan_name','c.creaditor_name','s.employee_full_name','cust.full_name','i.mobile_no','i.email_id','i.status');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		//echo $post['iSortCol_0'];exit;
		//$s = $post['iSortCol_0'];
		$sort = isset($post['iSortCol_0']) ? strval($post['iSortCol_0']) : $default_sort_column; 
		//echo $sort;exit;		
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<9;$i++)
		{
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
			
			
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.lead_id, i.trace_id, i.plan_id, i.creditor_id, i.sales_manager_id, i.primary_customer_id, i.mobile_no, i.email_id, p.plan_name, c.creaditor_name, cust.full_name, s.employee_full_name, i.status');
		$this -> db -> from('lead_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> join('master_employee as s', 'i.sales_manager_id  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.primary_customer_id  = cust.customer_id', 'left');
		
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.lead_id, i.trace_id, i.plan_id, i.creditor_id, i.sales_manager_id, i.primary_customer_id, i.mobile_no, i.email_id, p.plan_name, c.creaditor_name, cust.full_name, s.employee_full_name, i.status');
		$this -> db -> from('lead_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> join('master_employee as s', 'i.sales_manager_id  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.primary_customer_id  = cust.customer_id', 'left');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0);
		}
	}
	
	function customerProposalListing($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "proposal_details";
		$table_id = 'proposal_details_id ';
		$default_sort_column = 'proposal_details_id ';
		$default_sort_order = 'desc';
		if($_POST['role_id'] == 3){
			$condition = "i.created_by='".$_POST['user_id']."' ";
		}else{
			$condition = "1=1";
		}
		
		$colArray = array('i.trace_id','p.plan_name','s.employee_full_name','cust.full_name');
		$sortArray = array('i.trace_id','p.plan_name','s.employee_full_name','cust.full_name');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		//echo $post['iSortCol_0'];exit;
		//$s = $post['iSortCol_0'];
		$sort = isset($post['iSortCol_0']) ? strval($post['iSortCol_0']) : $default_sort_column; 
		//echo $sort;exit;		
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<8;$i++)
		{
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
			
			
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.proposal_details_id, i.lead_id, i.trace_id, i.plan_id, i.trace_id, i.created_by, i.customer_id, p.plan_name, cust.full_name, s.employee_full_name');
		$this -> db -> from('proposal_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_employee as s', 'i.created_by  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.customer_id  = cust.customer_id', 'left');
		
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.proposal_details_id, i.lead_id, i.trace_id, i.plan_id, i.trace_id, i.created_by, i.customer_id, p.plan_name, cust.full_name, s.employee_full_name');
		$this -> db -> from('proposal_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_employee as s', 'i.created_by  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.customer_id  = cust.customer_id', 'left');
		
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0);
		}
	}
	
	
	function boProposalListing($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "lead_details";
		$table_id = 'lead_id';
		$default_sort_column = 'lead_id';
		$default_sort_order = 'desc';
		
		$condition = "(i.status='BO-Approval-Awaiting' || i.status='Discrepancy' || i.status='Approved' || i.status='Rejected') && i.mode_of_payment='Cheque' ";
		
		$colArray = array('i.trace_id','p.plan_name','c.creaditor_name','s.employee_full_name','cust.full_name','i.mobile_no','i.email_id','i.status');
		$sortArray = array('i.trace_id','p.plan_name','c.creaditor_name','s.employee_full_name','cust.full_name','i.mobile_no','i.email_id','i.status');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		//echo $post['iSortCol_0'];exit;
		//$s = $post['iSortCol_0'];
		$sort = isset($post['iSortCol_0']) ? strval($post['iSortCol_0']) : $default_sort_column; 
		//echo $sort;exit;		
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<9;$i++)
		{
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
			
			
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.lead_id, i.trace_id, i.plan_id, i.creditor_id, i.sales_manager_id, i.primary_customer_id, i.mobile_no, i.email_id, p.plan_name, c.creaditor_name, cust.full_name, s.employee_full_name, i.status, i.updatedon');
		$this -> db -> from('lead_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> join('master_employee as s', 'i.sales_manager_id  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.primary_customer_id  = cust.customer_id', 'left');
		
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.lead_id, i.trace_id, i.plan_id, i.creditor_id, i.sales_manager_id, i.primary_customer_id, i.mobile_no, i.email_id, p.plan_name, c.creaditor_name, cust.full_name, s.employee_full_name, i.status, i.updatedon');
		$this -> db -> from('lead_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> join('master_employee as s', 'i.sales_manager_id  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.primary_customer_id  = cust.customer_id', 'left');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0);
		}
	}
	
	
	function coProposalListing($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "lead_details";
		$table_id = 'lead_id';
		$default_sort_column = 'lead_id';
		$default_sort_order = 'desc';
		
		$condition = "(i.status='CO-Approval-Awaiting' || i.status='Discrepancy' || i.status='Approved' || i.status='Rejected') && i.mode_of_payment='NEFT'  ";
		
		
		$colArray = array('i.trace_id','p.plan_name','c.creaditor_name','s.employee_full_name','cust.full_name','i.mobile_no','i.email_id','i.status');
		$sortArray = array('i.trace_id','p.plan_name','c.creaditor_name','s.employee_full_name','cust.full_name','i.mobile_no','i.email_id','i.status');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		//echo $post['iSortCol_0'];exit;
		//$s = $post['iSortCol_0'];
		$sort = isset($post['iSortCol_0']) ? strval($post['iSortCol_0']) : $default_sort_column; 
		//echo $sort;exit;		
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<9;$i++)
		{
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
			
			
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.lead_id, i.trace_id, i.plan_id, i.creditor_id, i.sales_manager_id, i.primary_customer_id, i.mobile_no, i.email_id, p.plan_name, c.creaditor_name, cust.full_name, s.employee_full_name, i.status, i.updatedon');
		$this -> db -> from('lead_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> join('master_employee as s', 'i.sales_manager_id  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.primary_customer_id  = cust.customer_id', 'left');
		
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.lead_id, i.trace_id, i.plan_id, i.creditor_id, i.sales_manager_id, i.primary_customer_id, i.mobile_no, i.email_id, p.plan_name, c.creaditor_name, cust.full_name, s.employee_full_name, i.status, i.updatedon');
		$this -> db -> from('lead_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> join('master_employee as s', 'i.sales_manager_id  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.primary_customer_id  = cust.customer_id', 'left');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0);
		}
	}
	
	function uwProposalListing($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "lead_details";
		$table_id = 'lead_id';
		$default_sort_column = 'lead_id';
		$default_sort_order = 'desc';
		
		$condition = "i.status='UW-Approval-Awaiting' ";
		
		$colArray = array('i.trace_id','p.plan_name','c.creaditor_name','s.employee_full_name','cust.full_name','i.mobile_no','i.email_id','i.status');
		$sortArray = array('i.trace_id','p.plan_name','c.creaditor_name','s.employee_full_name','cust.full_name','i.mobile_no','i.email_id','i.status');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		//echo $post['iSortCol_0'];exit;
		//$s = $post['iSortCol_0'];
		$sort = isset($post['iSortCol_0']) ? strval($post['iSortCol_0']) : $default_sort_column; 
		//echo $sort;exit;		
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<9;$i++)
		{
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
			
			
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.lead_id, i.trace_id, i.plan_id, i.creditor_id, i.sales_manager_id, i.primary_customer_id, i.mobile_no, i.email_id, p.plan_name, c.creaditor_name, cust.full_name, s.employee_full_name, i.status, i.updatedon');
		$this -> db -> from('lead_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> join('master_employee as s', 'i.sales_manager_id  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.primary_customer_id  = cust.customer_id', 'left');
		
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.lead_id, i.trace_id, i.plan_id, i.creditor_id, i.sales_manager_id, i.primary_customer_id, i.mobile_no, i.email_id, p.plan_name, c.creaditor_name, cust.full_name, s.employee_full_name, i.status, i.updatedon');
		$this -> db -> from('lead_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> join('master_employee as s', 'i.sales_manager_id  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.primary_customer_id  = cust.customer_id', 'left');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0);
		}
	}
	
	
	/*** 
		Policy Genration - Quick Qoute
		
		$emp_id = customer_id, 
		$policy_detail_id = master_policy_id, 
		$proposal_policy_id = proposal_policy_id
		$nominees = proposal nominee
	***/
	
	
	public function get_quote_data($lead_id, $emp_id,$master_policy_id, $proposal_policy_id, $proposal_details, $policy_sub_type_id, $sum_insured,$count=1){ 
		
		//get proposal policy details
		$data = array();
		$data['customer_data'] = (array)$this->get_profile($emp_id);
		$data['member_data'] = (array)$this->get_all_member_data($proposal_policy_id);
		
		$nominees = array();
		if(!empty($proposal_details)){
			$nominees['nominee_relation'] = $proposal_details[0]['nominee_relation'];
			$nominees['nominee_first_name'] = $proposal_details[0]['nominee_first_name'];
			$nominees['nominee_last_name'] = $proposal_details[0]['nominee_last_name'];
			$nominees['nominee_salutation'] = $proposal_details[0]['nominee_salutation'];
			$nominees['nominee_gender'] = $proposal_details[0]['nominee_gender'];
			$nominees['nominee_dob'] = $proposal_details[0]['nominee_dob'];
			$nominees['nominee_contact'] = $proposal_details[0]['nominee_contact'];
			$nominees['nominee_email'] = $proposal_details[0]['nominee_email'];
		}
		$data['nominee_data'] = $nominees;
		$data['proposal_data'] = $proposal_details;
		
		
		
		//echo "<pre>";print_r($data);exit;
		
		//get Api URL with policy_sub_type_id
		$getApiUrl = $this->db->query("select api_url from master_policy_sub_type where policy_sub_type_id='".$policy_sub_type_id."' ")->row_array();
		
		$url = trim($getApiUrl['api_url']);
		
		if($url == ''){
			return array(
				"status" => "error",
				"msg" => "Something Went Wrong"
			);
			exit;
		}
		 
		//echo $url;exit;
		
		$occupation = '';
		
		//Data Variables
		//$Member_Customer_ID = $data['customer_data']['customer_id'];
		$Member_Customer_ID = 10000000000004;
		$uidNo = (!empty($data['customer_data']['adhar'])) ? $data['customer_data']['adhar'] : null;
		
		$MasterPolicyNumber = '61-20-00040-00-00';
		$GroupID = 'GRP001';
		//$PlanCode = '4211';
		//$plan_code = (!empty($data['proposal_data']['plan_code'])) ? $data['proposal_data']['plan_code'] : null;
		$plan_code = '4211';
		$Product_Code = '4211';
		
		$Member_Type_Code = '';
		$intermediaryCode = '2108233';
		$intermediaryBranchCode = '10MHMUM01';
		$SchemeCode = '4112000003';
		
		//get SumInsured Type
		$sumins_type = $this->db->query("select mi.suminsured_type from master_policy_si_type_mapping as m left join master_suminsured_type as mi ON m.suminsured_type_id = mi.suminsured_type_id where m.master_policy_id='$master_policy_id' ")->row();
		
		//echo "<pre>";print_r($sumins_type);exit;
		$SumInsured_Type = $sumins_type->suminsured_type;
		//echo $SumInsured_Type;exit;
		
		
		
		$leadID = $lead_id;
		//$SumInsured = $sum_insured;
		$SumInsured = 500000;
		$product_id = $data['proposal_data'][0]['plan_id'];
		
		
		$SPID = 0;
		$RefCode1 = 0;
		$RefCode2 = 0;
		$Policy_Tanure = 1;
		$AutoRenewal = 'Y';
		//$intermediaryBranchCode = "10MHMUM01";
		
		$totalMembers = count($data['member_data']);
		$member = [];
		
		$explode_name = array($data['customer_data']['first_name'],$data['customer_data']['middle_name'],$data['customer_data']['last_name']);
		$explode_name_nominee = array($data['nominee_data']['nominee_first_name'],$data['nominee_data']['nominee_last_name']);
		
		for ($i = 0; $i < $totalMembers; $i++){
			//Checking relation based on self, spouse, son and daughter
			if($data['member_data'][$i]['relation_with_proposal'] == 1 ){
				$data['member_data'][$i]['relation_code'] = "R001";
			}else if($data['member_data'][$i]['relation_with_proposal'] == 2 ){
				$data['member_data'][$i]['relation_code'] = "R002";
			}else if($data['member_data'][$i]['relation_with_proposal'] == 3 ){
				$data['member_data'][$i]['relation_code'] = "R003";
			}else{
				$data['member_data'][$i]['relation_code'] = "R004";
			}
			
			//Nominee relation code
			if($data['nominee_data']['nominee_relation'] == 1 ){
				$data['nominee_data']['relation_code'] = "R001";
			}else if($data['nominee_data']['nominee_relation'] == 2 ){
				$data['nominee_data']['relation_code'] = "R002";
			}else if($data['nominee_data']['nominee_relation'] == 3 ){
				$data['nominee_data']['relation_code'] = "R003";
			}else{
				$data['nominee_data']['relation_code'] = "R004";
			}
			
			
			$abc = ["PEDCode" => null, "Remarks" => null];
			
			$explode_name_member = explode(" ", trim($data['member_data'][$i]['policy_member_first_name']),2);
		 
		
			$member[] = ["MemberNo" => $i + 1, "Salutation" => (($data['member_data'][$i]['policy_member_gender'] == "Male") ? 'Mr' : (($data['member_data'][$i]['policy_member_age'] >= 18) ? 'Mrs' : 'Ms')) , "First_Name" => $data['member_data'][$i]['policy_member_first_name'], "Middle_Name" => null, "Last_Name" => !empty($data['member_data'][$i]['policy_member_last_name']) ? $data['member_data'][$i]['policy_member_last_name'] : '.', "Gender" => ($data['member_data'][$i]['policy_member_gender'] == "Male") ? "M" : "F", "DateOfBirth" => date('m/d/Y', strtotime($data['member_data'][$i]['policy_member_dob'])) , "Relation_Code" => trim($data['member_data'][$i]['relation_code']), "Marital_Status" => null, "height" => "0.00", "weight" => "0", "occupation" => "O553", "PrimaryMember" => (($i == 0) ? "Y" : "N") , "MemberproductComponents" => [["PlanCode" => $plan_code, "MemberQuestionDetails" => [["QuestionCode" => null, "Answer" => null, "Remarks" => null]]]], "MemberPED" => $abc, "exactDiagnosis" => null, "dateOfDiagnosis" => null, "lastDateConsultation" => null, "detailsOfTreatmentGiven" => null, "doctorName" => null, "hospitalName" => null, "phoneNumberHosital" => null, "Nominee_First_Name" => $explode_name_nominee[0], "Nominee_Last_Name" => !empty($explode_name_nominee[1]) ? $explode_name_nominee[1] : '.', "Nominee_Contact_Number" => (!empty($data['nominee_data']['nominee_contact'])) ? $data['nominee_data']['nominee_contact'] : null, "Nominee_Home_Address" => null, "Nominee_Relationship_Code" => trim($data['nominee_data']['relation_code']), ];

		}
		
		//echo "<pre>";print_r($member);exit;
		$data['proposal_data']['SourceSystemName_api'] = 'CreditorPortal';
		
		
		//echo $policy_detail_id;exit;
		if($policy_sub_type_id == 1){ // ghi
			$fqrequest = ["ClientCreation" => ["Member_Customer_ID" =>  $Member_Customer_ID , "salutation" => $data['customer_data']['salutation'], "firstName" => $explode_name[0], "middleName" => "", "lastName" => !empty($explode_name[1]) ? $explode_name[1] : '.', "dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['dob'])) , "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F", "educationalQualification" => null, "pinCode" => $data['customer_data']['pincode'], "uidNo" => $uidNo, "maritalStatus" => null, "nationality" => "Indian", "occupation" => $occupation, "primaryEmailID" => $data['customer_data']['email_id'], "contactMobileNo" => substr(trim($data['customer_data']['mobile_no']), -10), "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => null, "remarks" => null, "startDate" => date('Y-m-d') , "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['address_line1'], "homeAddressLine2" => null, "homeAddressLine3" => null, "homePinCode" => $data['customer_data']['pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null, "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null], "PolicyCreationRequest" => ["Quotation_Number" => "", "MasterPolicyNumber" => $MasterPolicyNumber, "GroupID" => $GroupID, "Product_Code" => $Product_Code, "intermediaryCode" => $intermediaryCode, "AutoRenewal" => $AutoRenewal, "intermediaryBranchCode" => $intermediaryBranchCode, "agentSignatureDate" => null, "Customer_Signature_Date" => null, "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $leadID, "Source_Name" => $data['proposal_data']['SourceSystemName_api'], "SPID" => $SPID, "TCN" => null, "CRTNO" => null, "RefCode1" => $RefCode1, "RefCode2" => $RefCode2, "Employee_Number" => $data['customer_data']['customer_id'],"enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "0", "PaymentMode" => "", "PolicyproductComponents" => [["PlanCode" => $plan_code, "SumInsured" => $SumInsured, "SchemeCode" => $SchemeCode]]], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "", "modeOfEntry" => "", "cdAcNo" => null, "expiryDate" => null, "payerType" => "", "payerCode" => null, "paymentBy" => "", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => "", "collectionRcvdDate" => null, "collectionMode" => "", "remarks" => null, "instrumentNumber" => null, "instrumentDate" => null, "bankName" => null, "branchName" => null, "bankLocation" => null, "micrNo" => null, "chequeType" => null, "ifscCode" => "", "PaymentGatewayName" => "", "TerminalID" => "", "CardNo" => null]];
			
		}else{
			//group policies
			$fqrequest = ["ClientCreation" => ["Member_Customer_ID" => $Member_Customer_ID, "salutation" => $data['customer_data']['salutation'], "firstName" => $explode_name[0], "middleName" => "", "lastName" => !empty(trim($explode_name[1]))?$explode_name[1]:'.', "dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['dob'])) , "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F", "educationalQualification" => null, "pinCode" => $data['customer_data']['pincode'], "uidNo" => $uidNo, "maritalStatus" => null, "nationality" => "Indian", "occupation" => $occupation, "primaryEmailID" => $data['customer_data']['email_id'], "contactMobileNo" => substr(trim($data['customer_data']['mobile_no']), -10), "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => null, "remarks" => null, "startDate" => date('Y-m-d') , "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['address_line1'], "homeAddressLine2" => null, "homeAddressLine3" => null, "homePinCode" => $data['customer_data']['pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null, "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null], "PolicyCreationRequest" => ["Quotation_Number" => "", "MasterPolicyNumber" => $MasterPolicyNumber, "GroupID" => $GroupID, "Product_Code" => $Product_Code,"SumInsured_Type"=> $SumInsured_Type,"Policy_Tanure"=> $Policy_Tanure,"Member_Type_Code"=> $Member_Type_Code, "intermediaryCode" => $intermediaryCode, "AutoRenewal" => $AutoRenewal, "intermediaryBranchCode" => $intermediaryBranchCode, "agentSignatureDate" => null, "Customer_Signature_Date" => null, "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $leadID, "Source_Name" => $data['proposal_data']['SourceSystemName_api'], "SPID" => $SPID, "TCN" => null, "CRTNO" => null, "RefCode1" => $RefCode1, "RefCode2" => $RefCode2, "Employee_Number" => $data['customer_data']['customer_id'],"enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "0", "PaymentMode" => null, "PolicyproductComponents" => ["PlanCode" => $plan_code, "SumInsured" => $SumInsured, "SchemeCode" => $SchemeCode] ], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "", "modeOfEntry" => "", "cdAcNo" => null, "expiryDate" => null, "payerType" => "", "payerCode" => null, "paymentBy" => "", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => "", "collectionRcvdDate" => null, "collectionMode" => "", "remarks" => null, "instrumentNumber" => null, "instrumentDate" => null, "bankName" => null, "branchName" => null, "bankLocation" => null, "micrNo" => null, "chequeType" => null, "ifscCode" => "", "PaymentGatewayName" => "", "TerminalID" => "", "CardNo" => null]];
		}
		
		//echo "<pre>";print_r($fqrequest);exit;

		$request_arr = ["lead_id" => $leadID, "req" => json_encode($fqrequest) ,"product_id" => $product_id, "type"=>"quick_quote_request", "proposal_policy_id"=> $proposal_policy_id];
	
		$this->db->insert("logs_docs",$request_arr);
		$insert_id = $this->db->insert_id();

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($fqrequest) ,
			CURLOPT_HTTPHEADER => array(
				"Accept: */*",
				"Cache-Control: no-cache",
				"Connection: keep-alive",
				"Content-Length: " . strlen(json_encode($fqrequest)) ,
				"Content-Type: application/json",
				"Host: bizpre.adityabirlahealth.com"
			) ,
		));


		$response = curl_exec($curl);
		
		$err = curl_error($curl);
		if($response == '' || $response == NULL){
			$response = $err;
		}
		
		//echo "<pre>";print_r($response);exit;
		
		$request_arr = ["res" => json_encode($response)];
		$this->db->where("id",$insert_id);
		$this->db->update("logs_docs",$request_arr);

		curl_close($curl);

		if ($err){
			return array(
				"status" => "error",
				"msg" => $err
			);
			if($count <= 3){
				sleep(15);
				$this->get_quote_data($lead_id, $emp_id, $master_policy_id, $proposal_policy_id, $proposal_details, $policy_sub_type_id, $sum_insured,$count++);
			}
		}else{
			
			$new = simplexml_load_string($response);
			$con = json_encode($new);
			$newArr = json_decode($con, true);
			$errorObj = $newArr['errorObj'];
			//print_pre($errorObj);exit;
			if($errorObj['ErrorNumber'] == '00'){
			  $policydetail = $newArr['policyDtls'];
			  
			  $arr = ["emp_id" => $emp_id, "lead_id" => $lead_id,"proposal_policy_id" => $proposal_policy_id,"policy_subtype_id" =>$policy_sub_type_id, "QuotationNumber" => $policydetail['QuotationNumber'], "PolicyNumber" => $policydetail['PolicyNumber'],"status"=>"success"];
			  
			  $query = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' AND proposal_policy_id='$proposal_policy_id'")->row_array();
				
				if($query > 0){
					$this->db->where("emp_id",$emp_id);
					$this->db->where("lead_id",$lead_id);
					$this->db->where("proposal_policy_id",$proposal_policy_id);
					
					$this->db->update("ghi_quick_quote_response",$arr);
				}else{
					$this->db->insert("ghi_quick_quote_response", $arr);
				}
			 
			  
				return array(
					"status" => "Success",
					"msg" => $policydetail['QuotationNumber']
				);
				
			}else{
			  
				$query = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' AND proposal_policy_id='$proposal_policy_id'")->row_array();
				if($query > 0){
					$arr = ["count" => $query['count'] + 1,"status"=>"error"];
					$this->db->where("emp_id",$emp_id);
					$this->db->where("lead_id",$lead_id);
					$this->db->where("proposal_policy_id",$proposal_policy_id);
					$this->db->update("ghi_quick_quote_response",$arr);
				}else{
					$arr = ["emp_id" => $emp_id,"status"=>"error"];
					$this->db->insert("ghi_quick_quote_response", $arr);
				}
			  
				return array(
					"status" => "error",
					"msg" => $errorObj['ErrorMessage']
				);
				if($count <= 3){
					sleep(15);
					$this->get_quote_data($lead_id, $emp_id,$master_policy_id, $proposal_policy_id, $proposal_details, $policy_sub_type_id, $sum_insured,$count++);
				}
			}
		}
	}
	
	
	//Full Quote Request
	public function get_full_quote_data($lead_id, $emp_id,$master_policy_id, $proposal_policy_id, $proposal_details, $policy_sub_type_id, $sum_insured){ 

		//get proposal policy details
		$data = array();
		$data['customer_data'] = (array)$this->get_profile($emp_id);
		$data['member_data'] = (array)$this->get_all_member_data($proposal_policy_id);
		$nominees = array();
		if(!empty($proposal_details)){
			$nominees['nominee_relation'] = $proposal_details[0]['nominee_relation'];
			$nominees['nominee_first_name'] = $proposal_details[0]['nominee_first_name'];
			$nominees['nominee_last_name'] = $proposal_details[0]['nominee_last_name'];
			$nominees['nominee_salutation'] = $proposal_details[0]['nominee_salutation'];
			$nominees['nominee_gender'] = $proposal_details[0]['nominee_gender'];
			$nominees['nominee_dob'] = $proposal_details[0]['nominee_dob'];
			$nominees['nominee_contact'] = $proposal_details[0]['nominee_contact'];
			$nominees['nominee_email'] = $proposal_details[0]['nominee_email'];
		}
		$data['nominee_data'] = $nominees;
		$data['proposal_data'] = $proposal_details;
		// print_pre($data);exit;
		//get Api URL with policy_sub_type_id
		$getApiUrl = $this->db->query("select api_url from master_policy_sub_type where policy_sub_type_id='".$policy_sub_type_id."' ")->row_array();
		
		$url = trim($getApiUrl['api_url']);
		
		if($url == ''){
			return array(
				"status" => "error",
				"msg" => "Something Went Wrong"
			);
			exit;
		}
		
		//Get Policy Details
		$policyDetails = $this->db->query("select * from proposal_policy where proposal_policy_id='".$proposal_policy_id."' ")->row_array();
		
		$explode_name = explode(" ", trim($data['customer_data']['customer_name']),2);
		
		//Data Variables
		$uidNo = (!empty($data['customer_data']['adhar'])) ? $data['customer_data']['adhar'] : null;
		$source_name = "ABC_GFB";		
		$MasterPolicyNumber = '61-20-00040-00-00';
		$GroupID = 'GRP001';
		$plan_code = '4211';
		$Product_Code = '4211';
		$micrNo = null;
		
		$Member_Type_Code = "M209";
		$intermediaryCode = '2108233';
		$intermediaryBranchCode = '10MHMUM01';
		$SchemeCode = '4112000003';
		
		//get SumInsured Type
		$sumins_type = $this->db->query("select mi.suminsured_type from master_policy_si_type_mapping as m left join master_suminsured_type as mi ON m.suminsured_type_id = mi.suminsured_type_id where m.master_policy_id='$master_policy_id' ")->row();
		
		//echo "<pre>";print_r($sumins_type);exit;
		$SumInsured_Type = $sumins_type->suminsured_type;
		//echo $SumInsured_Type;exit;
		
		$leadID = $lead_id;
		$txnRefNumber = "pay_FrAaDQjzQFtQWG";
		
		$PaymentMode = "online";
		$bankName = 0;
		$branchName = null;
		$bankLocation = null;
		$chequeType = null;
		$ifscCode = null;
		$terminal_id = "EuxJCz8cZV9V63";
		
		$SumInsured = $sum_insured;
		$product_id = $data['proposal_data'][0]['plan_id'];
		$tax_amount = $policyDetails['tax_amount'];
		
		$collectionAmount = ( $policyDetails['premium_amount'] + $tax_amount );
		
		$transaction_date = explode(" ",$data['proposal_data'][0]['transaction_date']);
		$trans_date = date("Y-m-d", strtotime($transaction_date[0])); 
		//online,RTGS/ NEFT,Cheque
		$collectionMode = (!empty($data['proposal_data'][0]['mode_of_payment'])) ? $data['proposal_data'][0]['mode_of_payment'] : "Cheque";
		
		$SPID = 0;
		$RefCode1 = 0;
		$RefCode2 = 0;
		$Policy_Tanure = 1;
		$AutoRenewal = 'Y';
	
	
		$query_quote = [];
	
		if($policy_sub_type_id == 1){
			$data['customer_data']['customer_id'] = $data['customer_data']['customer_id']."GHI";
			
			$query_quote = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and lead_id='$lead_id' and proposal_policy_id='$proposal_policy_id' and status = 'success'")->row_array();
			
			/*if(empty($query_quote)){
				$quote_data = $this->get_quote_data($emp_id, $policy_no);
				if($quote_data['status'] == 'Success')
				{
					$query_quote['QuotationNumber'] = $quote_data['msg'];
				}
			}*/
			
		}else{
			
			
			$query_quote = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and lead_id='$lead_id' and proposal_policy_id='$proposal_policy_id' and status = 'success' ")->row_array();
			
			/*if(empty($query_quote)){
				$quote_data = $this->get_quote_data($emp_id, $policy_no, $mem_data);
				if($quote_data['status'] == 'Success')
				{
					$query_quote['QuotationNumber'] = $quote_data['msg'];
				}
			}*/
			
			if($policy_sub_type_id == 2){
				$concat_string = "GPA";
			}elseif($policy_sub_type_id == 3){
				$concat_string = "GCI";
			}elseif($policy_sub_type_id == 7){
				$concat_string = "GP";
			}
			
			$data['customer_data']['customer_id'] = $data['customer_data']['cust_id'].$concat_string;
			
				
		}
	
		if($query_quote['QuotationNumber']){
		   
			$totalMembers = count($data['member_data']);
			$member = [];
				
			$explode_name = array($data['customer_data']['first_name'],$data['customer_data']['middle_name'],$data['customer_data']['last_name']);
			$explode_name_nominee = array($data['nominee_data']['nominee_first_name'],$data['nominee_data']['nominee_last_name']);
			
			for ($i = 0;$i < $totalMembers; $i++){
					
				//Checking relation based on self, spouse, son and daughter
				if($data['member_data'][$i]['relation_with_proposal'] == 1 ){
					$data['member_data'][$i]['relation_code'] = "R001";
				}else if($data['member_data'][$i]['relation_with_proposal'] == 2 ){
					$data['member_data'][$i]['relation_code'] = "R002";
				}else if($data['member_data'][$i]['relation_with_proposal'] == 3 ){
					$data['member_data'][$i]['relation_code'] = "R003";
				}else{
					$data['member_data'][$i]['relation_code'] = "R004";
				}
				
				//Nominee relation code
				if($data['nominee_data']['nominee_relation'] == 1 ){
					$data['nominee_data']['relation_code'] = "R001";
				}else if($data['nominee_data']['nominee_relation'] == 2 ){
					$data['nominee_data']['relation_code'] = "R002";
				}else if($data['nominee_data']['nominee_relation'] == 3 ){
					$data['nominee_data']['relation_code'] = "R003";
				}else{
					$data['nominee_data']['relation_code'] = "R004";
				}
			
				
				$abc = ["PEDCode" => null, "Remarks" => null];
			
				$member[] = ["MemberNo" => $i + 1, "Salutation" => $data['member_data'][$i]['policy_member_salutation'] , "First_Name" => $data['member_data'][$i]['policy_member_first_name'], "Middle_Name" => null, "Last_Name" => !empty($data['member_data'][$i]['policy_member_last_name']) ? $data['member_data'][$i]['policy_member_last_name'] : '.', "Gender" => ($data['member_data'][$i]['policy_member_gender'] == "Male") ? "M" : "F", "DateOfBirth" => date('m/d/Y', strtotime($data['member_data'][$i]['policy_member_dob'])) , "Relation_Code" => trim($data['member_data'][$i]['relation_code']), "Marital_Status" => null, "height" => "0.00", "weight" => "0", "occupation" => "O553", "PrimaryMember" => (($i == 0) ? "Y" : "N") , "MemberproductComponents" => [["PlanCode" => $plan_code, "MemberQuestionDetails" => [["QuestionCode" => null, "Answer" => null, "Remarks" => null]]]], "MemberPED" => $abc, "exactDiagnosis" => null, "dateOfDiagnosis" => null, "lastDateConsultation" => null, "detailsOfTreatmentGiven" => null, "doctorName" => null, "hospitalName" => null, "phoneNumberHosital" => null, "Nominee_First_Name" => $explode_name_nominee[0], "Nominee_Last_Name" => !empty($explode_name_nominee[1]) ? $explode_name_nominee[1] : '.', "Nominee_Contact_Number" => $data['nominee_data']['nominee_contact'], "Nominee_Home_Address" => null, "Nominee_Relationship_Code" => trim($data['nominee_data']['relation_code']), ];

			}
			
			$data['proposal_data']['SourceSystemName_api'] = 'CreditorPortal';	


			$fqrequest = ["ClientCreation" => ["Member_Customer_ID" => $data['customer_data']['customer_id'], "salutation" => $data['customer_data']['salutation'],"firstName" => $data['customer_data']['first_name'], "middleName" => "", "lastName" => !empty($data['customer_data']['last_name']) ? $data['customer_data']['last_name'] : '.',"dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['dob'])) , "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F","educationalQualification" => null, "pinCode" => $data['customer_data']['pincode'], "uidNo" => $uidNo,"maritalStatus" => null, "nationality" => "Indian", "occupation" => "O553", "primaryEmailID" => $data['customer_data']['email_id'],"contactMobileNo" => substr(trim($data['customer_data']['mobile_no']), -10), "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null,"annualIncome" => null, "remarks" => null, "startDate" => date('Y-m-d') , "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null,"ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['address_line1'], "homeAddressLine2" => null, "homeAddressLine3" => null,"homePinCode" => $data['customer_data']['pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null,"homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null,"mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null,"mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null,"ifscCode" => $ifscCode, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null,"EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null],"PolicyCreationRequest" => ["Quotation_Number" => $query_quote['QuotationNumber'], "MasterPolicyNumber" => $MasterPolicyNumber,"GroupID" => $GroupID, "Product_Code" => $Product_Code,"SumInsured_Type"=> $SumInsured_Type,"Policy_Tanure"=> $Policy_Tanure,"Member_Type_Code"=> $Member_Type_Code, "intermediaryCode" => $intermediaryCode,"AutoRenewal" => $AutoRenewal, "intermediaryBranchCode" => $intermediaryBranchCode, "agentSignatureDate" => null,"Customer_Signature_Date" => null,"businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $leadID, "Source_Name" => $source_name, "SPID" => $SPID, "TCN" => null, "CRTNO" => null, "RefCode1" => $RefCode1,"RefCode2" => $RefCode2, "Employee_Number" => $data['customer_data']['customer_id'],"enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "1", "PaymentMode" => $collectionMode,"PolicyproductComponents" => [["PlanCode" => $plan_code, "SumInsured" => $SumInsured, "SchemeCode" => $SchemeCode]]], "MemObj" => ["Member" => $member],"ReceiptCreation" => ["officeLocation" => "Mumbai", "modeOfEntry" => "Direct", "cdAcNo" => null, "expiryDate" => null, "payerType" => "Customer", "payerCode" => null,"paymentBy" => "Customer", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => round($collectionAmount,2),"collectionRcvdDate" => $trans_date,"collectionMode" => $collectionMode, "remarks" => null, "instrumentNumber" => $txnRefNumber,"instrumentDate" => $trans_date, "bankName" => $bankName, "branchName" => $branchName,"bankLocation" => $bankLocation, "micrNo" => $micrNo, "chequeType" => $chequeType, "ifscCode" => $ifscCode, "PaymentGatewayName" => $source_name, "TerminalID" => $terminal_id,"CardNo" => null]];
		
			//print_pre($fqrequest);exit;
			//Monolog::saveLog("full_quote_request2", "I", json_encode($fqrequest));

			$request_arr = ["lead_id" => $leadID, "req" => json_encode($fqrequest) ,"product_id" => $product_id, "type"=>"full_quote_request", "proposal_policy_id"=> $proposal_policy_id];
		
			$this->db->insert("logs_docs",$request_arr);
			$insert_id = $this->db->insert_id();

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 90,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => json_encode($fqrequest) ,
				CURLOPT_HTTPHEADER => array(
					"Accept: */*",
					"Cache-Control: no-cache",
					"Connection: keep-alive",
					"Content-Length: " . strlen(json_encode($fqrequest)) ,
					"Content-Type: application/json",
					"Host: bizpre.adityabirlahealth.com"
				) ,
			));

			$response = curl_exec($curl);

			//Monolog::saveLog("full_quote_reponse2", "I", json_encode($response));

			$request_arr = ["res" => json_encode($response)];
			$this->db->where("id",$insert_id);
			$this->db->update("logs_docs",$request_arr);

			$err = curl_error($curl);
			//echo "<pre>";print_r($response);exit;
			curl_close($curl);

			if ($err){
				return array(
					"status" => "error",
					"msg" => $err
				);
			}else{
				$new = simplexml_load_string($response);
				$con = json_encode($new);
				$newArr = json_decode($con, true);
				
				$errorObj = $newArr['errorObj'];
				$premium = $newArr['premium'];
				$return_data = [];

				if($errorObj['ErrorNumber']=='00' || ($errorObj['ErrorNumber']=='302' && $master_policy_id == $newArr['policyDtls']['PolicyNumber'])){
					
					$return_data['status'] = 'Success';
					$return_data['msg'] = $errorObj['ErrorMessage'];

					$api_insert = array(
						"emp_id" => $emp_id,
						"client_id" => $newArr['policyDtls']['ClientID'],
						"certificate_number" => $newArr['policyDtls']['CertificateNumber'],
						"quotation_no" => $newArr['policyDtls']['QuotationNumber'],
						"proposal_no" => $newArr['policyDtls']['ProposalNumber'],
						"policy_no" => $newArr['policyDtls']['PolicyNumber'],
						"gross_premium" => empty($premium['GrossPremium'])?'':$premium['GrossPremium'],
						"status" => "Success",
						"start_date" => $newArr['policyDtls']['startDate'],
						"end_date" => $newArr['policyDtls']['EndDate'],
						"created_date" => date('Y-m-d H:i:s'),
						"proposal_no_lead"=>$proposal_policy_id,
						"PolicyStatus" => $newArr['policyDtls']['PolicyStatus'],
						"letter_url" => $newArr['policyDtls']['LetterURL'],
						"CustomerID" => $newArr['policyDtls']['MemberCustomerID'],
						"MemberCustomerID" => $newArr['policyDtls']['MemberCustomerID'],
						//"COI_url" => $newArr['policyDtls']['COIUrl'],

					);
					
					$this->db->insert('api_proposal_response', $api_insert);
					
					//Monolog::saveLog("api_insert", "I", json_encode($api_insert));	
					
					//HB emandate call
					$this->emandate_HB_call($emp_id,$data['proposal_data'][0]['proposal_details_id']);

					$request_arr = ["lead_id" => $leadID, "req" => json_encode($api_insert) ,"product_id"=> $product_id, "type"=>"api_insert"];
					$this->db->insert("logs_docs",$request_arr);
					$this->Logs_m->insertLogs($dataArray);
					
					

				}else{

					$return_data = array(
						'status'=>'error',
						"msg" => $errorObj['ErrorMessage']
					);
				}
				
				return $return_data;
				
			}

		}else{
			
			return $return_data = array(
				'status'=>'error',
				"msg" => "Quote error"
			);
		}

	}
	
	// Emadate HB Call
	
	function emandate_HB_call($emp_id,$proposal_details_id)
	{ 	
		$query_check = $this->db->query("select ed.lead_id,ed.plan_id,ed.json_qote,apr.certificate_number,apr.proposal_no,apr.pr_api_id from proposal_details as ed,proposal_policy as p,api_proposal_response as apr,emandate_data as emd where ed.proposal_details_id = p.proposal_details_id and p.proposal_policy_id = apr.proposal_no_lead and p.status in('Success','Payment Received') and apr.mandate_send_status = 0 and emd.lead_id = ed.lead_id and emd.status = 'Success' and ed.proposal_details_id = '$proposal_details_id' group by p.proposal_details_id")->result_array();

		if($query_check){ 
		
		foreach ($query_check as $val)
		{
			
			//BIZ HB call start
			$json_data = json_decode($val['json_qote'],true);

			$url = 'https://bizpre.adityabirlahealth.com/ABHICL_HealthCoreXML/Service1.svc/AddEmendateDetails';
			
			$req_arr = [
					  'EmendateDeatails' => 
					  [
						'EmendateList' => 
						[ 
						  [
							'Bank_Name' => ($json_data['AXISBANKACCOUNT'] == 'Y')?'Axis Bank':'Other',
							'Debit_Account_Number' => $json_data['ACCOUNTNUMBER'],
							'Mandate_Start_Date' => '10-02-2020',
							'Mandate_End_Date' => '11-09-2020',
							'Account_Type' => 'Saving',
							'Bank_Branch_Name' => $json_data['BRANCH_NAME'],
							'MICR' => '123',
							'IFSC' => $json_data['IFSCCODE'],
							'Frequency' => "Annual",
							'Policy_Number' => $val['certificate_number'],
						  ],
						],
					  ],
					];
					
				   $curl = curl_init();

					curl_setopt_array($curl, array(
						CURLOPT_URL => $url,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => "",
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 60,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => "POST",
						CURLOPT_POSTFIELDS => json_encode($req_arr) ,
						CURLOPT_HTTPHEADER => array(
							"Accept: */*",
							"Cache-Control: no-cache",
							"Connection: keep-alive",
							"Content-Length: " . strlen(json_encode($req_arr)) ,
							"Content-Type: application/json",
							"Host: bizpre.adityabirlahealth.com"
						) ,
					));

				   $response = curl_exec($curl);
				   
				   curl_close($curl);
				   
				   // if($response){
					   // $response = json_decode($response);
				   // }
				   
				   $update_arr = ["mandate_send_status" => 1];
						
					$this->db->where("pr_api_id",$val['pr_api_id']);
					$this->db->update("api_proposal_response",$update_arr);
				   
				   $request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($req_arr),"res" => json_encode($response),"product_id"=> "ABC", "type"=>"emandate_HB_post"];
				
					$this->db->insert('logs_docs',$request_arr);
					//BIZ HB call end
					
		}
		
		}
	}
	
	
	//Get Customer data
	function get_profile($emp_id){
		return $this->db->query("select e.* from master_customer as e where e.customer_id='$emp_id'")->row();
    }
	
	//Get all member data
	function get_all_member_data($proposal_policy_id)
    {
        
		$response = $this->db->query('select * from proposal_policy_member where policy_id='.$proposal_policy_id.' ')->result_array();
		//echo $this->db->last_query();
        return $response;
    }
	
	//Get Policy nominee
	function get_all_nominee($emp_id)
    {
        $response = $this->db->select('*,mfr.relation_code,mfr.nominee_type as fr_name')
        ->from('member_policy_nominee AS mpn,master_nominee as mfr')
        ->where('mpn.emp_id', $emp_id)
        ->where('mpn.fr_id = mfr.nominee_id')
        ->get()->row_array();
        if ($response) {
            return $response;
        }
    }
	
	//Get assignment declarations
	function assignmentDeclarationListing($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "assignment_declaration";
		$table_id = 'assignment_declaration_id';
		$default_sort_column = 'assignment_declaration_id';
		$default_sort_order = 'desc';
		
		$condition = "1=1";
		
		
		$colArray = array('p.plan_name','c.creaditor_name','i.label','i.isactive');
		$sortArray = array('p.plan_name','c.creaditor_name','i.label','i.isactive');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		//echo $post['iSortCol_0'];exit;
		//$s = $post['iSortCol_0'];
		$sort = isset($post['iSortCol_0']) ? strval($post['iSortCol_0']) : $default_sort_column; 
		//echo $sort;exit;		
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<8;$i++)
		{
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
			
			
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.assignment_declaration_id, i.plan_id, i.creditor_id, i.label, i.is_active, p.plan_name, c.creaditor_name');
		$this -> db -> from('assignment_declaration as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.assignment_declaration_id, i.plan_id, i.creditor_id, i.label, i.is_active, p.plan_name, c.creaditor_name');
		$this -> db -> from('assignment_declaration as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0);
		}
	}
	
	//Get assignment declarations
	function getAssignmentDeclarationDetails($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('assignment_declaration as i');
		$this -> db -> where('i.assignment_declaration_id', $ID);
	
		$query = $this -> db -> get();
	   
		//print_r($this->db->last_query());
		//exit;
	   
		if($query -> num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}	
	}
	
	//Get ghd declarations
	function ghdDeclarationListing($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "ghd_declaration";
		$table_id = 'declaration_id';
		$default_sort_column = 'declaration_id';
		$default_sort_order = 'desc';
		
		$condition = "1=1";
		
		
		$colArray = array('p.plan_name','c.creaditor_name','i.label','i.isactive');
		$sortArray = array('p.plan_name','c.creaditor_name','i.label','i.isactive');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		//echo $post['iSortCol_0'];exit;
		//$s = $post['iSortCol_0'];
		$sort = isset($post['iSortCol_0']) ? strval($post['iSortCol_0']) : $default_sort_column; 
		//echo $sort;exit;		
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<8;$i++)
		{
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
			
			
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.declaration_id, i.plan_id, i.creditor_id, i.label, i.is_active, p.plan_name, c.creaditor_name');
		$this -> db -> from('ghd_declaration as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.declaration_id, i.plan_id, i.creditor_id, i.label, i.is_active, p.plan_name, c.creaditor_name');
		$this -> db -> from('ghd_declaration as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0);
		}
	}
	
	//Get ghd declarations
	function getGHDDeclarationDetails($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('ghd_declaration as i');
		$this -> db -> where('i.declaration_id', $ID);
	
		$query = $this -> db -> get();
	   
		//print_r($this->db->last_query());
		//exit;
	   
		if($query -> num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}	
	}
	
}
?>
