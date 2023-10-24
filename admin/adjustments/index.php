<?php
$l_find = isset($_GET["search"]) ? $_GET["search"] : '';

?>


<div class="main-container"> 
	<div class="">
		<div class="pd-ltr-20">
			<div class="title pb-20">
                <h2 class="h3 mb-0">Adjustment of Accounts</h2>
            </div>
			<div class="card-box pd-20 height-1400-p mb-30">
				<form action="" id="filter">
					<input type="hidden" id="page" name="page" value="adjustments" class="form-control form-control-sm rounded-0">
					<div class="col-md-4 form-group">
						<label for="search" class="control-label">Search Account: </label>
						<input type="text" id="search" name="search" class="form-control">
                    </div>
					<div class="col-md-4 form-group">
						<button class="btn btn-primary"><i class="dw dw-search"></i> Search</button>
                    </div>
                </form>		
			</div>

			<div class="card-box mb-30">
				<div class="pd-20">
						<h2 class="text-blue h4">Bill of Accounts</h2>
                      
				</div>
				<div class="pb-20">
					<table class="data-table table stripe hover nowrap">
						<thead>
							<tr>
                                <th class="table-plus">Start Date</th>
                                <th>End Date</th>
                                <th>Due Date</th>
                                <th>Description</th>
                                <th>Amount Due</th>
								<th class="datatable-nosort">ACTION</th>
							</tr>
						</thead>
						<tbody>
                   
							<tr>
								
                            <?php 
							
								if(isset($_GET['search']) && $_GET['search']){
									$sql = "SELECT c_account_no, c_start_date, c_end_date, c_due_date, c_bill_type, c_amount_due FROM t_utility_bill WHERE c_account_no = '%s' ORDER BY c_account_no";
									$sql = sprintf($sql, $l_find);
								}else{
									$sql = "SELECT c_account_no, c_start_date, c_end_date, c_due_date, c_bill_type, c_amount_due FROM t_utility_bill WHERE c_account_no = '1' ORDER BY c_account_no";
								}
                               
			                    
                                $qry = odbc_exec($conn2,$sql);


                                while(odbc_fetch_row($qry)):
									$acc = odbc_result($qry, "c_account_no");
                                    $start = odbc_result($qry, "c_start_date");
                                    $end = odbc_result($qry, "c_end_date");
                                    $due = odbc_result($qry, "c_due_date");
                                    $type = odbc_result($qry, "c_bill_type");
                                    $amount = odbc_result($qry, "c_amount_due");
               
                            ?>
                                <tr>
                                    <td class=""><?php echo $start; ?></td>
                                    <td class=""><?php echo $end ?></td>
                                    <td class=""><?php echo $due ?></td>
                                    <td class=""><?php echo $type ?></td>
                                    <td class=""><?php echo $amount ?></td>
                            
                        
                                    <td>
                                    <div class="dropdown">
                                        <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                            <i class="dw dw-more"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                            <a class="dropdown-item view_data" id ="<?php echo $acc ?>"><i class="dw dw-eye"></i> View</a>
                                            <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $acc ?>"><i class="dw dw-delete-3"></i> Delete</a>
                                        </div>
                                    </div>
                                

                                    </td>
                                </tr>
                            <?php endwhile; ?>
						</tbody>
					</table>
			   </div>
			</div>
			
		</div>
	</div>