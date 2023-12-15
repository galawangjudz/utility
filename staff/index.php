<?php require_once('../includes/config.php'); ?>
<?php include('includes/header.php')?>
<?php include('../includes/session.php');?>
<?php include('includes/staff_session.php')?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
<body>
<!-- 	<div class="pre-loader">
		<div class="pre-loader-box">
			<div class="loader-logo"><img src="../vendors/images/deskapp-logo-svg.png" alt=""></div>
			<div class='loader-progress' id="progress_div">
				<div class='bar' id='bar1'></div>
			</div>
			<div class='percent' id='percent1'>0%</div>
			<div class="loading-text">
				Loading...
			</div>
		</div>
	</div> -->

	<?php include('includes/navbar.php')?>

	<?php include('includes/right_sidebar.php')?>

	<?php include('includes/left_sidebar.php')?>

	<div class="mobile-menu-overlay"></div>

	<?php $page = isset($_GET['page']) ? $_GET['page'] : 'home';  ?>
		<section class="content">
		
			
				<?php 
				if(!file_exists($page.".php") && !is_dir($page)){
					include '404.html';
				}else{
					if(is_dir($page))
					include $page.'/index.php';
					else
					include $page.'.php';

				}
				?>
			
		</section>
		<div class="modal fade" id="confirm_modal" role='dialog'>
			<div class="modal-dialog modal-md modal-dialog-centered rounded-0" role="document">
			<div class="modal-content">
				<div class="modal-header">
				<h5 class="modal-title">Confirmation</h5>
			</div>
			<div class="modal-body">
				<div id="delete_content"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary btn-flat" id='confirm' onclick="">Continue</button>
				<button type="button" class="btn btn-secondary btn-flat" data-dismiss="modal">Close</button>
			</div>
			</div>
			</div>
		</div>
		<div class="modal fade rounded-0" id="uni_modal" role='dialog'>
			<div class="modal-dialog modal-md modal-dialog-centered rounded-0" role="document">
			<div class="modal-content rounded-0">
				<div class="modal-header rounded-0">
				<h5 class="modal-title"></h5>
			</div>
			<div class="modal-body rounded-0">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary btn-flat" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
				<button type="button" class="btn btn-secondary btn-flat" data-dismiss="modal">Cancel</button>
			</div>
			</div>
			</div>
		</div>
		<div class="modal fade rounded-0" id="uni_modal_ticket" role='dialog'>
			<div class="modal-dialog modal-md modal-dialog-centered rounded-0" role="document">
			<div class="modal-content rounded-0">
				<div class="modal-header rounded-0">
				<h5 class="modal-title"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span class="fa fa-close"></span>
				</button>
			</div>
			<div class="modal-body rounded-0">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary btn-flat" id='submit' onclick="$('#uni_modal_ticket form').submit()">Save</button>
				<button type="button" class="btn btn-secondary btn-flat" data-dismiss="modal">Cancel</button>
			</div>
			</div>
			</div>
		</div>
		<div class="modal fade rounded-0" id="uni_modal_right" role='dialog'>
			<div class="modal-dialog modal-full-height  modal-md rounded-0" role="document">
			<div class="modal-content">
				<div class="modal-header">
				<h5 class="modal-title"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span class="fa fa-arrow-right"></span>
				</button>
			</div>
			<div class="modal-body">
			</div>
			</div>
			</div>
		</div>
		<div class="modal fade rounded-0" id="uni_modal_payment" role='dialog'>
			<div class="modal-dialog modal-md modal-dialog-centered rounded-0" role="document">
			<div class="modal-content rounded-0">
				<div class="modal-header rounded-0">
				<h5 class="modal-title"></h5>
			</div>
			<div class="modal-body rounded-0">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary btn-flat" id='submit' onclick="$('#uni_modal_payment form').submit()">Save & Print</button>
				<button type="button" class="btn btn-secondary btn-flat" data-dismiss="modal">Cancel</button>
			</div>
			</div>
			</div>
		</div>
		<div class="modal fade" id="uni_modal_2" role='dialog'>
			<div class="modal-dialog modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-header">
				<h5 class="modal-title"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span class="fa fa-times"></span>
				</button>
			</div>
			<div class="modal-body" style="max-height: 1200px; overflow-y: auto;">
			
			</div>
			</div>
			</div>
		</div>
		<div class="modal fade rounded-0" id="viewer_modal" role='dialog'>
			<div class="modal-dialog modal-md rounded-0" role="document">
			<div class="modal-content">
					<button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
					<img src="" alt="">
			</div>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
	<!-- js -->
	<?php include('includes/scripts.php')?>
</body>
</html>