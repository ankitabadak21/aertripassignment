<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AerTrip</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url();?>resources/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="<?php echo base_url();?>resources/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url();?>resources/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="<?php echo base_url();?>" class="h1"><b>Aer</b>Trip</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to start your session</p>
 
      <form action="" id="form-validate" method="post">
        <div class="input-group mb-3">
          <input type="username" name="username" class="form-control" placeholder="Username">
          
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password">
          
        </div>
        <div id="show_msg" class="success-txt col-md-12" style="text-align:center;"></div>

        <div class="row">
          
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          
        </div>
      </form>

     
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<script src="<?php echo base_url('assets/js/jquery-3.3.1.min.js', PROTOCOL); ?>"></script>
<script src="<?php echo base_url('assets/js/jquery.form.js', PROTOCOL); ?>"></script>
<script src="<?php echo base_url('assets/js/jquery.validate.js', PROTOCOL); ?>"></script>
<!-- jQuery -->
<!-- Bootstrap 4 -->
<script src="<?php echo base_url();?>resources/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url();?>resources/dist/js/adminlte.min.js"></script>
</body>
</html>

<script type="text/javascript">
  var vRules = {
    username: {
      required: true
    },
    password: {
      required: true
    }
  };
  var vMessages = {
    username: {
      required: "Please enter username."
    },
    password: {
      required: "Please enter password."
    }
  };

  $("#form-validate").validate({
    rules: vRules,
    messages: vMessages,
    submitHandler: function(form) {
      var act = "<?php echo base_url(); ?>login/loginvalidate";
      $("#form-validate").ajaxSubmit({
        url: act,
        type: 'POST',
        dataType: 'JSON',
        cache: false,
        clearForm: false,
        success: function(response) {
          // var res = eval('('+response+')');
          //alert("jlf: "+ res['success']);
          if (response.success) {
            $("#show_msg").html('<span style="color:#339900;">' + response.msg + '</span>');
            setTimeout(function() {
              window.location = "<?php echo base_url(); ?>home";
            }, 2000);

          } else {
            $("#show_msg").html('<span style="color:#ff0000;">' + response.msg + '</span>');
            return false;
          }
        }
      });
    }
  });
  document.title = "Login";
</script>
