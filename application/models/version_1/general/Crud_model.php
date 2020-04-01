<?php
class Crud_model extends BEN_Model {

	public $table = "crud";

	public function accesses($data){

		$this->db->select('*');

		$account_type_id = $data['account_type_id'];
		
		$this->db->where('crud_access.account_type_id', $account_type_id);
		$this->db->where('crud_access.deleted', 0);
		$this->db->where('crud_access.status', 1);
		$this->db->join('crud', 'crud.id = crud_access.crud_id');
		$query = $this->db->get("crud_access");
		$return = $query->result_array();
		// print_r($return);
		// exit;
		if(!empty($return)){
			$return_array = array();
			foreach ($return as $return_key => $return_value) {
				$return_array[$return_key] = $return_value['module']."/".$return_value['controller']."/".$return_value['action_function'];
			}
			return $return_array;

		}else{
			return false;
		}
	}

	public function crud(){

		$this->db->select('*');
		$this->db->where('crud.deleted', 0);
		$this->db->where('crud.status', 1);
		$query = $this->db->get("crud");
		$return = $query->result_array();
		return $return;
	}

	public function accountType_company(){

        $this->db->select('*');
		$this->db->from('account_type');
		$this->db->join('company', 'account_type.company_id = company.id');
		$query = $this->db->get();
        $return = $query->result_array();
        return $return;    

	}

}