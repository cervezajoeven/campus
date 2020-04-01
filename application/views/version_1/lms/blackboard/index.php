<style type="text/css">
    .lesson{
        min-height: 345px;
    }
    .lesson .body {
        padding: 0;
    }
    .views_counter{
        top: -5px;
        position: relative;
    }
    .card .header h2 {
        margin: 0;
        font-size: 18px;
        font-weight: normal;
        color: #111;
        min-height: 36px;
    }
    .pointer{
        cursor: pointer;
    }
</style>
<div class="row clearfix">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            
            <?php echo $general_class->ben_open_form("lms/".$general_class->current_page['controller']."/save"); ?>
                <div class="header">
                    <div class="row clearfix">
                        <div class="col-xs-12 col-sm-6">
                            <h2>Lessons</h2>
                        </div>
                    </div>
                </div>
                <div class="body">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" class="form-control" placeholder="Search For Lessons">
                            </div>
                        </div>
                    </div>
                    
                    <a href="<?php echo $general_class->ben_link('lms/blackboard/save');?>"><button class="form-control btn btn-success waves-effect" type="button">Create New</button></a>
                        
                </div>
            </form>
        </div>
    </div>
    <?php foreach ($data['blackboard'] as $key => $value) : ?>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 pointer" data-id="<?php echo $value['id']; ?>">
            <div class="card lesson bg-pink">
                <div class="header bg-pink">
                    <h2>
                        <?php echo $value['lesson_name'] ?>
                    </h2>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="javascript:void(0);">Open</a></li>
                                <li><a href="javascript:void(0);">Delete</a></li>
                                <li><a href="javascript:void(0);">Something else here</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    <img style="height: 200px;width: 100%;" src="https://www.panaynews.net/wp-content/uploads/2019/06/Jose-Rizal-696x365.png">
                </div>
                <div class="header bg-pink">
                    <h2>
                        <small>March 23, 2020</small>
                    </h2>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown views_counter">
                            54
                        </li>
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">remove_red_eye</i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var create_link = '<?php echo $general_class->ben_link("lms/blackboard/create")?>'
        $(".pointer").click(function(){
            var data_id = $(this).attr("data-id");
            window.location.replace(create_link+"/"+data_id);
        });
    });
</script>