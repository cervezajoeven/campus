<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Initial extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper('url');

   	}	

	public function index(){

		redirect("version_1/general/home/index");
	}

}
