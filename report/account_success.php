<?php

include_once('php/db.php');
if(!isset($_SESSION)) 
{ 
  session_start(); 
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
	<title>Popsixle Account Success</title>
</head>
<body class="m-3" style="background: #FFFFFF;">
	<a href="/<?php echo $base;?>index.php"><img src="/<?php echo $base;?>img/popsixle_logo2.png" width="205" height="52" style="margin:0 0 10px;" alt="Popsixle Logo" border="0" /></a>
		<!-- <p class="pt-2" style="margin-left:2px;"><b>Back to the good old days.</b></p> -->

	<?php
	if( !empty($_SESSION['user']['email']) ){
		echo '<div id="log_out">Logged in as '.$_SESSION['user']['username'].' | <a href="/'.$base.'php/logout.php">logout</a></div>';
	}	
	?>				
			<div class="p-3 mb-2 card base-card border text-white bg-danger" id="error_block" style="display:none;">
			</div>
			
			<div class="p-3 mb-2  card base-card border text-white bg-success" id="success_block" style="display:none;">
			</div>

			
			<div class="card base-card bg-light border p-3">
				<h5 class="mb-0 " style="color:#AC39FD">Next Step: Email Verification</h5>
				<p>Please check your email and click the verify link we sent you.</p>
				<?php
				echo '	
						<div class="form-group pt-3">
							<label for="" class="blue-text mt-0 mb-1">Your Email Address:</label>
							<input type="text" id="input-email" name="input-email" class="form-control form-control-sm text-muted border rounded px-3 py-1 mb-0 mt-0" value="'.$_SESSION['user']['email'].'" readonly />';
					
					echo '</div>';
				?>
			</div>

			
		</div>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>	
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

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
			
						
			function show_success( msg ){
				
				if(msg == 'beta'){
					msg = 'Welcome to the Popsixle Beta! Your account was created successfully.';
				} else if(msg == 'waitlist'){
					msg = 'Your request for a closed beta invite was received.';
				} else if(msg == 'direct'){
					msg = 'Welcome to Popsixle! Your account was created successfully.';
				}
				
				$('#success_block').html('<p class="mb-0">'+msg+'</p><div class="x-close rounded">x</div>').show();
				$("html, body").animate({ scrollTop: 0 }, "slow");
				
				$( '.x-close' ).click(function(){
					$('#success_block').hide();
				});

			}
			
			function show_error( msg ){
				if(msg == 'poa'){
					msg = 'Oh no! You selected too many wrong answers and need to retry that campaign later. Remember to pay close attention to the content.';
				} else if(msg == 'tab'){
					msg = 'Oh no! You abandoned your campaign and will need to restart from the beginning. Remember to give your full attention.';
				} else if(msg == 'saved'){
					msg = 'Something went wrong. Your account info was not saved.';
				} else {
					msg = 'Something went wrong. Please try again later.';
				}
				
				$('#error_block').html('<p class="mb-0">'+msg+'</p><div class="x-close rounded">x</div>').show();
				$("html, body").animate({ scrollTop: 0 }, "slow");
				
				$( '.x-close' ).click(function(){
					$('#error_block').hide();
				});

			}
			
		</script>
	</body>
</html>