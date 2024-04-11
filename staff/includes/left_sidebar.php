<div class="left-side-bar">
		<div class="brand-logo">
			<a href="<?php echo base_url ?>staff/?page=home">
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
						<a href="<?php echo base_url ?>staff/?page=home" class="dropdown-toggle no-arrow">
							<span class="micon dw dw-house-1"></span><span class="mtext">Dashboard</span>
						</a>
						
					</li>
					<li>
						<a href="<?php echo base_url ?>staff/?page=accounts" class="dropdown-toggle no-arrow">
							<span class="micon dw dw-calendar1"></span><span class="mtext">Accounts</span>
						</a>
					</li>
					<li>
						<a href="<?php echo base_url ?>staff/?page=service_request" class="dropdown-toggle no-arrow">
							<span class="micon dw dw-right-arrow"></span><span class="mtext">Service Request</span>
						</a>
					</li>
					<li>
						<a href="<?php echo base_url ?>staff/?page=get-soa" class="dropdown-toggle no-arrow">
							<span class="micon dw dw-calendar1"></span><span class="mtext">Bill Report (E-SOA)</span>
						</a>
					</li>
					<?php if ($_SESSION['user_type'] == 'Cashier'): ?>
					
					<li class="dropdown">
						<a href="javascript:;" class="dropdown-toggle">
							<span class="micon dw dw-apartment"></span><span class="mtext"> Reports </span>
						</a>
						<ul class="submenu">
							<!-- <li><a href="<?php echo base_url ?>staff/?page=report/car_logs">Daily Report</a></li> -->
							<li><a href="<?php echo base_url ?>staff/?page=report/masterlist">For Sending SOA</a></li>
							<li><a href="<?php echo base_url ?>staff/?page=report/car_logs_v2">Daily Report (NEW)</a></li>
							<li><a href="<?php echo base_url ?>staff/?page=report/summary_per_bank">Summary per Online Bank Report</a></li>
							
						</ul>
					</li>
					<?php endif; ?>
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