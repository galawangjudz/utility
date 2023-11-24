
<?php


$l_site = isset($_GET["phase"]) ? $_GET["phase"] : '';
$l_block = isset($_GET["block"]) ? $_GET["block"] : '';
$l_lot = isset($_GET["lot"]) ? $_GET["lot"] : '' ;

$l_acct_no = isset($_GET["acct_no"]) ? $_GET["acct_no"] : '' ;

if ($l_acct_no != ''){
    $l_find = $l_acct_no;
    
}else{
    if ($l_block == ''):
        $l_find = sprintf("%03d", (int)$l_site);
    else:
        
        if ($l_lot == ''):
            $l_find = sprintf("%03d%03d", (int)$l_site, (int)$l_block);	
        else:
            $l_find = sprintf("%03d%03d%02d", (int)$l_site, (int)$l_block, (int)$l_lot);
            
        endif;
    endif;
}

?>

	<div class="main-container">
		<div class="pd-ltr-20">
			    <div class="card-box mb-30">
                    <div class="pd-20">
                       <!--  <h4 class="text-blue h4">Search</h4> -->
                       <form action="" id="filter">
                            <div class="row align-items-end">
                                <input type="hidden" id="page" name="page" value="accounts" class="form-control form-control-sm rounded-0">
                            
                                <div class="col-md-3 form-group">
                                    <label for="acct_no" class="control-label">Account #</label>
                                    <input type="text" id="acct_no" name="acct_no" value="<?= $l_acct_no ?>" class="form-control" maxlength="11">
                                </div>

                                <div class="col-md-3 form-group">
                                    <button class="btn btn-primary"><i class="dw dw-search"></i> Find Account</button>
                                    <!-- <button class="btn btn-default border btn-flat btn-sm" id="print" type="button"><i class="fa fa-print"></i> Print</button> -->
                                </div>
                            </div>
                        </form>

                        <form action="" id="filter">
                        <div class="row align-items-end">
                            <input type="hidden" id="page" name="page" value="accounts" class="form-control form-control-sm rounded-0">
                        
                       
                            <div class="col-md-2 form-group">
                                
                                <label for="phase" class="control-label">Phase</label>
                                <select name="phase" id="phase" class="custom-select form-control" autocomplete="off">
                                  <?php
                                
                                    $sql = "SELECT * FROM t_projects ORDER BY c_acronym";
                                    $results = odbc_exec($conn2, $sql);


                                    $selectedValue = isset($_GET['phase']) ? $_GET['phase'] : ''; // Get the selected value from the submitted form

                                    
                                   
                                    echo '<option value="" selected>--SELECT--</option>';
                                    echo '<option value="100">ALL</option>';
                                    while ($row = odbc_fetch_array($results)) {
                                        $optionValue = $row['c_code'];
                                        $optionText = $row['c_acronym'];
                                        $selected = ($selectedValue == $optionValue) ? 'selected' : ''; // Check if this option is selected
                                        echo '<option value="' . $optionValue . '" ' . $selected . '>' . $optionText . '</option>';
                                    }
                                    echo '</select>';
                                        
                                    
                                ?>
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="block" class="control-label">Block</label>
                                <input type="number" id="block" name="block" value="<?= $l_block ?>" class="form-control">
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="lot" class="control-label">Lot</label>
                                <input type="number" id="lot" name="lot" value="<?= $l_lot ?>" class="form-control">
                            </div>
                            <div class="col-md-3 form-group">
                                <button class="btn btn-primary"><i class="dw dw-search"></i> Search Location</button>
                                <!-- <button class="btn btn-default border btn-flat btn-sm" id="print" type="button"><i class="fa fa-print"></i> Print</button> -->
                            </div>
                        </div>
                        </form>
                    
					</div>
				</div>
                
               

			<div class="card-box mb-30">
				<div class="pd-20">
						<h2 class="text-blue h4">List of Accounts</h2>
                      
					</div>
				<div class="pb-20">
					<table class="data-table table stripe hover nowrap">
						<thead>
							<tr>
								<th class="table-plus">No</th>
                                <th>Account No</th>
                                <th>Phase Block Lot</th>
                                <th>Full name</th>
                                <th>Types</th>
                                <th>Status</th>
								<th class="datatable-nosort">ACTION</th>
							</tr>
						</thead>
						<tbody>
                   
							<tr>
								
                            <?php 
                                $i = 1;
                            
                               /*  if ($l_find == "100"):
                                    $l_find = '1';
                                endif; */
                              
                                if ($l_find == "00000000" & $l_acct_no == ""):
                                    $sql = "SELECT c_control_no, c_account_no, c_location, c_first_name, c_last_name, c_types, c_status FROM t_utility_accounts WHERE c_status = 'Active' ORDER BY c_date_applied DESC limit 10 ";
                                else:
                                    $sql = "SELECT c_control_no, c_account_no, c_location, c_first_name, c_last_name, c_types, c_status FROM t_utility_accounts WHERE c_account_no::text ~* '^%s' ORDER BY c_account_no";
                                endif;
                                $sql = sprintf($sql, $l_find);

                                $qry = odbc_exec($conn2,$sql);
                                while(odbc_fetch_row($qry)):
                                    $ctr = odbc_result($qry, "c_control_no");
                                    $acc = odbc_result($qry, "c_account_no");
                                    $loc = odbc_result($qry, "c_location");
                                    $fname = odbc_result($qry, "c_first_name");
                                    $lname = odbc_result($qry, "c_last_name");
                                    $types = odbc_result($qry, "c_types");
                                    $status = odbc_result($qry, "c_status");
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++; ?></td>
                                    <td class=""><?php echo $acc ?></td>
                                    <td class=""><?php echo $loc ?></td>
                                    <td class=""><?php echo $lname . ','. $fname ?></td>
                                    <td class=""><?php
                                        if ($types == 'STL and MTF') {
                                            echo 'STL and GCF';
                                        } else {
                                            echo $types;
                                        }
                                    ?></td>
                            
                                    <td class="text-center">
                                        <?php 
                                            switch($status){
                                                case 'Inactive':
                                                    echo '<span class="badge badge-danger bg-gradient-danger px-3 rounded-pill">Inactive</span>';
                                                    break;
                                                case 'Active':
                                                    echo '<span class="badge badge-primary bg-gradient-primary px-3 rounded-pill">Active</span>';
                                                    break;
                                                default:
                                                    echo '<span class="badge badge-default border px-3 rounded-pill">N/A</span>';
                                                        break;
                                            }
                                        ?>
                                    </td>
                                    <td>
                                    <div class="dropdown">
                                        <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                            <i class="dw dw-more"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                            <a class="dropdown-item view_data" id ="<?php echo $acc ?>"><i class="dw dw-eye"></i> View</a>
                                            <a class="dropdown-item soa_data" id ="<?php echo $acc ?>"><i class="dw dw-file-4"></i> GCF & STL Records</a>
                                            <a class="dropdown-item stl_bill_data" id ="<?php echo $acc ?>" bill_type ="STL" ><i class="dw dw-light-bulb"></i> StreetLight Records</a>
                                            <a class="dropdown-item mtf_bill_data" id ="<?php echo $acc ?>" bill_type ="MTF" ><i class="dw dw-scissors"></i> Grass-Cutting Records</a>
                                              <a class="dropdown-item payment_data" id ="<?php echo $acc ?>"><i class="dw dw-wallet"></i> Payment Window</a>
                                           <!--  <a class="dropdown-item edit_data" href="javascript:void(0)" id ="<?php echo $acc ?>"><i class="dw dw-edit2"></i> Edit</a> -->
                                          
                                        </div>
                                    </div>
        

                                    </td>
                                </tr>
                            <?php endwhile; ?>
						</tbody>
					</table>
			   </div>
			</div>
