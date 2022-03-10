<?PHP
class Loginmodel extends CI_Model
{
	function login($username,$password)
	{
		$whr = "user_name = '".$username."' AND password = '".$password."' ";		
		$this->db->select('user_id, user_name, email_id, first_name , last_name, role_id, user_type, frecord_id');
		$this->db->from('tbl_admin_users');
		$this->db->where($whr);
		$this->db->limit(1);
		$query = $this->db->get();		
		if($query -> num_rows() == 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}
	}
	
	function getEmailContent($mail_key)
	{	
		$whr = "mail_key ='".$mail_key."' ";		
		$this->db->select('*');
		$this->db->from('tbl_emailcontents');
		
		$query = $this->db->get();
		
		if($query -> num_rows() == 1)
		{
			$res = $query->result_array();
			return $res[0];
		}
		else
		{
			return false;
		}
	}
	
	/* function getContentForgetPass($eid)
	{
		$this -> db -> select('fromemail,toemail,subject,content,eid');
		$this -> db -> from('tbl_emailcontents');
		$this -> db -> where('eid', $eid);
		$query = $this -> db -> get();	  
		if($query -> num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}
	
	} */
	
	function forgotpass($email)
	{
		$whr = "email_id ='".$email."' ";		
		$this->db->select('user_id, email_id, user_name, password');
		$this->db->from('tbl_admin_users');
		$this->db->where($whr);
		$this->db->limit(1);

		$query = $this->db->get();
		
		if($query -> num_rows() == 1)
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
