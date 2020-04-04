<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Online_offline extends BEN_General {

    public $current_function;

    public $tables;

    function __construct() {

        parent::__construct();
        $this->module_folder = "general";
        $this->page_title = "020";
        $this->view_folder = strtolower(get_class());
        $this->tables = $arrayName = array('blackboard');
        $this->load->model('version_1/general/online_offline_model');
        $this->host = "http://test.campuscloudph.com/";
        $this->folder = "campus/version_1/general/online_offline/";
        $this->online_link = $this->host.$this->folder;
        $this->password = "campuscloudph";
    }

    public function index($password=""){
        $this->check_password($password);
        echo "<pre>";
        $tables = $this->tables;
        foreach ($tables as $table_key => $table_value) {
            //get all id and date_updated online and offline
            //get offline ids
            $offline_ids = $this->online_offline_model->get_all_id($table_value);
            //get online ids
            $get_online_ids = $this->online_link."get_online_ids/".$this->password."/".$table_value;
            $ch = curl_init($get_online_ids);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_POSTREDIR, 3);                                                                  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                 
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $online_fetch = json_decode(curl_exec($ch));
            curl_close($ch);
            print_r($online_fetch);
            //get all id and date_updated online and offline
        }
        

        //check new ids of offline or online ids
        
        //check newer updated data
        //separate newer online or offline id

        //fetch from online for new offline data

        //fetch from offline for new online data

        //fetch from online for newer data

        //fetch from offline for newer data

    }

    public function check_password($password){
        if($password!=$this->password){
            exit("This has a grave restriction access! Your IP will be reported and banned.");
        }
    }

    public function get_online_ids($password="",$table=""){
        $this->check_password($password);
        $return = $this->online_offline_model->get_all_id($table);
        echo json_encode($return);
    }

}
