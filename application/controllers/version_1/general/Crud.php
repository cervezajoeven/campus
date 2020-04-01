<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crud extends BEN_General {
    public $current_function;
    function __construct() {

        parent::__construct();
        $this->module_folder = "general";
        $this->page_title = "CRUD";
        $this->view_folder = strtolower(get_class());
    }

    public function index(){
        $this->data['data'] = $this->crud_model->crud();
        $this->data['account_type'] = $this->account_type_model->account_type();
        // echo "<pre>";
        // print_r($this->data['account_type']);
        // exit();
        $this->sms_view(__FUNCTION__);
    }

    public function account_type($id=""){
        $this->db->select("account_type_id");
        $this->db->where("crud_id",$id);
        $query = $this->db->get("crud_access");
        $array = $query->result_array();
        $return = array();
        foreach ($array as $key => $value) {
           array_push($return, $value['account_type_id']);
        }

        echo json_encode($return);
    }

    public function update_crud_access($crud_id=""){
        if(!$crud_id){
            $data = explode(",", $_REQUEST['data']);
            $this->db->where('crud_id',$crud_id);
            $this->db->delete('crud_access');

            foreach ($data as $key => $value) {
                $insert_data = array(
                    "account_type_id" =>$value,
                    "crud_id" => $crud_id
                );
                $this->account_type_model->create_new("crud_access",$insert_data);
            }
        }
    }

    public function create(){

        $this->data['all_data'] = $this->crud_model->multiple_join(array('account_type','company'));

        $this->ben_view(__FUNCTION__);
    }

    public function read(){

        if($_REQUEST['id_storage']){
            $id_storage = json_decode($_REQUEST['id_storage']);
            $this->data['all_account_type'] = $this->crud_model->join(array("account_type"=>"company_id","company"=>"id"));
            $this->data['update_data'] = $this->db->get_where("crud",array("id"=>$id_storage[0]))->result_array()[0];
            $this->ben_view(__FUNCTION__);
        }else{
            $this->ben_notify(array(array("danger","No data was specified")));
            $this->ben_redirect("general/crud/index");
        }
    }

    public function update(){
        if($_REQUEST['id_storage']){
            $id_storage = json_decode($_REQUEST['id_storage']);
            $this->data['all_account_type'] = $this->crud_model->join(array("account_type"=>"company_id","company"=>"id"));
            $this->data['update_data'] = $this->db->get_where("crud",array("id"=>$id_storage[0]))->result_array()[0];
            $this->ben_view(__FUNCTION__);
        }else{
            $this->ben_notify(array(array("danger","No data was specified")));
            $this->ben_redirect("general/crud/index");
        }
        
    }
    public function delete(){
        $this->db->where('id',$_REQUEST['id']);
        $this->db->delete('crud');
        $this->ben_redirect("general/crud/index");
    }

    public function save(){
        
        if($_REQUEST){

            $crud = explode("/",$_REQUEST['crud']);
            $data['module'] = $crud[0];
            $data['controller'] = $crud[1];
            $data['action_function'] = $crud[2];
            if($this->crud_model->create_new("crud",$data)){

                $this->ben_redirect("general/crud/index");
            }
        }else{

            $this->ben_redirect("general/crud/index");
        }
    }

    public function update_save(){
        if($_REQUEST){
            // var_dump($this->crud_model->update("crud",$_REQUEST));
            // print_r($_REQUEST);

            if($this->crud_model->update("crud",$_REQUEST)){
                $this->ben_notify(array(array("success","You have successfully updated the data")));
                $this->ben_redirect("general/crud/index");
            }
        }else{
            $this->ben_notify(array(array("warning","No data to update")));
            $this->ben_redirect("general/crud/index");
        }
    }

}
