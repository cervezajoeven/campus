<?php
class Account_type_model extends BEN_Model {

	public $table = "account_type";

	public function account_type($id=""){
		$this->db->select("*");
		$query = $this->db->get("account_type");
		$return = $query->result_array();
		return $return;
	}

}