<style>
.modal-dialog.large {
    width: 80% !important;
    max-width: unset;
}

.modal-dialog.mid-large {
    width: 50% !important;
    max-width: unset;
}
</style>

<script>
    $(document).ready(function(){


        $('#create_new').click(function(){
			uni_modal("Add New Account","accounts/manage_account.php",'mid-large')
		})
        $('.soa_data').click(function(){
			uni_modal_2("Due and Payment Details", "soa/statement.php?id=" + $(this).attr('id'), 'large');
		})
		/* $('.stl_bill_data').click(function(){
			uni_modal_2("Due and Payment Details", "soa/stl_payment_record.php?id=" + $(this).attr('id') + "&bill_type=" + $(this).attr('bill_type'), 'large');
		}) */
        $('.stl_bill_data').click(function(){
			uni_modal_2("STL Due and Payment Details", "soa/statement_stl.php?id=" + $(this).attr('id') + "&bill_type=" + $(this).attr('bill_type'), 'large');
		})
		/* $('.mtf_bill_data').click(function(){
			uni_modal_2("Due and Payment Details", "soa/mtf_payment_record.php?id=" + $(this).attr('id') + "&bill_type=" + $(this).attr('bill_type'), 'large');
		}) */
        $('.mtf_bill_data').click(function(){
			uni_modal_2("GCF Due and Payment Details", "soa/statement_gcf.php?id=" + $(this).attr('id') + "&bill_type=" + $(this).attr('bill_type'), 'large');
		})
      
        $('.payment_data').click(function(){
			uni_modal_payment("Utility Payment Window","payments/index.php?id="+$(this).attr('id'),'mid-large')
		})
		$('.delete_data').click(function(){
			_conf("Are you sure to delete '<b>"+$(this).attr('data-name')+"</b>' from Accounts List permanently?","delete_account",[$(this).attr('data-id')])
		})
		$('.view_data').click(function(){
			uni_modal("Account Details","accounts/manage_account.php?id="+$(this).attr('id'),'mid-large')
		})

    })

    function delete_account($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_account",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
                    alert(resp.msg);
					location.reload();
				}else{
					alert("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>