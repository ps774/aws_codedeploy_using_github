<?php
if(!isset($_SESSION)) 
{ 
  session_start(); 
}  

include('php/db.php');
if (!empty($_SESSION['user'])){
	if( $_SESSION['user']['status'] == 'logged_in' && !empty($_SESSION['user']['email']) && $_SESSION['user']['type'] == '99' ){
		$mode = 1;
		header('Location: /'.$base.'admin.php');
		exit();
	} else if( $_SESSION['user']['status'] == 'logged_in' && !empty($_SESSION['user']['email']) ){
		$mode = 1;
		header('Location: /'.$base.'account.php');
		exit();
	} else {
		//var_dump($_SESSION);
		
	}
}
?>
<html>
<head>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<style>
.x-close{
	position: absolute;
    right: 0;
    top: 0px;
    padding: 1 7px;
}

.x-close:hover{
	cursor: pointer;
	background: #7a0000;
}

#log_out{
	position: fixed;
	right:5px;
	top: 5px;
	padding: 5px 10px;
	font-size:small;
	z-index: 10 !important;
	background:#FFFFFF;
}

.too-small-hide{
	display: none;
}

 @media (min-width: 769px) {
	.too-small-hide{
		display: block;
	}	 
}
a {
	color: #AC39FD;
}
.cust-btn {
	background: #AC39FD;
	color: white;
	font-weight: 600;
}
.cust-btn:hover {
	background: #761ab8;
	color: white;
}
.checkbox:checked:after{
	background-color: #AC39FD;
}
	</style>
	<link rel="apple-touch-icon" sizes="180x180" href="/<?php echo $base;?>favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/<?php echo $base;?>favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/<?php echo $base;?>favicon/favicon-16x16.png">
	<link rel="manifest" href="/<?php echo $base;?>favicon/site.webmanifest">
	<link rel="mask-icon" href="/<?php echo $base;?>favicon/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="content-type" content="text/html; charset=utf-8 ;">
	<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
	<title>Popsixle Account Login</title>
</head>
<body class="m-3" style="background: #FFFFFF;">
	<a href="/<?php echo $base;?>index.php"><img src="/<?php echo $base;?>img/popsixle_logo2.png" width="205" height="52" style="margin:0 0 10px;" alt="Popsixle Logo" border="0" /></a>

	<!-- <p class="pt-2" style="margin-left:2px;"><b>Back to the good old days.</b></p> -->
	
	<?php
	if( !empty($_SESSION['user']['email']) ){
		echo '<div id="log_out">'.$_SESSION['user']['username'].' | <a href="/'.$base.'php/logout.php">logout</a></div>';
	}	
	?>
	
	<div class="p-3 mb-2 card base-card border text-white bg-danger" id="error_block" style="display:none;">
	</div>
	
	<div class="p-3 mb-2  card base-card border text-white bg-success" id="success_block" style="display:none;">
	</div>
	
	<ul class="nav nav-tabs" id="tab_nav" role="tablist" style="border-color: #CCC;">
		<li class="nav-item" role="presentation">
			<button  style="border-color: #CCC;" class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#account_login" type="button" role="tab" aria-controls="account_login" aria-selected="false"><small>Account Login</small></button>
		</li>
		<?php
		if( empty($_GET['q'])){
			echo '<!--';
		}	
		?>
		<li class="nav-item" role="presentation">
			<button style="border-color: #CCC;" class="nav-link" id="create-tab" data-bs-toggle="tab" data-bs-target="#create_account" type="button" role="tab" aria-controls="create_account" aria-selected="true"><small>Create Account</small></button>
		</li>
		<?php
		if( empty($_GET['q'])){
			echo '-->';
		}	
		?>
	</ul>
	<div class="bg-white tab-content p-3 border-left border-right border-bottom rounded-bottom" style="border-color: #CCC !important;" id="tab_content">
		<?php
		if( empty($_GET['q'])){
			echo '<!--';
		}	
		?>
		<div class="tab-pane fade" id="create_account" role="tabpanel" aria-labelledby="create-tab">
			
			<form id="createAccount" action="/<?php echo $base;?>php/beta_invite.php" method="post" autocomplete="off">
				<input autocomplete="false" name="hidden" type="text" style="display:none;">
			  	<div class="form-group">
			    	<label for="full_name" style="color:#AC39FD"><b>Full Name:</b></label>
					<input type="full_name" class="form-control" autocomplete="Name" id="full_name" name="full_name" placeholder="Enter your Full Name..." minlength="2" required>
			  	</div>
			  	<div class="form-group">
			    	<label for="email" style="color:#AC39FD"><b>Email:</b></label>
					<input type="email" class="form-control" autocomplete="email" id="email" name="email" placeholder="Enter your email address..." minlength="6" required email="true">
			  	</div>
			  	<div class="form-group">
			    	<label for="email_again" style="color:#AC39FD"><b>Confirm Email:</b></label>
					<input type="text" class="form-control" autocomplete="email" id="email_again" name="email_again" placeholder="Enter the same email again..." minlength="6" required email="true">
			  	</div>
			  	
			  	<div class="form-check">
					<input type="checkbox" class="form-check-input checkbox" id="terms_box" name="terms_box" required>
					<label for="terms_box" class="error" style="display:none;"></label>
					<label class="form-check-label" for="terms_box">I agree to the <a href="/<?php echo $base;?>access_agreement.php" target=_blank>Access Agreement</a></label>
					
			  	</div>
			  
			  	
			  	<div class="text-center row px-2 mt-3">
				  	
						<div class="col px-1 too-small-hide"></div>
						<div class="col px-1">
							<button type="submit" class="btn btn-block cust-btn text-center">Create Account</button>
						</div>
						<div class="col px-1 too-small-hide"></div>
			  	</div>
			  	<div class="text-center">
			  		<p class="card-text small" style="padding-top:20px;">Your privacy matters to us.<br/>Before creating a beta account, please closely read our <a href="/<?php echo $base;?>terms_of_service.php" target=_blank>Terms of Service</a> and <a href="/<?php echo $base;?>privacy.php" target=_blank>Privacy Policy</a>.</p>
			  	</div>
			</form>			
		</div>
		<?php
		if( empty($_GET['q'])){
			echo '-->';
		}	
		?>
		<div class="tab-pane fade show active" id="account_login" role="tabpanel" aria-labelledby="login-tab">
