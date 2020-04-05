<?php
class Online_offline_model extends BEN_Model {

	public $table = "account_type";

	public function get_all_id($table=""){
		$this->db->select("id,date_updated");
		$query = $this->db->get($table);
		$return = $query->result_array();
		return $return;
	}

	public function get_data($table="",$id=""){
		$this->db->select("*");
		$this->db->where("id",$id);
		$query = $this->db->get($table);
		$return = $query->result_array();
		return $return;
	}

}