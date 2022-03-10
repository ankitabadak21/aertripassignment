<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Employee</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Employee</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-primary">
              
              <!-- /.card-header -->
              <!-- form start -->
              <form id="quickForm" method="post">
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Department</label>
                    <select id="department_id" class="form-control" name="department_id">
                    	<option value="">Select Department</option>
                    	<?php foreach($department_details as $value){
                    		$selected = "";
                    		if(isset($getDetails)){
                    			if($getDetails[0]['department_id'] == $value['id']){
	                    			$selected = "selected";
	                    		}
                    		}
                    		
                    		?>
                    	<option value="<?php echo $value['id'] ?>" <?php echo $selected;?>><?php echo $value['name'] ?></option>
                    	<?php } ?>
                    </select>
                   
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Employee Name</label>
                    <input type="text" name="employee_name" class="form-control" id="employee_name" value="<?php echo (isset($getDetails[0]['employee_name'])) ? $getDetails[0]['employee_name'] : "";?>" placeholder="Employee Name">
                  </div>
                  <?php if(!isset($contactDetails)){?>
                   <div class="form-group control-group after-add-more-phone">
                    <label for="exampleInputPassword1">Phone Number</label>
                    <input type="text" name="phone_no[]" class="form-control" value="<?php echo (isset($getDetails[0]['phone_no'])) ?? "";?>" placeholder="Phone No">
                  </div>
                  <div class="form-group copy-phone hide" style="display:none;">
                  	 <div class="control-group input-group" style="margin-top:10px">
			          <label for="exampleInputPassword1">Phone Number</label>
			            <input type="text" name="phone_no[]" class="form-control" value="<?php echo (isset($getDetails[0]['phone_no'])) ?? "";?>" placeholder="Phone No">
			            <div class="input-group-btn"> 
			              <button class="btn btn-danger remove-phone" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
			            </div>
			            </div>
			          
			        </div> 
			        <div class="input-group-btn"> 
		            <button class="btn btn-success add-more-phone" type="button"><i class="glyphicon glyphicon-plus"></i> Add More Phone NO.</button>
		          </div>
                  <?php }else{
                  	$i = 1;
                  	foreach($contactDetails as $key => $value){ ?>

                  <div class="form-group copy-phone hide" >
                  	 <div class="control-group input-group" style="margin-top:10px">
			          <label for="exampleInputPassword1">Phone Number</label>
			            <input type="text" name="phone_no[]" class="form-control" value="<?php echo $value['contact_no'] ;?>" placeholder="Phone No">
			            <?php if($i != 1){?>
			            <div class="input-group-btn"> 
			              <button class="btn btn-danger remove-phone" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
			            </div>
			            <?php }?>
			            </div>
			          
			        </div> 
                  <?php $i++; }
                  } ?> 
                  
                  

                      

			        <?php if(!isset($addressDetails)){?>
                  <div class="form-group control-group after-add-more-address">
                    <label for="exampleInputPassword1">Address</label>
                    <input type="text" name="address[]" class="form-control" value="<?php echo (isset($getDetails[0]['address'])) ?? "";?>" placeholder="Address">
                  </div>
                  <div class="input-group-btn"> 
		            <button class="btn btn-success add-more-address" type="button"><i class="glyphicon glyphicon-plus"></i> Add More Addess</button>
		          </div>
                  

                  <div class="form-group copy-address hide" style="display:none;">
                  	 <div class="control-group input-group" style="margin-top:10px">
			          <label for="exampleInputPassword1">Address</label>
			            <input type="text" name="address[]" class="form-control" value="<?php echo (isset($getDetails[0]['address'])) ?? "";?>" placeholder="Address">
			            <div class="input-group-btn"> 
			              <button class="btn btn-danger remove-address" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
			            </div>
			            </div>
			          
			        </div> 
                  <?php }else{ 
                  	//print_r($contactDetails);exit;
                  	$j = 1;
                  	foreach($addressDetails as $key => $value){?>
                  	<div class="form-group copy-address hide">
                  	 <div class="control-group input-group" style="margin-top:10px">
			          <label for="exampleInputPassword1">Address</label>
			            <input type="text" name="address[]" class="form-control" value="<?php echo $value['address']; ?>" placeholder="Address">
			            <?php if($j != 1){?>
			            <div class="input-group-btn"> 
			              <button class="btn btn-danger remove-address" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
			            </div>
			            <?php } ?>
			            </div>
			          
			        </div> 
                  	<?php $j++; }
                  } ?> 
                      



                 
                </div>
                <input type="hidden" name="emp_id" value="<?php echo (isset($getDetails[0]['emp_id'])) ? $getDetails[0]['emp_id'] : "";?>" id="emp_id">
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
            </div>
            <!-- /.card -->
            </div>
          <!--/.col (left) -->
          <!-- right column -->
          <div class="col-md-6">

          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
<!-- end: Content -->		
<script type="text/javascript">


    $(document).ready(function() {


      $(".add-more-phone").click(function(){ 
          var html = $(".copy-phone").html();
          $(".after-add-more-phone").after(html);
      });


      $(document).on("click",".remove-phone",function(){
          $(this).parents(".control-group").remove();
      });


      $(".add-more-address").click(function(){ 
          var html = $(".copy-address").html();
          $(".after-add-more-address").after(html);
      });


      $(document).on("click",".remove-address",function(){
          $(this).parents(".control-group").remove();
      });


    });


</script>



<script type="text/javascript">


jQuery.validator.addMethod("lettersonlys", function(value, element) {
    return this.optional(element) || /^[a-zA-Z ]*$/.test(value);
}, "Letters only please");



var vRules = {
	department_id:{required:true},
	employee_name:{required:true, lettersonlys:true},
	phone_no:{required:true},
	address:{required:true},


};

var vMessages = {
	department_id:{required:"Please select department name."},
	employee_name:{required:"Please enter employee name."},
	phone_no:{required:"Please enter phone number."},
	address:{required:"Please enter address."},
	

};

$("#quickForm").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		// debugger;
		var act = "<?php echo base_url();?>employee/submitForm";
		$("#quickForm").ajaxSubmit({
			url: act, 
			type: 'post',
			dataType: 'json',
			cache: false,
			clearForm: false, 
			beforeSubmit : function(arr, $form, options){
				$(".btn-primary").hide();
				//return false;
			},
			success: function (response) 
			{
				// debugger;
				$(".btn-primary").show();
				if(response.success)
				{
					displayMsg("success",response.msg);
					setTimeout(function(){
						window.location = "<?php echo base_url();?>employee";
					},2000);
				}
				else
				{	
					displayMsg("error",response.msg);
					return false;
				}
			}
		});
	}
});



document.title = "Add/Edit Employee";
</script>