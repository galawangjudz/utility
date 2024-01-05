<div class="left-side-bar">
		<div class="brand-logo">
		<a href="<?php echo base_url ?>admin/?page=head_dashboard">
				<img src="../vendors/images/deskapp-logo-white-svg.png" alt="" class="dark-logo">
				<img src="../vendors/images/deskapp-logo-white-svg.png" alt="" class="light-logo">
			</a>
			<div class="close-sidebar" data-toggle="left-sidebar-close">
				<i class="ion-close-round"></i>
			</div>
		</div>
		<div class="menu-block customscroll">
			<div class="sidebar-menu">
				<ul id="accordion-menu">
					<li class="dropdown">
						<a href="<?php echo base_url ?>heads/?page=head_dashboard" class="dropdown-toggle no-arrow">
							<span class="micon dw dw-house-1"></span><span class="mtext">Dashboard</span>
						</a>
						
					</li>
					<li>
						<a href="<?php echo base_url ?>heads/?page=accounts" class="dropdown-toggle no-arrow">
							<span class="micon dw dw-user-13"></span><span class="mtext">Accounts</span>
						</a>
					</li>
					<?php if ($_SESSION['alogin'] == '10184'):?>
					<li class="dropdown">
						<a href="javascript:;" class="dropdown-toggle">
							<span class="micon dw dw-apartment"></span><span class="mtext"> Adjustments </span>
						</a>
						<ul class="submenu">
							<li><a href="<?php echo base_url ?>heads/?page=adjustments/add_bill">Add Bill</a></li>
							<li><a href="<?php echo base_url ?>heads/?page=adjustments/adjust">Adjustment Bill & Payment</a></li>
							
						</ul>
					</li>
					<?php endif;?>
					<li>
						<a href="<?php echo base_url ?>heads/?page=service_request" class="dropdown-toggle no-arrow">
							<span class="micon dw dw-right-arrow"></span><span class="mtext">Service Request</span>
						</a>
					</li>
					<li>
						<a href="<?php echo base_url ?>heads/?page=get-soa" class="dropdown-toggle no-arrow">
							<span class="micon dw dw-newspaper"></span><span class="mtext">Bill Report (E-SOA)</span>
						</a>
					</li>
					<li>
						<a href="<?php echo base_url ?>heads/?page=report/car_logs" class="dropdown-toggle no-arrow">
							<span class="micon dw dw-newspaper"></span><span class="mtext">CAR Report</span>
						</a>
					</li>
					<li>
				
					<li>
						<div class="dropdown-divider"></div>
					</li>
					<li>
						<div class="sidebar-small-cap">Extra</div>
					</li>
					<li>
						<a href="https://asianland.ph" class="dropdown-toggle no-arrow">
							<span class="micon dw dw-edit-2"></span><span class="mtext">Visit Us</span>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>