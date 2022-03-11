<?php
//Get Login user details
$ludata = array();
$ludata['utoken'] = $_SESSION['webpanel']['utoken'];
$ludata['id'] = $_SESSION['webpanel']['id'];
$luserDetails = curlFunction(SERVICE_URL . '/api/getLoginUserDetails', $ludata);
$luserDetails = json_decode($luserDetails, true);
//echo "here<pre>";print_r($luserDetails);exit;
//echo $luserDetails['Data']['user_data'][0]['employee_id'];exit;
$last_login = date("d-m-Y H:i:s A", strtotime($luserDetails['Data']['user_data'][0]['last_login']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | AerTrip</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url();?>resources/plugins/fontawesome-free/css/all.min.css">
  <!-- fullCalendar -->
  <link rel="stylesheet" href="<?php echo base_url();?>resources/plugins/fullcalendar/main.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url();?>resources/dist/css/adminlte.min.css">
  <link href="<?PHP echo base_url('assets/css/jquery.dataTables.css', PROTOCOL); ?>" rel="stylesheet" type="text/css">

    <link href="<?php echo base_url(); ?>assets/css/noty_theme_default.css" rel="stylesheet">

   <link href="<?PHP echo base_url();?>assets/css/jquery.noty.css" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      
      <li class="nav-item d-none d-sm-inline-block">
        <a href="<?php echo base_url("home/logout");?>" class="nav-link">Logout</a>
      </li>
    </ul>

    
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo base_url();?>" class="brand-link">
      <img src="<?php echo base_url();?>resources/dist/img/logo.jpg" alt="AerTrip Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">AerTrip</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php echo base_url();?>resources/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Admin</a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
    

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          
          <li class="nav-item">
            <a href="<?php echo base_url("home");?>" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                
              </p>
            </a>
          </li>
      
          <li class="nav-item">
            <a href="<?php echo base_url("employee");?>" class="nav-link">
              <i class="nav-icon far fa-image"></i>
              <p>
                Employee
              </p>
            </a>
          </li>
          
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
  <script src="<?php echo base_url('assets/js/jquery-3.3.1.min.js', PROTOCOL); ?>"></script>
<script src="<?php echo base_url('assets/js/jquery.form.js', PROTOCOL); ?>"></script>
<script src="<?php echo base_url('assets/js/jquery.validate.js', PROTOCOL); ?>"></script>

  <script src="<?php echo base_url('assets/js/additional-methods.js', PROTOCOL); ?>"></script>
      <script type="text/javascript" src='<?PHP echo base_url('assets/js/jquery.dataTables.min.js', PROTOCOL); ?>'></script>
  <script type="text/javascript" src='<?PHP echo base_url('assets/js/datatable.js', PROTOCOL); ?>'></script>
      <script type="text/javascript" src="<?PHP echo base_url();?>assets/js/jquery.noty.js"></script>
  <script>
    function setTabIndex()
    {
      var tabindex = 1;
      $('input,select,textarea,.icon-plus,.icon-minus,button,a').each(function() {
        if (this.type != "hidden") {
          var $input = $(this);
          $input.attr("tabindex", tabindex);
          tabindex++;
        }
      });
    }
    
    $(function()
    {
      setTabIndex();
      $(".select2").each(function(){
        $(this).select2({
          placeholder: "Select",
          allowClear: true
        });
        $("#s2id_"+$(this).attr("id")).removeClass("searchInput");
      });
      //document.title = "Home - Commodity Alpha";
      //$(".inline").colorbox({inline:true, width:"50%",  onComplete : function() { 
      //$(this).colorbox.resize(); 
      //} });

      $(".dataTables_filter input.hasDatepicker").change( function () {
        /* Filter on the column (the index) of this element*/ 
        oTable.fnFilter( this.value, oTable.oApi._fnVisibleToColumnIndex(oTable.fnSettings(), $(".searchInput").index(this) ) );
      });
      
      window.scrollTo(0,0);
    });
    
    function displayMsg(type,msg)
    { 
      $.noty({
        text:msg,
        layout:"topRight",
        type:type
      });
    }
  </script>