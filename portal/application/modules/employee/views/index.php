  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Employees</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Employees</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
          
            <div class="card">
              <div class="card-header">
              	<div class="col-md-3 col-6">
               <a href="<?php echo base_url();?>employee/addEdit">
								<button type="button" class="btn btn-block btn-primary">Add Employee</button>
							</a>
							</div>

              </div>
              <div class="card-body">
        <div class="row">
          
          <div class="col-md-3 mb-3">
            <label for="validationCustomUsername" class="col-form-label">Employee Name</label>
            <div class="dataTables_filter input-group">
              <input id="sSearch_0" name="sSearch_0" type="text" class="searchInput form-control" placeholder="Name" aria-describedby="inputGroupPrepend" >
              
            </div>
          </div>
          
          <div class="col-md-2 col-12 txt-lr">
            <label style="visibility: hidden;" class="mt-1">For Space</label>
            <a href="<?php echo base_url();?>employee"><button class="btn cnl-btn btn-primary">Clear Search</button></a>
          </div>
        </div>
      </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="dynamicTable table table-bordered non-bootstrap display pt-3 pb-3">
            <thead class="tbl-cre">
              <tr>
                    <th>Department</th>
                    <th>Employee Name</th>
                    <th>Date</th>
                    <th>Action</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
                
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

  <script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
    })
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>
<script type="text/javascript">
$( document ).ready(function() {
	
});

function deleteData(id)
{
	var r=confirm("Are you sure you want to delete this record?");
	if (r==true)
	{
		$.ajax({
			url: "<?php echo base_url().$this->router->fetch_module();?>/employee/delRecord/"+id,
			async: false,
			type: "POST",
			success: function(data2){
				data2 = $.trim(data2);
				if(data2 == "1")
				{
					displayMsg("success","Record has been Deleted!");
					setTimeout("location.reload(true);",1000);
					
				}
				else
				{
					displayMsg("error","Oops something went wrong!");
					setTimeout("location.reload(true);",1000);
				}
			}
		});
	}
}
document.title = "Employee";
</script>