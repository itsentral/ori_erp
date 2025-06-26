<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $idt->nm_perusahaan." | ".$title; ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?php echo base_url('assets/adminlte/bootstrap/css/bootstrap.min.css') ?>">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/font-awesome/css/font-awesome.min.css') ?>">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/ionicons/css/ionicons.min.css') ?>">
    <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/select2/select2.min.css') ?>">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url('assets/adminlte/dist/css/AdminLTE.min.css') ?>">
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
  <link rel="stylesheet" href="<?php echo base_url('assets/adminlte/dist/css/skins/_all-skins.min.css') ?>">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?php echo base_url('assets/css/styles.css') ?>">
  <!-- jQuery 2.2.3 -->
  <script src="<?php echo base_url('assets/plugins/jQuery/jquery-2.2.3.min.js') ?>"></script>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition">		
	<div class="wrapper">
        <?php if ($this->session->flashdata('error')): ?>
        <p style="color:red"><?= $this->session->flashdata('error') ?></p>
        <?php endif; ?>
		<form method="post" action="" class="login">
            <p class="title text-center">Setup Google Authenticator</p>
             <p class="title text-center">Scan QR Code</p>
                   <img src="<?= $qrCodeUrl ?>" alt="QR Code" center>
                   <!-- <p><strong><?= $secret ?></strong>-->
        </form>
		<footer>
			<font color="white">  
				<p>Copyright &copy; <?php echo $idt->nm_perusahaan.' '.date('Y');?></p>
				<p>This page is loaded for <strong>{elapsed_time}</strong> seconds</p>
			</font>
		</footer>
		</p>
	</div>
	<script  src="<?php echo base_url();?>assets/login/js/index.js"></script>
</body>
</html>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">ALERT</h5>
        
      </div>
      <div class="modal-body text-red h4" id="modal_text">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div id="spinner" class="spinner" style="display:none;">
	<img src="<?php echo base_url('assets/img/loading.gif') ?>" id="img-spinner" alt="Loading...">
</div>


<style>
	
      /* NOTE: The styles were added inline because Prefixfree needs access to your styles and they must be inlined if they are on local disk! */
 body {
    font-family: "Open Sans", sans-serif;
    height: 100vh;
    background: url("<?php echo base_url();?>assets/images/background3.jpg") 50% fixed;
    background-size: cover;
  }

    @keyframes spinner {
      0% {
        transform: rotateZ(0deg);
      }
      100% {
        transform: rotateZ(359deg);
      }
    }
    * {
      box-sizing: border-box;
    }

    .wrapper {
      display: flex;
      align-items: center;
      flex-direction: column;
      justify-content: center;
      width: 100%;
      min-height: 100%;
      padding: 20px;
      background: rgba(4, 40, 68, 0.85);
    }

    .login {
      border-radius: 2px 2px 5px 5px;
      padding: 10px 20px 20px 20px;
      width: 90%;
      max-width: 320px;
      background: #ffffff;
      position: relative;
      padding-bottom: 80px;
      box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.3);
    }
    .login.loading button {
      max-height: 100%;
      padding-top: 50px;
    }
    .login.loading button .spinner {
      opacity: 1;
      top: 40%;
    }
    .login.ok button {
      background-color: #8bc34a;
    }
    .login.ok button .spinner {
      border-radius: 0;
      border-top-color: transparent;
      border-right-color: transparent;
      height: 20px;
      animation: none;
      transform: rotateZ(-45deg);
    }
    .login input {
      display: block;
      padding: 15px 10px;
      margin-bottom: 10px;
      width: 100%;
      border: 1px solid #ddd;
      transition: border-width 0.2s ease;
      border-radius: 2px;
      color: #ccc;
    }
    .login input + i.fa {
      color: #fff;
      font-size: 1em;
      position: absolute;
      margin-top: -47px;
      opacity: 0;
      left: 0;
      transition: all 0.1s ease-in;
    }
    .login input:focus {
      outline: none;
      color: #444;
      border-color: #2196F3;
      border-left-width: 35px;
    }
    .login input:focus + i.fa {
      opacity: 1;
      left: 30px;
      transition: all 0.25s ease-out;
    }
    .login a {
      font-size: 0.8em;
      color: #2196F3;
      text-decoration: none;
    }
    .login .title {
      color: #444;
      font-size: 1.2em;
      font-weight: bold;
      margin: 10px 0 30px 0;
      border-bottom: 1px solid #eee;
      padding-bottom: 20px;
    }
    .login button {
      width: 100%;
      height: 100%;
      padding: 10px 10px;
      background: #2196F3;
      color: #fff;
      display: block;
      border: none;
      margin-top: 20px;
      position: absolute;
      left: 0;
      bottom: 0;
      max-height: 60px;
      border: 0px solid rgba(0, 0, 0, 0.1);
      border-radius: 0 0 2px 2px;
      transform: rotateZ(0deg);
      transition: all 0.1s ease-out;
      border-bottom-width: 7px;
    }
    .login button .spinner {
      display: block;
      width: 40px;
      height: 40px;
      position: absolute;
      border: 4px solid #ffffff;
      border-top-color: rgba(255, 255, 255, 0.3);
      border-radius: 100%;
      left: 50%;
      top: 0;
      opacity: 0;
      margin-left: -20px;
      margin-top: -20px;
      animation: spinner 0.6s infinite linear;
      transition: top 0.3s 0.3s ease, opacity 0.3s 0.3s ease, border-radius 0.3s ease;
      box-shadow: 0px 1px 0px rgba(0, 0, 0, 0.2);
    }
    .login:not(.loading) button:hover {
      box-shadow: 0px 1px 3px #2196F3;
    }
    .login:not(.loading) button:focus {
      border-bottom-width: 4px;
    }

    footer {
      display: block;
      padding-top: 50px;
      text-align: center;
      color: #ddd;
      font-weight: normal;
      text-shadow: 0px -1px 0px rgba(0, 0, 0, 0.2);
      font-size: 0.8em;
    }
    footer a, footer a:link {
      color: #fff;
      text-decoration: none;
    }
	.spinner{
		position	: fixed;		 
		top			: 0; 
		right		: 0;
		bottom		: 0; 
		left		: 0;
		background-color : rgba(255,255,255,0.7);
		
	}
	#img-spinner {
		left: 50%;
		margin-left: -4em;
		font-size: 16px;
		border: .8em solid rgba(218, 219, 223, 1);
		border-left: .8em solid rgba(58, 166, 165, 1);
		animation: spin 1.1s infinite linear;
		
	}
	#img-spinner, #img-spinner:after {
		border-radius: 50%;
		width: 8em;
		height: 8em;
		display: block;
		position: absolute;
		top: 50%;
		margin-top: -4.05em;
	}

	@keyframes spin {
	  0% {
		transform: rotate(360deg);
	  }
	  100% {
		transform: rotate(0deg);
	  }
	}
	
</style>

<script>
setInterval(function() { window.location.reload();}, 120000);

</script>