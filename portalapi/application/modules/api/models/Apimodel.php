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
		$this -> db -> select('*');
		$this -> db -> from('tbl_admin');
		$this -> db -> where("($condition)");
		
		$query = $this -> db -> get();
		// print_r($this->db->last_query());
		// exit;
	 
		if($query -> num_rows() >= 1)
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
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
	
	
	function getEmployeeList($post)
	{
		$table = "tbl_employee";
		$table_id = 'emp_id ';
		$default_sort_column = 'emp_id ';
		$default_sort_order = 'desc';
		$condition = "i.department_id=d.id AND i.isactive=1";
		
		$colArray = array('i.employee_name');
		$sortArray = array('i.employee_name','i.department_id');
		
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
		$this->db-> select('*');
		$this->db->from('tbl_employee as i,tbl_department_master as d');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this->db->get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this->db-> select('*');
		$this->db->from('tbl_employee as i,tbl_department_master as d');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this->db->get();
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
	
	

	


	
	function saleAdminDashBorad($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "proposal_payment_details";
		$table_id = 'proposal_payment_id';
		$default_sort_column = 'premiumsum';
		$default_sort_order = 'desc';
		$condition = "1=1";
		
		$colArray = array('c.creaditor_name','s.employee_full_name');
		$sortArray = array('premiumsum','c.creaditor_name','s.employee_full_name','premiumsum','premiumsum','premiumsum');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = 200;	// iDisplayLength no of records from the offset
		
		// sort order by column
		//echo $post['iSortCol_0'];exit;
		$sort = isset($post['iSortCol_0']) ? strval($post['iSortCol_0']) : $default_sort_column; 
		//echo $sort;exit;		
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<12;$i++)
		{
			/*if($i==6)
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
			}*/
			
			if($i == 0 && isset($post['sSearch_0']) && !empty($post['sSearch_0'])){
				$condition .= " AND i.creditor_id = '".$_POST['sSearch_0']."'";
			}
			
			if($i == 1 && isset($post['sSearch_1']) && !empty($post['sSearch_1'])){
				$condition .= " AND i.created_by = '".$_POST['sSearch_1']."'";
			}
			
			if($i == 2 && isset($post['sSearch_2']) && !empty($post['sSearch_2'])){
				$condition .= " AND l.lead_location_id = '".$_POST['sSearch_2']."'";
			}
			
			if($i == 3 && isset($_POST['sSearch_3']) && !empty($_POST['sSearch_3'])){
				if($_POST['sSearch_3'] == 'Pending'){
					$condition .= " AND (l.status != 'Approved' && l.status != 'Rejected' ) ";
				}else{
					$condition .= " AND l.status = '".$_POST['sSearch_3']."'";
				}
				
			}
			
			if($i == 4 && isset($post['sSearch_4']) && !empty($post['sSearch_4'])){
				$condition .= "  AND i.created_at >= '".date("Y-m-d",strtotime($post['sSearch_4']))." 00:00:01' ";
			}
			
			if($i == 5 && isset($post['sSearch_5']) && !empty($post['sSearch_5'])){
				$condition .= "  AND i.created_at <= '".date("Y-m-d",strtotime($post['sSearch_5']))." 23:59:59' ";
			}
			
			
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.creditor_id,i.lead_id,i.trace_id,i.lan_id,i.sum_insured,i.created_by, c.creaditor_name, s.employee_full_name, SUM(i.premium) as premiumsum, SUM(i.premium_with_tax) as premiumwithtaxsum');
		$this -> db -> from('proposal_payment_details as i');
		$this -> db -> join('master_employee as s', 'i.created_by  = s.employee_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> join('lead_details as l', 'i.lead_id  = l.lead_id', 'left');
		$this->db->where("($condition)");
		$this->db->group_by(array("i.creditor_id", "i.created_by"));
		//$this->db->order_by('premiumwithtaxsum', 'desc');
		$this->db->order_by('premiumwithtaxsum', $_POST['sSearch_6']);

		//$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.creditor_id,i.lead_id,i.trace_id,i.lan_id,i.sum_insured,i.created_by, c.creaditor_name, s.employee_full_name, SUM(i.premium) as premiumsum, SUM(i.premium_with_tax) as premiumwithtaxsum');
		$this -> db -> from('proposal_payment_details as i');
		$this -> db -> join('master_employee as s', 'i.created_by  = s.employee_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> join('lead_details as l', 'i.lead_id  = l.lead_id', 'left');
		$this->db->where("($condition)");
		$this->db->group_by(array("i.creditor_id", "i.created_by"));
		$this->db->order_by('premiumwithtaxsum', $_POST['sSearch_6']);
		
		$query1 = $this -> db -> get();

		//print_r($this->db->last_query());
		//exit;

		//echo "total: ".$query1 -> num_rows();
		//exit;
		//echo $condition;exit;
		//echo "<pre>mmm";print_r($query1->result());exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			$totcount_val = $totcount;
			$final_result_arr = array();
			$i = 0;
			$tot_premium = 0;
			$tot_premium_withtax = 0;
			$current_month = date("m");
			if($current_month >= 4){
				$cond =  "( YEAR(created_at) BETWEEN '".date("Y")."' AND '".(date("Y") + 1)."' )";
			}else{
				$cond =  "( YEAR(created_at) BETWEEN '".(date("Y") - 1)."' AND '".date("Y")."' )";
			}
			foreach($query->result() as $row){
				//Get Yearly Total    
				//$yearly_query = $this->db->query("Select SUM(premium) as total from proposal_payment_details where creditor_id='".$row->creditor_id."' and created_by='".$row->created_by."' and created_by='".$row->created_by."' and YEAR(created_at) = YEAR(NOW()) ");
				//echo $row->created_by;exit;
				$yearly_query = $this->db->query("Select SUM(premium) as total, SUM(premium_with_tax) as totalwithtax from proposal_payment_details where creditor_id='".$row->creditor_id."' and created_by='".$row->created_by."' and $cond ");
				$yearly_result = $yearly_query->row();
				//echo $yearly_result->totalwithtax;exit;
				
				//Get monthly Total    
				$monthly_query = $this->db->query("Select SUM(premium) as total, SUM(premium_with_tax) as totalwithtax from proposal_payment_details where creditor_id='".$row->creditor_id."' and created_by='".$row->created_by."' and YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at)=MONTH(NOW()) ");
				$monthly_result = $monthly_query->row();
				//echo $monthly_result->total;exit;
				
				//Get weekly Total    
				$weekly_query = $this->db->query("Select SUM(premium) as total, SUM(premium_with_tax) as totalwithtax from proposal_payment_details where creditor_id='".$row->creditor_id."' and created_by='".$row->created_by."' and YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at)=MONTH(NOW()) AND WEEKOFYEAR(created_at) = WEEKOFYEAR(NOW()) ");
				$weekly_result = $weekly_query->row();
				//echo $weekly_result->total;exit;
				
				
				if(!empty($post['sSearch_4']) && !empty($post['sSearch_5'])){
					$row->range_total = round($row->premiumsum, 2);
					$row->range_total_withtax = round($row->premiumwithtaxsum, 2);
					$row->date_from = date("d-m-Y", strtotime($post['sSearch_4']));
					$row->date_to = date("d-m-Y", strtotime($post['sSearch_5']));
				}else{
					$row->range_total = 0;
					$row->range_total_withtax = 0;
					$row->date_from = "-";
					$row->date_to = "-";
				}
				
				$row->yearly_tot = ($yearly_result->total > 0) ? round($yearly_result->total, 2) : 0;
				$row->monthly_tot = ($monthly_result->total > 0) ? round($monthly_result->total, 2) : 0;
				$row->weekly_tot = ($weekly_result->total > 0) ? round($weekly_result->total, 2) : 0;

				$row->yearly_tot_withtax = ($yearly_result->totalwithtax > 0) ? round($yearly_result->totalwithtax, 2) : 0;
				$row->monthly_tot_withtax = ($monthly_result->totalwithtax > 0) ? round($monthly_result->totalwithtax, 2) : 0;
				$row->weekly_tot_withtax = ($weekly_result->totalwithtax > 0) ? round($weekly_result->totalwithtax, 2) : 0;
				
				if($_POST['sSearch_6'] == 'desc'){
					$row->rank = ++$i;
				}else{
					$row->rank = $totcount_val--;
				}
				
				$final_result_arr[] = $row;
			}
			

			$tot_premium = $tot_premium_withtax = $weeklyNetTot = $weeklyGrossTot = $mothlyNetTot = $mothlyGrossTot = $yearlyNetTot = $yearlyGrossTot = $dateRangeNetTot = $dateRangeGrossTot = 0;
			
			foreach($query1->result() as $row1){
				$tot_premium += $row1->premiumsum;
				$tot_premium_withtax += $row1->premiumwithtaxsum;

				//yearly total
				$yearly_query = $this->db->query("Select SUM(premium) as total, SUM(premium_with_tax) as totalwithtax from proposal_payment_details where creditor_id='".$row1->creditor_id."' and created_by='".$row1->created_by."' and $cond ");
				$yearly_result = $yearly_query->row();

				//echo $row1->creditor_id."<br/>";

				//Get monthly Total    
				$monthly_query = $this->db->query("Select SUM(premium) as total, SUM(premium_with_tax) as totalwithtax from proposal_payment_details where creditor_id='".$row1->creditor_id."' and created_by='".$row1->created_by."' and YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at)=MONTH(NOW()) ");
				$monthly_result = $monthly_query->row();

				//Get weekly Total    
				$weekly_query = $this->db->query("Select SUM(premium) as total, SUM(premium_with_tax) as totalwithtax from proposal_payment_details where creditor_id='".$row1->creditor_id."' and created_by='".$row1->created_by."' and YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at)=MONTH(NOW()) AND WEEKOFYEAR(created_at) = WEEKOFYEAR(NOW()) ");
				$weekly_result = $weekly_query->row();

				$yearlyNetTot += $yearly_result->total;
				$mothlyNetTot += $monthly_result->total;
				$weeklyNetTot += $weekly_result->total;

				$yearlyGrossTot += $yearly_result->totalwithtax;
				$mothlyGrossTot += $monthly_result->totalwithtax;
				$weeklyGrossTot += $weekly_result->totalwithtax;

				if(!empty($post['sSearch_4']) && !empty($post['sSearch_5'])){
					$dateRangeNetTot += $row1->premiumsum;
					$dateRangeGrossTot += $row1->premiumwithtaxsum;
				}


			}

			//echo "yearlyNetTot: ".$yearlyNetTot."mothlyNetTot".$mothlyNetTot."weeklyNetTot".$weeklyNetTot."yearlyGrossTot".$yearlyGrossTot."mothlyGrossTot".$mothlyGrossTot."weeklyGrossTot".$weeklyGrossTot."dateRangeNetTot".$dateRangeNetTot."dateRangeGrossTot".$dateRangeGrossTot;
			//exit;
			
			//echo $tot_premium;exit;
			return array("query_result" => $final_result_arr, "totalRecords" => $totcount, "tot_premium" => round($tot_premium,2), "tot_premium_withtax" => round($tot_premium_withtax,2), "yearlyNetTot" => round($yearlyNetTot,2), "yearlyGrossTot"=>round($yearlyGrossTot,2), "mothlyNetTot"=>round($mothlyNetTot,2), "mothlyGrossTot"=>round($mothlyGrossTot,2), "weeklyNetTot"=>round($weeklyNetTot,2), "weeklyGrossTot"=>round($weeklyGrossTot,2), "dateRangeNetTot"=>$dateRangeNetTot, "dateRangeGrossTot"=>$dateRangeGrossTot );
			//return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0, "tot_premium" => 0, "tot_premium_withtax" =>0, "yearlyNetTot" => 0, "yearlyGrossTot"=>0, "mothlyNetTot"=>0, "mothlyGrossTot"=>0, "weeklyNetTot"=>0, "weeklyGrossTot"=>0 );
		}
	}
	
	
	

	function smDashBorad($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "proposal_payment_details";
		$table_id = 'proposal_payment_id';
		$default_sort_column = 'premiumsum';
		$default_sort_order = 'desc';
		$condition = "1=1 AND i.created_by='".$_POST['sm_id']."' ";
		
		$colArray = array('c.creaditor_name','s.employee_full_name');
		$sortArray = array('premiumsum','c.creaditor_name','s.employee_full_name','premiumsum','premiumsum','premiumsum');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = 200;	// iDisplayLength no of records from the offset
		
		// sort order by column
		//echo $post['iSortCol_0'];exit;
		$sort = isset($post['iSortCol_0']) ? strval($post['iSortCol_0']) : $default_sort_column; 
		//echo $sort;exit;		
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<12;$i++)
		{
			
			if($i == 0 && isset($_POST['sSearch_0']) && !empty($_POST['sSearch_0'])){
				$condition .= " AND i.creditor_id = '".$_POST['sSearch_0']."'";
			}
			
			if($i == 1 && isset($post['sSearch_1']) && !empty($post['sSearch_1'])){
				$condition .= "  AND i.created_at >= '".date("Y-m-d",strtotime($post['sSearch_1']))." 00:00:01' ";
			}
			
			if($i == 2 && isset($post['sSearch_2']) && !empty($post['sSearch_2'])){
				$condition .= "  AND i.created_at <= '".date("Y-m-d",strtotime($post['sSearch_2']))." 23:59:59' ";
			}
			
			if($i == 3 && isset($_POST['sSearch_3']) && !empty($_POST['sSearch_3'])){
				if($_POST['Searchkey_3'] == 'Pending'){
					$condition .= " AND (l.status != 'Approved' && l.status != 'Rejected' ) ";
				}else{
					$condition .= " AND l.status = '".$_POST['sSearch_3']."'";
				}
			}
			
			
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.*, c.creaditor_name, s.employee_full_name, SUM(i.premium) as premiumsum, SUM(i.premium_with_tax) as premiumwithtaxsum');
		$this -> db -> from('proposal_payment_details as i');
		$this -> db -> join('master_employee as s', 'i.created_by  = s.employee_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> join('lead_details as l', 'i.lead_id  = l.lead_id', 'left');
		$this->db->where("($condition)");
		$this->db->group_by(array("i.creditor_id", "i.created_by"));
		$this->db->order_by('premiumwithtaxsum', $_POST['sSearch_4']);
		//$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.*, c.creaditor_name, s.employee_full_name, SUM(i.premium) as premiumsum, SUM(i.premium_with_tax) as premiumwithtaxsum');
		$this -> db -> from('proposal_payment_details as i');
		$this -> db -> join('master_employee as s', 'i.created_by  = s.employee_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> join('lead_details as l', 'i.lead_id  = l.lead_id', 'left');
		$this->db->where("($condition)");
		$this->db->group_by(array("i.creditor_id", "i.created_by"));
		$this->db->order_by('premiumwithtaxsum', $_POST['sSearch_4']);
		//$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		//echo $condition;exit;
		//echo "<pre>mmm";print_r($query->result());exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			$totcount_val = $totcount;
			$final_result_arr = array();
			$i = 0;
			$tot_premium = 0;
			$tot_premium_withtax = 0;
			$current_month = date("m");
			if($current_month >= 4){
				$cond =  "( YEAR(created_at) BETWEEN '".date("Y")."' AND '".(date("Y") + 1)."' )";
			}else{
				$cond =  "( YEAR(created_at) BETWEEN '".(date("Y") - 1)."' AND '".date("Y")."' )";
			}
			foreach($query->result() as $row){
				//Get Yearly Total    
				$yearly_query = $this->db->query("Select SUM(premium) as total, SUM(premium_with_tax) as totalwithtax from proposal_payment_details where creditor_id='".$row->creditor_id."' and created_by='".$row->created_by."' and $cond ");
				$yearly_result = $yearly_query->row();
				//echo $yearly_result->total;exit;
				
				//Get monthly Total    
				$monthly_query = $this->db->query("Select SUM(premium) as total, SUM(premium_with_tax) as totalwithtax from proposal_payment_details where creditor_id='".$row->creditor_id."' and created_by='".$row->created_by."' and YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at)=MONTH(NOW()) ");
				$monthly_result = $monthly_query->row();
				//echo $monthly_result->total;exit;
				
				//Get weekly Total    
				$weekly_query = $this->db->query("Select SUM(premium) as total, SUM(premium_with_tax) as totalwithtax from proposal_payment_details where creditor_id='".$row->creditor_id."' and created_by='".$row->created_by."' and YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at)=MONTH(NOW()) AND WEEKOFYEAR(created_at) = WEEKOFYEAR(NOW()) ");
				$weekly_result = $weekly_query->row();
				//echo $weekly_result->total;exit;
				
				
				if(!empty($post['sSearch_1']) && !empty($post['sSearch_2'])){
					$row->range_total = round($row->premiumsum, 2);
					$row->range_total_withtax = round($row->premiumwithtaxsum, 2);
					$row->date_from = date("d-m-Y", strtotime($post['sSearch_1']));
					$row->date_to = date("d-m-Y", strtotime($post['sSearch_2']));
				}else{
					$row->range_total = 0;
					$row->range_total_withtax = 0;
					$row->date_from = "-";
					$row->date_to = "-";
				}
				
				$row->yearly_tot = ($yearly_result->total > 0) ? round($yearly_result->total, 2) : 0;
				$row->monthly_tot = ($monthly_result->total > 0) ? round($monthly_result->total, 2) : 0;
				$row->weekly_tot = ($weekly_result->total > 0) ? round($weekly_result->total, 2) : 0;

				$row->yearly_tot_withtax = ($yearly_result->totalwithtax > 0) ? round($yearly_result->totalwithtax, 2) : 0;
				$row->monthly_tot_withtax = ($monthly_result->totalwithtax > 0) ? round($monthly_result->totalwithtax, 2) : 0;
				$row->weekly_tot_withtax = ($weekly_result->totalwithtax > 0) ? round($weekly_result->totalwithtax, 2) : 0;
				
				if($_POST['sSearch_4'] == 'desc'){
					$row->rank = ++$i;
				}else{
					$row->rank = $totcount_val--;
				}
				$final_result_arr[] = $row;
			}
			
			$tot_premium = $tot_premium_withtax = $weeklyNetTot = $weeklyGrossTot = $mothlyNetTot = $mothlyGrossTot = $yearlyNetTot = $yearlyGrossTot = $dateRangeNetTot = $dateRangeGrossTot = 0;
			
			foreach($query1->result() as $row1){
				//echo $row1->premiumsum."<br/>";
				$tot_premium += $row1->premiumsum;
				$tot_premium_withtax += $row1->premiumwithtaxsum;

				//Get Yearly Total    
				$yearly_query = $this->db->query("Select SUM(premium) as total, SUM(premium_with_tax) as totalwithtax from proposal_payment_details where creditor_id='".$row1->creditor_id."' and created_by='".$row1->created_by."' and $cond ");
				$yearly_result = $yearly_query->row();
				//echo $yearly_result->total;exit;
				
				//Get monthly Total    
				$monthly_query = $this->db->query("Select SUM(premium) as total, SUM(premium_with_tax) as totalwithtax from proposal_payment_details where creditor_id='".$row1->creditor_id."' and created_by='".$row1->created_by."' and YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at)=MONTH(NOW()) ");
				$monthly_result = $monthly_query->row();
				//echo $monthly_result->total;exit;
				
				//Get weekly Total    
				$weekly_query = $this->db->query("Select SUM(premium) as total, SUM(premium_with_tax) as totalwithtax from proposal_payment_details where creditor_id='".$row1->creditor_id."' and created_by='".$row1->created_by."' and YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at)=MONTH(NOW()) AND WEEKOFYEAR(created_at) = WEEKOFYEAR(NOW()) ");
				$weekly_result = $weekly_query->row();
				//echo $weekly_result->total;exit;
				
				$yearlyNetTot += $yearly_result->total;
				$mothlyNetTot += $monthly_result->total;
				$weeklyNetTot += $weekly_result->total;

				$yearlyGrossTot += $yearly_result->totalwithtax;
				$mothlyGrossTot += $monthly_result->totalwithtax;
				$weeklyGrossTot += $weekly_result->totalwithtax;

				if(!empty($post['sSearch_1']) && !empty($post['sSearch_2'])){
					$dateRangeNetTot += $row1->premiumsum;
					$dateRangeGrossTot += $row1->premiumwithtaxsum;
				}


			}
			
			//echo $tot_premium;exit;
			return array("query_result" => $final_result_arr, "totalRecords" => $totcount, "tot_premium" => round($tot_premium, 2), "tot_premium_withtax" => round($tot_premium_withtax,2), "yearlyNetTot" => round($yearlyNetTot,2), "yearlyGrossTot"=>round($yearlyGrossTot,2), "mothlyNetTot"=>round($mothlyNetTot,2), "mothlyGrossTot"=>round($mothlyGrossTot,2), "weeklyNetTot"=>round($weeklyNetTot,2), "weeklyGrossTot"=>round($weeklyGrossTot,2), "dateRangeNetTot"=>$dateRangeNetTot, "dateRangeGrossTot"=>$dateRangeGrossTot );
			//return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0, "tot_premium" => 0, "tot_premium_withtax" => 0, "yearlyNetTot" => 0, "yearlyGrossTot"=>0, "mothlyNetTot"=>0, "mothlyGrossTot"=>0, "weeklyNetTot"=>0, "weeklyGrossTot"=>0);
		}
	}
	


	function getDepartmentDetails(){
		$query = $this->db->get("tbl_department_master")->result_array();
		if(!empty($query)){
			return $query;
		}
		else
		{
			return false;
		}	


	}

}
?>