<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blackboard extends BEN_General {
    public $current_function;
    function __construct() {

        parent::__construct();
        $this->module_folder = "lms";
        $this->page_title = "Blackboard";
        $this->view_folder = strtolower(get_class());
        $this->load->model("version_".$this->app_version.'/lms/'.'blackboard_model');
        $this->load->library('../controllers/version_1/lms/youtube');
    }

    public function index(){
        $this->toggled = array("sics","blackboard");
        $this->data['blackboard'] = $this->blackboard_model->all("blackboard");
        // echo "<pre>";
        // print_r($this->data['blackboard']);
        // exit;
        $this->sms_view(__FUNCTION__);
    }

    public function create($id=""){
        $this->toggled = array("sics","blackboard");
        $this->data['id'] = $id;
        $this->data['blackboard'] = $this->blackboard_model->ben_get_by_id("blackboard",$id);
        if(!is_dir(FCPATH."resources/uploads/blackboard/".$id)){
            mkdir(FCPATH."resources/uploads/blackboard/".$id);
            mkdir(FCPATH."resources/uploads/blackboard/".$id."/thumbnails/");
            mkdir(FCPATH."resources/uploads/blackboard/".$id."/contents/");
        }
        
        $this->ben_view_ultraclear(__FUNCTION__);
    }

    public function save(){
        
        $data = array("lesson_name"=>"Untitled");
        $id = $this->blackboard_model->create_new("blackboard",$data);

        if(!is_dir(FCPATH."resources/uploads/blackboard/".$id)){
            mkdir(FCPATH."resources/uploads/blackboard/".$id);
            mkdir(FCPATH."resources/uploads/blackboard/".$id."/thumbnails/");
            mkdir(FCPATH."resources/uploads/blackboard/".$id."/contents/");
        }
        
        $this->ben_redirect("lms/blackboard/create/".$id);
    }

    public function update(){

        $data['id'] = $_REQUEST['id'];
        $data['lesson_name'] = $_REQUEST['title'];
        $data['content_order'] = json_encode($_REQUEST['content_order']);
        $data['content_pool'] = json_encode($_REQUEST['content_pool']);
        $data['folder_names'] = $_REQUEST['folder_names'];
        $this->blackboard_model->update("blackboard",$data);

        //thumbnails
        $this->db->select("content_id");
        $this->db->where("table_id","thumbnail_".$data['id']);
        $query = $this->db->get("resources_queue");
        $thumbnails_result = $query->result_array();
        $thumbnails = [];
        foreach ($thumbnails_result as $key => $value) {
            array_push($thumbnails, $value['content_id']);
        }

        foreach ($_REQUEST['content_pool'] as $key => $value) {
            if(!in_array($value['content']['result_id'], $thumbnails)){
                $download_data['table_id'] = "thumbnail_".$data['id'];
                $download_data['file_type'] = "image";
                $download_data['content_id'] = $value['content']['result_id'];
                $download_data['url'] = urldecode($value['content']['image']);
                $download_data['output_path'] = "C:\\xampp\htdocs\campus\\resources\uploads\blackboard\\".$data['id']."\\thumbnails\\";
                $type = $this->check_url_type($value['content']['image']);
                $download_data['filename'] = $value['content']['result_id'];
                $download_data['completed'] = 0;
                $download_data['status'] = "download";

                $this->blackboard_model->create_new("resources_queue",$download_data);
            }
            

        }
        //thumbnails

        //contents
        $this->db->select("content_id");
        $this->db->where("table_id",$data['id']);
        $query = $this->db->get("resources_queue");
        $content_result = $query->result_array();
        $contents = [];
        foreach ($content_result as $key => $value) {
            array_push($contents, $value['content_id']);
        }
        foreach ($_REQUEST['content_pool'] as $key => $value) {
            

            if(!in_array($value['content']['result_id'], $contents)){

                $download_data['output_path'] = "C:\\xampp\htdocs\campus\\resources\uploads\blackboard\\".$data['id']."\\contents\\";
                $download_data['filename'] = $value['content']['result_id'];
                $download_data['table_id'] = $data['id'];
                $download_data['content_id'] = $value['content']['result_id'];
                $download_data['completed'] = 0;

                if($value['content']['type'] == "youtube"){
                    $download_data['file_type'] = "video";
                    $download_data['url'] = $this->youtube($value['content']['source']);
                }else{
                    $download_data['file_type'] = urldecode($value['content']['type']);
                    $download_data['url'] = urldecode($value['content']['source']);
                }
                
                if($value['content']['type'] == "website"){
                    $download_data['status'] = "convert";

                }else{
                    $download_data['status'] = "download";
                }
                

                if($download_data['url']!=""){
                    $this->blackboard_model->create_new("resources_queue",$download_data);
                }
                
            }
            

        }
        //contents

    
    }

    public function check_thumbnail(){
        
        if(array_key_exists('data', $_REQUEST)){
            $data = $_REQUEST['data'];
            $blackboard_id = $_REQUEST['blackboard_id'];
            //thumbnails
            $thumbnail_directory = FCPATH."resources/uploads/blackboard/".$blackboard_id."/";
            $thumbnail_link_directory = base_url('resources/uploads/blackboard/'.$blackboard_id.'/');
            $existed = [];
            // print_r($data);
            foreach ($data as $key => $value) {
                
                
                if(!strpos($value['image'],"campuscloudph")&&!strpos($value['image'],"localhost")){
                    $thumbnail_file_directory = $thumbnail_directory."thumbnails/".$value['result_id'].".jpg";

                    if(file_exists($thumbnail_file_directory)){
                        $thumbnail_link = $thumbnail_link_directory."thumbnails/".$value['result_id'].".jpg";
                        $exists = array('key'=>$value['result_id'],'offline_thumbnail'=>$thumbnail_link);
                        array_push($existed, $exists);
                    }else{
                        $thumbnail_link = $value['image'];
                        $exists = array('key'=>$value['result_id'],'offline_thumbnail'=>$thumbnail_link);
                        array_push($existed, $exists);
                        
                    }
                }
                
                
            }

            echo json_encode($existed);
        }else{
            echo "empty";
        }
        
    }

    public function check_offline_files(){
        if(array_key_exists('data', $_REQUEST)){
            $data = $_REQUEST['data'];
            $blackboard_id = $_REQUEST['blackboard_id'];


            $directory = FCPATH."resources/uploads/blackboard/".$blackboard_id."/contents/";
            $link_directory = base_url('resources/uploads/blackboard/'.$blackboard_id."/contents/");
            
            $existed = [];

            foreach ($data as $key => $value) {
                
                if(!strpos($value['data']['content']['source'],"campuscloudph")&&!strpos($value['data']['content']['source'],"localhost")){

                    $old_type = $value['data']['content']['type'];
                    $file_directory = $directory.$value['data']['content']['result_id'];
                    $link = $link_directory.$value['data']['content']['result_id'];
                    $the_filename = $value['data']['content']['result_id'];

                    if($value['data']['content']['type']=="website"||$value['data']['content']['type']=="pdf"){

                        $file_directory = $file_directory.".pdf";
                        $link = $link.".pdf";
                        $the_filename = $the_filename.".pdf";
                        $type = "pdf";

                    }elseif($value['data']['content']['type']=="youtube"||$value['data']['content']['type']=="video"){
                        $file_directory = $file_directory.".mp4";
                        $link = $link.".mp4";
                        $the_filename = $the_filename.".mp4";
                        $type = "video";
                    }elseif($value['data']['content']['type']=="image"){
                        $file_directory = $file_directory.".jpg";
                        $link = $link.".jpg";
                        $the_filename = $the_filename.".jpg";
                        $type = "image";
                    }
                    if(file_exists($file_directory)){
                        
                        $result_id = $value['data']['content']['result_id'];
                        $this->db->select("completed");
                        $this->db->where("table_id",$blackboard_id);
                        $this->db->where("content_id",$result_id);
                        $result = $this->db->get("resources_queue")->result_array();

                        if(!empty($result)){
                            if($result[0]['completed']==1){
                                $exists = array('key'=>$value['key'],'type'=>$type,'source'=>urlencode($link),'status'=>'completed');
                            }else{
                                $exists = array('key'=>$value['key'],'type'=>$type,'source'=>urlencode($link),'status'=>'downloading');
                            }
                        }

                        
                    }else{
                        $result_id = $value['data']['content']['result_id'];
                        $this->db->select("completed");
                        $this->db->where("table_id",$blackboard_id);
                        $this->db->where("content_id",$result_id);
                        $result = $this->db->get("resources_queue")->result_array();
                        if(!empty($result)){
                            if($result[0]['completed']==1){
                                $exists = array('key'=>$value['key'],'type'=>$old_type,'source'=>$value['data']['content']['source'],'status'=>'checking');
                            }else{
                                $exists = array('key'=>$value['key'],'type'=>$old_type,'source'=>$value['data']['content']['source'],'status'=>'downloading');
                            }
                        }else{
                            $exists = array('key'=>$value['key'],'type'=>$old_type,'source'=>$value['data']['content']['source'],'status'=>'not_downloadable');
                        }
                        
                        
                    }
                    
                }else{
                    $result_id = $value['data']['content']['result_id'];
                    $this->db->select("completed");
                    $this->db->where("table_id",$blackboard_id);
                    $this->db->where("content_id",$result_id);
                    $result = $this->db->get("resources_queue")->result_array();
                    if(!empty($result)){
                        if($result[0]['completed']==1){
                            $exists = array('key'=>$value['key'],'type'=>$value['data']['content']['type'],'source'=>$value['data']['content']['source'],'status'=>'completed');
                        }else{
                            $exists = array('key'=>$value['key'],'type'=>$value['data']['content']['type'],'source'=>$value['data']['content']['source'],'status'=>'downloading');
                        }
                    }else{
                        $exists = array('key'=>$value['key'],'type'=>$old_type,'source'=>$value['data']['content']['source'],'status'=>'not_downloadable');
                    }
                    
                }
                array_push($existed, $exists);

                
            }

            echo json_encode($existed);
        }else{
            echo "empty";
        }

        
    }

    public function get($id){

        echo json_encode($this->blackboard_model->ben_get_by_id("blackboard",$id));

    }

    public function check_url_type($url=""){
        $image_array = array(".png",".jpg",".jpeg",".svg");
        $image_type = "";
        foreach ($image_array as $key => $value) {

            if(strpos(strtolower($url), $value)){
                $image_type = $value;
            }
            
        }
        return $image_type;
        
    }


    public function youtube($url=""){
        $youtubeURL = urldecode($url);
        $youtubeURL = "https://youtube.com/watch?v=".str_replace("/", "", explode("embed",$youtubeURL)[1]);
        
        // Check whether the url is valid 
        if(!empty($youtubeURL) && !filter_var($youtubeURL, FILTER_VALIDATE_URL) === false){ 
            // Get the downloader object 
            $downloader = $this->youtube->getDownloader($youtubeURL); 
            // print_r($downloader);
            // exit;
            
            // Set the url 
            $downloader->setUrl($youtubeURL); 
             
            // Validate the youtube video url 
            if($downloader->hasVideo()){ 
                // Get the video download link info 
                $videoDownloadLink = $downloader->getVideoDownloadLink(); 
                 
                $videoTitle = $videoDownloadLink[0]['title']; 
                $videoQuality = $videoDownloadLink[0]['qualityLabel']; 
                $videoFormat = $videoDownloadLink[0]['format']; 
                $videoFileName = strtolower(str_replace(' ', '_', $videoTitle)).'.'.$videoFormat; 
                
                $fileName = preg_replace('/[^A-Za-z0-9.\_\-]/', '', basename($videoFileName));
                
                if(array_key_exists('url', $videoDownloadLink[0])){
                    $downloadURL = $videoDownloadLink[0]['url'];
                    return $downloadURL;
                }else{
                    return false;
                }
                
            
            }else{ 
                echo "The video is not found, please check YouTube URL."; 
            } 
        }else{ 
            echo "Please provide valid YouTube URL."; 
        }
    }

   
    

}
