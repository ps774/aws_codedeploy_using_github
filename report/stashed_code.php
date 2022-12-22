<div class="card base-card bg-light border p-3">
				<div class="">
					<h4 class="my-2  pb-2" style="color:#AC39FD"><b>Meta Ad Account Settings</b></h4>
				</div>
				<form id="fb_ad_settings" action='/<?php echo $base;?>admin_shop_account.php?shop=<?php echo$_GET["shop"];?>' method="post" autocomplete="off">
					<input autocomplete="false" name="hidden" type="text" style="display:none;">
					
						<div class="form-group">
				    	<label for="fb_ad_account_id" class="blue-text"><b>Account fb_ad_account_id</b></label>
						<input type="text" class="form-control" id="fb_ad_account_id" name="fb_ad_account_id" placeholder="Enter Meta Account ID" <?php 
							if( !empty($db_data[0]['fb_ad_account_id']) ){
								echo 'value="'.$db_data[0]['fb_ad_account_id'].'"';
							}
						?>>
						<div class="explanation_text">Text Here</div> 
				  	</div>
						<div class="form-group">
				    	<label for="install_date" class="blue-text"><b>Account Install Date</b></label>
						<input type="text" class="form-control" id="install_date" name="install_date" placeholder="Enter Meta Account ID" <?php 
							if( !empty($db_data[0]['install_date']) ){
								echo 'value="'.$db_data[0]['install_date'].'"';
							}
						?>>
						<div class="explanation_text">Text Here</div> 
				  	</div>
			  	
				  	<div class="text-center row px-2">
					  	
							<div class="col px-1 too-small-hide"></div>
							<div class="col px-1">
								<button type="submit" class="btn btn-block cust-btn text-center">Update Meta Account Settings</button>
							</div>
							<div class="col px-1 too-small-hide"></div>
				  	</div>
				</form>
			</div>
