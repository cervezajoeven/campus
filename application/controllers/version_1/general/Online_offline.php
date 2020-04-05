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


            //get online ids
            $get_online_ids = $this->online_link."get_online_ids/".$this->password."/".$table_value;
            $ch = curl_init($get_online_ids);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_POSTREDIR, 3);                                                                  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                 
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $online_fetch = json_decode(curl_exec($ch));
            curl_close($ch);

            $online_pool = $online_fetch;

            //get offline ids
            $offline_pool = $this->online_offline_model->get_all_id($table_value);


            //get all id and date_updated online and offline

            //check new ids of offline or online ids
            $offline_list = [];
            foreach ($offline_pool as $pool_key => $pool_value) {
                array_push($offline_list, $pool_value['id']);
            }
            $online_list = [];
            foreach ($online_pool as $pool_key => $pool_value) {
                array_push($online_list, $pool_value->id);
            }
             //check new ids of offline or online ids

            //check newer updated data
            //separate newer online or offline id
            $new_offline = [];
            foreach ($offline_list as $list_key => $list_value) {
                if(!in_array($list_value, $online_list)){
                    array_push($new_offline, $list_value);
                }
            }
            $new_online = [];
            foreach ($online_list as $list_key => $list_value) {
                if(!in_array($list_value, $offline_list)){
                    array_push($new_online, $list_value);
                }
            }

            //fetch from offline for new online data
            $offline_data = $this->get_offline_data($this->password,$table_value,$new_offline);
            $get_online_data = $this->online_link."get_online_data/".$this->password."/".$table_value;
            $ch = curl_init($get_online_data);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_POSTREDIR, 3);                                                                  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);                              
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($new_online));
            $online_data = json_decode(curl_exec($ch));
            curl_close($ch);

            print_r($online_data);

           

        }
        

        
        
        

        

        //fetch from online for new offline data


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
    public function get_online_data($password="",$table=""){
        $this->check_password($password);
        $return = [];
        echo json_encode($_REQUEST);
        // foreach ($ids as $key => $value) {
        //     array_push($return, $this->online_offline_model->get_data($table,$value)[0]);
        // }
        
        // echo json_encode($return);

    }
    public function get_offline_data($password="",$table="",$ids=""){
        $this->check_password($password);
        $return = [];

        foreach ($ids as $key => $value) {
            array_push($return, $this->online_offline_model->get_data($table,$value)[0]);
        }
        
        return $return;

    }

    public function insert_data($password="",$table="",$id=""){

        
    }

}
