<?PHP
class Employeemodel extends CI_Model
{
	function insertData($tbl_name,$data_array,$sendid = NULL)
	{
	 	$this->db->insert($tbl_name,$data_array);
	 	$result_id = $this->db->insert_id();
	 	
	 	/*echo $result_id;
	 	exit;*/
	 	
	 	if($sendid == 1)
	 	{
	 		return $result_id;
	 	}
	}
	 
	
	
	function checkRecord($tbl_name,$POST,$condition)
	{
		//print_r($POST);
		//exit;
		$this -> db -> select('*');
		$this -> db -> from($tbl_name);
		$this->db->where("($condition)");
	
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