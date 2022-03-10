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
                    	<?php foreach($department_details as $value){?>
                    	<option value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?></option>
                    	<?php } ?>
                    </select>
                   
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Employee Name</label>
                    <input type="text" name="employee_name" class="form-control" id="employee_name" placeholder="Employee Name">
                  </div>
                 
                </div>
                <input type="hidden" name="emp_id" id="emp_id">
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


jQuery.validator.addMethod("lettersonlys", function(value, element) {
    return this.optional(element) || /^[a-zA-Z ]*$/.test(value);
}, "Letters only please");



var vRules = {
	department_id:{required:true},
	employee_name:{required:true, lettersonlys:true}
};

var vMessages = {
	department_id:{required:"Please select department name."},
	employee_name:{required:"Please enter employee name."}
};

$("#quickForm").validate({
	rules: vRules,
	messages: vMessages,
	submitHandler: function(form) 
	{
		debugger;
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
				debugger;
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