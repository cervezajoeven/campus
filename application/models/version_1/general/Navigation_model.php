<?php
class Navigation_model extends BEN_Model {

	public $table = "profile";

	public function navigations($account_id=""){
		$this->db->select("*");
		$this->db->from("navigation");
		$this->db->where("navigation");
	}

}