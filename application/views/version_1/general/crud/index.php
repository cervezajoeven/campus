<link rel="stylesheet" type="text/css" href="<?php echo $general_class->ben_resources('lms/multi-select.css') ?>">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
<style type="text/css">
    .form-control{
        border-bottom: 1px solid #e9e9e9;
    }
    .hrline {
       width: 100%; 
       text-align: center; 
       border-bottom: 1px solid #000; 
       line-height: 0.1em;
       margin: 10px 0 20px; 
    } 

    .hrline span { 
        background:#fff; 
        padding:0 10px;
    }
    .custom-header{
        background-color: rgb(50,50,50);
        color:white;
        text-align: center;
    }
</style>
<div class="row clearfix">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            
            <div class="header">
                <div class="row clearfix">
                    <div class="col-xs-12 col-sm-6">
                        <h2>Accesses</h2>
                    </div>
                </div>
                <ul class="header-dropdown m-r--5">

                </ul>
            </div>
            <div class="body">
                <form action="<?php echo $general_class->ben_link('general/crud/save'); ?>" method="POST">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" class="form-control" placeholder="New Crud" name="crud"/>
                            </div>
                        </div>
                        <button class="btn btn-primary" type="submit">Add</button>
                    </div>
                </form>    
                <form action="<?php echo $general_class->ben_link('general/crud/delete'); ?>" id="delete" method="POST">
                    <div class="col-lg-8">
                        <div class="form-group">
                            <label>Access</label>
                            <select id="data" class="form-control" name="id">
                                <option>Please Select</option>
                                <?php foreach ($data['data'] as $data_key => $data_value): ?>
                                    <option class="" value="<?php echo $data_value['id'] ?>">
                                        <?php echo $data_value['module']; ?>/<?php echo $data_value['controller']; ?>/<?php echo $data_value['action_function']; ?>
                                            
                                    </option>
                                    
                                <?php endforeach; ?>
                                
                            </select>
                            
                        </div>
                        <button class="btn btn-danger form-control" type="submit">Delete</button>
                    </div>

                </form>

                <div class="col-lg-4">
                    <?php foreach ($data['account_type'] as $data_key => $data_value): ?>
                        <div class="form-group">
                            <input type="checkbox" data-id="<?php echo $data_value['id'] ?>" id="basic_checkbox_<?php echo $data_value['id'] ?>" class="filled-in"/>
                            <label for="basic_checkbox_<?php echo $data_value['id'] ?>"><?php echo $data_value['account_type_name'] ?></label>

                        </div>
                        
                    <?php endforeach; ?>

                </div>
            
                
                <input type="submit" class="btn btn-primary waves-effect" value="Save" name="">
            </div>
        </div>
    </div>
</div>

<?php $general_class->datatable_clear() ?>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.full.js"></script>
<script type="text/javascript">

    $("#data").select2();
    function sleep(milliseconds) {
      const date = Date.now();
      let currentDate = null;
      do {
        currentDate = Date.now();
      } while (currentDate - date < milliseconds);
    }
    async function awaitData(id) {
        await getData(id);   
    }
    function getData(id){
        $.ajax({
            url: '<?php echo $general_class->ben_link("general/crud/account_type")?>/'+id,
        }).done(function(data){
            var parsed_data = JSON.parse(data);
            $(".filled-in").prop("checked",false);
            $.each(parsed_data,function(key,value){

                
                $('input[data-id="'+value+'"]').prop("checked",true);
            });

        });
        
    }

    $("#data").change(function(){

        awaitData($(this).val());

    });
    $("#delete").submit(function(e){
        if(confirm("Are you sure you want to delete this crud?")){

        }else{
            e.preventDefault();
        }
    });
    $(".filled-in").change(function() {
        var checked = [];
        var crud_id = $("#data").val();
        $.each($(".filled-in"),function(key,value){
            
            if($(value).is(":checked")) {
                checked.push($(value).attr("data-id"));
            }
        });
        checked_data = checked.join(",");
        checked_data = {data:checked_data};
        $.ajax({
            url: '<?php echo $general_class->ben_link("general/crud/update_crud_access")?>/'+crud_id,
            data: checked_data,
        }).done(function(data){
            
        });
    });
    
    
</script>