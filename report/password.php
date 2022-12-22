<?php
	include_once('php/db.php');
if(!isset($_SESSION)) 
{ 
  session_start(); 
} 

if( $_SESSION['user']['status'] == 'logged_in' && !empty($_SESSION['user']['email']) ){
	$mode = 1;
} else {
	//var_dump($_SESSION);
	header('Location: /'.$base.'index.php');
	exit();
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
	</style>
	<link rel="apple-touch-icon" sizes="180x180" href="/<?php echo $base;?>favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/<?php echo $base;?>favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/<?php echo $base;?>favicon/favicon-16x16.png">
	<link rel="manifest" href="/<?php echo $base;?>favicon/site.webmanifest">
	<link rel="mask-icon" href="/<?php echo $base;?>favicon/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Popsixle Password</title>
</head>
<body class="m-3" style="background: #FFFFFF;">
	<a href="/<?php echo $base;?>index.php"><img src="/<?php echo $base;?>img/popsixle_logo2.png" width="205" height="52" style="margin:0 0 10px;" alt="Popsixle Logo" border="0" /></a>
		<!-- <p class="pt-2" style="margin-left:2px;"><b>Back to the good old days.</b></p> -->

					
			<div class="p-3 mb-2 card base-card bg-light border text-white bg-danger" id="error_block" style="display:none;">
			</div>
			
			<div class="p-3 mb-2  card base-card bg-light border text-white bg-success" id="success_block" style="display:none;">
			</div>

			
			<div class="card base-card bg-light border p-3">
				<div class="text-center">
					<h5 class="my-2 " style="color:#AC39FD">Set Account Password</h4>
				</div>
				<form id="changePassword" action="/<?php echo $base;?>php/change_password.php" method="post" autocomplete="off">
					<input autocomplete="false" name="hidden" type="text" style="display:none;">
				  	
				  	<?php
					  	
					//is it a beta user?
					if( $mode == 1 ){
						echo '<input type="hidden" id="mode" name="mode" value="1">';
						echo '
						<div class="form-group">
					    	<label for="password" class="blue-text">Your Email:</label>
							<input class="form-control" id="email" name="email" value="'.$_SESSION['user']['email'].'" readonly>
					  	</div>
				  		';
					} else if( $mode >= 2 ){
						echo '<input type="hidden" id="mode" name="mode" value="2">';
						echo '<input type="hidden" id="hash" name="hash" value="'.$passed_hash.'">';
						echo '
						<div class="form-group">
					    	<label for="password" class="blue-text">Account Email:</label>
							<input class="form-control" id="email" name="email" placeholder="What\'s your email?">
					  	</div>
				  		';
					}	

					?>
					
				  	
				  	<div class="form-group">
				    	<label for="password" class="blue-text">New Password:</label>
						<input type="password" class="form-control" id="password" name="password" placeholder="Enter a strong password..." minlength="5" required autocomplete="new-password">
				  	</div>
				  	<div class="form-group">
				    	<label for="password_again" class="blue-text">Confirm Password:</label>
						<input type="password" class="form-control" id="password_again" name="password_again" placeholder="Enter the same password again..." autocomplete="new-password">
				  	</div>
				  	<div class="text-center row px-2">
					  	
							<div class="col px-1 too-small-hide"></div>
							<div class="col px-1">
								<button type="submit" class="btn btn-block btn-primary text-center">Set Password</button>
							</div>
							<div class="col px-1 too-small-hide"></div>
				  	</div>
				</form>
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
					
				?>
			});
			
			$('#changePassword').validate({
				rules: {
					password: "required",
					password_again: {
						equalTo: "#password"
					}
				}
			});

			
			function show_success( msg ){
				
				if(msg == 'beta'){
					msg = 'Welcome to the Popsixle Beta! Your account was created successfully.';
				} 
				
				$('#success_block').html('<p class="mb-0">'+msg+'</p><div class="x-close rounded">x</div>').show();
				//$("html, body").animate({ scrollTop: 0 }, "slow");
				
				$( '.x-close' ).click(function(){
					$('#success_block').hide();
				});

			}
			
			function show_error( msg ){
				if(msg == 'poa'){
					msg = 'Oh no! You selected too many wrong answers and need to retry that campaign later. Remember to pay close attention to the content.';
				} else {
					msg = 'Something went wrong. Please try again later.';
				}
				
				$('#error_block').html('<p class="mb-0">'+msg+'</p><div class="x-close rounded">x</div>').show();
				//$("html, body").animate({ scrollTop: 0 }, "slow");
				
				$( '.x-close' ).click(function(){
					$('#error_block').hide();
				});

			}
					
		</script>

	</body>
</html>