<form id="login" action="/<?php echo $base;?>php/login.php" method="post">
			  	<div class="form-group">
			    	<label for="user_email" style="color:#AC39FD"><b>Email:</b></label>
					<input type="user_email" class="form-control" autocomplete="email" id="user_email" name="user_email" placeholder="Enter your email address..." minlength="6" required email="true">
			  	</div>
			  	<div class="form-group">
			    	<label for="password" style="color:#AC39FD"><b>Password:</b></label>
					<input type="password" autocomplete="current-password" class="form-control" id="password" name="password" placeholder="Enter your password..." required>
			  	</div>
			  	
			  	<div class="text-center row px-2" style="">
					<div class="col px-1 too-small-hide"></div>
					<div class="col px-1">
						<button type="submit" class="btn btn-block cust-btn text-center">Log In</button>
					</div>
					<div class="col px-1 too-small-hide"></div>
				</div>
				<div class="text-center">
			  		<p class="card-text small" style="padding-top:20px;">Your privacy matters to us.<br/>Before creating a beta account, please closely read our <a href="/<?php echo $base;?>terms_of_service.php">Terms of Service</a> and <a href="/<?php echo $base;?>privacy.php">Privacy Policy</a>.</p>
			  	</div>
				<!--<p class="mt-2 mb-3 mx-2"><small>Forgot your password? <a href="/'.$base.'password_reset.php">Click to reset it.</a></small></p>--> 
		
			</form>
		</div>
	</div>
	
	<div class="text-center bg-light p-3 border rounded mt-3" style="border-color: #CCC !important;" id="">
		<h6 class="">Have questions about Popsixle?</h6>
		<p><small>To find out more about Popsixle's services and to speak with someone from our team, visit the <a href="https://popsixle.com/pages/contact">Contact Us</a> page on our main site, <a href="https://popsixle.com">popsixle.com</a>.</small></p>
	</div>
	

	
	


<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>	
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.min.js" crossorigin="anonymous"></script>
<script>
	
	var base = "<?php echo $base;?>";
			
	$(function() {
		<?php
	
		if( !empty($_GET['success']) ) {
			echo '
			show_success( \''.$_GET['success'].'\' );';	
			
		}
		
		if( !empty($_GET['error']) ) {
			echo '
			show_error( \''.$_GET['error'].'\' );';	
		}
			
		if( !empty($_GET['mode']) ) {
			echo '
			$("#create-tab").removeClass("active");
		$("#login-tab").addClass("active");
		
		$("#create_account").removeClass("show active");
		$("#account_login").addClass("show active");
		';	
			
		}
		?>
	});
	
	$('#create-tab').click(function(){
		$('#create-tab').addClass('active');
		$('#login-tab').removeClass('active');
		
		$('#create_account').addClass('show active');
		$('#account_login').removeClass('show active');
	});
	
	$('#login-tab').click(function(){
		$('#create-tab').removeClass('active');
		$('#login-tab').addClass('active');
		
		$('#create_account').removeClass('show active');
		$('#account_login').addClass('show active');
	});
	
	function show_success( msg ){
		
		$('#success_block').html('<p class="mb-0">'+msg+'</p><div class="x-close rounded">x</div>').show();
		$("html, body").animate({ scrollTop: 0 }, "slow");
		
		$( '.x-close' ).click(function(){
			$('#success_block').hide();
		});

	}
	
	function show_error( msg ){
		
		$('#error_block').html('<p class="mb-0">'+msg+'</p><div class="x-close rounded">x</div>').show();
		//$("html, body").animate({ scrollTop: 0 }, "slow");
		
		$( '.x-close' ).click(function(){
			$('#error_block').hide();
		});

	}
	
	
			
		jQuery.validator.addMethod("alphanumeric", function (value, element) {
		  if (/^[A-Za-z0-9_\-]+$/i.test(value)) {
		      return true;
		  } else {
		      return false;
		  };
		}, "Numbers, letters, and dashes only");

		
		$('#createAccount').validate({
				rules: {
					email: {
						remote: {
							url: "/"+base+"php/validate_email.php",
					        type: "post",
					        data: {
					        	username: function() {
					            	return document.getElementById("email").value;
					          	}
					        }
						}
					},
					email_again: {
						equalToIgnoreCase: "#email"
					}
				},
				messages: {
					email: {
						remote: "This email is already registered.<br/>Try another email or <a href='/"+base+"index.php'>login</a> to your existing account."
					}
				}
			}
		);
		
		$.validator.addMethod("equalToIgnoreCase", function (value, element, param) {
		    return this.optional(element) || 
		    (value.toLowerCase() == $(param).val().toLowerCase());
		});


</script>
</body>
</html>

