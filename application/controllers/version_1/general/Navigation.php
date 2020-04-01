<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Navigation extends BEN_General {
    public $current_function;
    function __construct() {

        parent::__construct();
        $this->module_folder = "general";
        $this->page_title = "Navigation";
        $this->view_folder = strtolower(get_class());
        
    }

    public function index(){
        $this->ben_view_ultraclear(__FUNCTION__);
    }

    
}
