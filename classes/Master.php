<?php

Class Master{
	private $conn2;

    public function __construct() {
        require_once('../includes/config.php');
        $this->conn2 = odbc_connect($dsn, $user, $pass);
    }

	function add_ticket(){
		extract($_POST);
		$request=$_POST['request']; 
		$dept=$_POST['department']; 
		$acc_no=$_POST['account_no'];  
		$acc_no_to=isset($_POST['account_no_to']) ? $_POST['account_no_to'] : NULL; 
		$adjust_for=$_POST['adjust_for'];  
		$adjust_to=isset($_POST['adjust_to']) ? $_POST['adjust_to'] : NULL;   
		$amount = isset($_POST['amount']) ? $_POST['amount'] : NULL; 
		$gcf_edate = isset($_POST['gcf_edate']) ? $_POST['gcf_edate'] : NULL;  
		$priority=$_POST['priority'];  
		$purpose=$_POST['purpose'];
		$attachment_path=  isset($_POST['attachment_path']) ? $_POST['attachment_path'] : NULL;
	

		if (empty($acc_no) || empty($purpose) || empty($priority) || empty($dept) || empty($adjust_for)) {
			$resp['status'] = 'error';
			$resp['msg'] = "Please fill in all required fields";
			return json_encode($resp);
			exit;
		}
		
		if (!empty($_FILES['attachment']['name'])) {
			$uploadedFile = $_FILES['attachment'];
			$allowedExtensions = ['jpg', 'jpeg', 'png'];
	
			$fileExtension = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));
	
			if (in_array($fileExtension, $allowedExtensions)) {
				// Delete the old attachment file
				if (!empty($attachment_path && file_exists($attachment_path))) {
					if (unlink($attachment_path)) {
						// File deleted successfully
						// You can log a success message here if needed
					} else {
						// Error deleting file
						$error = error_get_last();
						error_log("Unable to delete file: " . $error['message']);
					}
				} 
	
				// Generate a unique filename
				//$unique_filename = uniqid() . '_' . time() . '_' . $uploadedFile['name'];
				$unique_filename = $uploadedFile['name'];
	
				// Set the new attachment path
				$uploadDirectory = 'C:/xampp/htdocs/utility/sr_attachments/';
				$targetFilePath = $uploadDirectory . $unique_filename;
	
				if (move_uploaded_file($uploadedFile['tmp_name'], $targetFilePath)) {
					$attachment_path = $targetFilePath;
				} else {
					$resp['status'] = "error";
					$resp['msg'] = "Error uploading file.";
					return json_encode($resp);
				}
			} else {
				$resp['status'] = 'error';
				$resp['msg'] = "Invalid file type. Please upload a JPEG or PNG file.";
				return json_encode($resp);
				exit;
			}
		}
	


		$status = 0;
		$date_created = date('Y-m-d H:i:s');
		require_once('../includes/session.php');
		$loginID = $_SESSION['alogin'];
		
		
		$params = "'$request','$dept','$acc_no', " . ($acc_no_to ? "'$acc_no_to'" : 'NULL') . ",'$adjust_for','$adjust_to', " . ($amount ? "'$amount'" : 'NULL') . ", " . ($gcf_edate ? "'$gcf_edate'" : 'NULL') . ", '$priority', '$purpose','$loginID','$status', '$date_created', '$attachment_path'";
		if (empty($id)) {
			$ticket_query = "INSERT INTO tickets (request, department_id, account_no, transfer_to, request_from, request_to, amount, gcf_edate, priority, description, employee_id,status, date_created, attachment) VALUES ($params)";
			$update = 0;
		}else{
			$data ="request, department_id, account_no, transfer_to, request_from, request_to, amount, gcf_edate, priority, description, status, attachment";
			$values = "'$request','$dept','$acc_no', " . ($acc_no_to ? "'$acc_no_to'" : 'NULL') . ",'$adjust_for','$adjust_to', " . ($amount ? "'$amount'" : 'NULL') . ", " . ($gcf_edate ? "'$gcf_edate'" : 'NULL') . ", '$priority', '$purpose','0', '$attachment_path'";
			$ticket_query = "UPDATE tickets SET ($data) = ($values) WHERE id = ".$id;
			$update = 1;
		}
		if (odbc_exec($this->conn2, $ticket_query)) {
			$resp['status'] = 'success';
			if ($update == 1){
				$this->log_log('Ticket Add', "Update - $id : $request : $acc_no ");
				$resp['msg'] = "Utility Ticket has been successfully updated.";
			}else{
				$this->log_log('Ticket Add', "ADD : $request : $acc_no ");
				$resp['msg'] = "Account has been successfully added.";
			}
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred.";
			$resp['err'] = odbc_errormsg($this->conn2) . " [$ticket_query]";
		}
		
		return json_encode($resp);
	}
	

	
	public function log_log($module, $notes){
/* 		$dsn = "pgadmin4"; 
		$user = "glicelo";   
		$pass = "admin12345";   
		$conn2 = odbc_connect($dsn, $user, $pass); */
		require_once('../includes/session.php');
		$employee_id = $_SESSION['alogin'];
		$date = date('Y-m-d');
		$time = date('H:i:s');
		$values = "'$employee_id', '$date','$time','$module','$notes'";
		$insert = "INSERT INTO t_utility_logs (c_username, c_date, c_time, c_module, c_notes) VALUES ($values)";
		$save = odbc_exec($this->conn2, $insert);
		if ($save) {
				$resp['status'] = 'success';
				$resp['msg'] = "Logs has been successfully inserted.";
		} else {
				$resp['status'] = 'failed';
				$resp['err'] = odbc_errormsg($this->conn2) . " [$insert]";
		}
	}

	public function update_request(){
		extract($_POST);
	
		require_once('../includes/session.php');
		$loginID = $_SESSION['alogin'];
		
		$ticket_query = "UPDATE tickets SET status = '" .$ticket_status. "' WHERE id = ".$id;
		
		if (odbc_exec($this->conn2, $ticket_query)) {
			$this->log_log('Ticket Updated', "ADD - $id : $ticket_status");
			$resp['status'] = 'success';
			$resp['msg'] = "Utility Ticket has been successfully updated.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred.";
			$resp['err'] = odbc_errormsg($this->conn2) . " [$ticket_query]";
		}
		
		return json_encode($resp);
	}


	public function save_account(){
	
		extract($_POST);
		
		if (empty($acc_no) || empty($fname) || empty($add) || empty($city_prov)) {
			$resp['status'] = 'error';
			$resp['msg'] = "Please fill in all required fields";
			return json_encode($resp);
			exit;
		}

		$site = substr($acc_no, 0, 3);
		$blk = substr($acc_no, 3, 3);
		$lot = substr($acc_no, 6, 2);
		$no = substr($acc_no, 8);

		$check = "SELECT * FROM t_projects where c_code = $site";

		$result = odbc_exec($this->conn2, $check);

			if (!$result) {
				die("ODBC query execution failed: " . odbc_errormsg());
			}
			// Fetch and display the results
			while ($row = odbc_fetch_array($result)) {
				$acronym = $row['c_acronym'];
			}
		$pbl = sprintf("%s B-%d L-%d No. %d", $acronym, $blk, $lot, $no);
		$billing_method=isset($_POST['billing_method']) ? $_POST['billing_method'] : 0; 


		$data = "c_account_no, c_site, c_block, c_lot, c_no, c_control_no, c_location, c_last_name, c_first_name, c_middle_name, c_address, c_city_prov, c_zipcode, c_status, c_remarks, c_date_applied, c_lot_area, c_types, c_end_date, c_email, c_contact_no, billing_method";
		$values = "'$acc_no', '$site','$blk', '$lot', '$no', '$ctr', '$pbl', '$lname', '$fname', '$mname', '$add', '$city_prov', '$zip_code', '$status', '$remarks', '$date_applied', '$lot_area', '$type', '$mtf_end', '$email_add', '$contact_no','$billing_method' ";
		if (empty($id)) {
			$data2 = "c_account_no, c_billed_up_to_date, c_due_date, c_balance, c_begin_balance , c_begin_date";
			$values2 = "'$acc_no', '$date_applied','$date_applied','0.0','0.0','$date_applied'";
			$values3 = "'$acc_no', '$date_applied','$date_applied','$date_applied','MTF','0.0','0.0'";
			$values4 = "'$acc_no', '$date_applied','$date_applied','$date_applied','STL','0.0','0.0'";
			$sql = "INSERT INTO t_utility_accounts ($data) VALUES ($values)";
			$sql2 = "INSERT INTO t_utility_flags ($data2) VALUES ($values2)";
			$sql3 = "INSERT INTO t_utility_bill VALUES ($values3),($values4)";
			$save = odbc_exec($this->conn2, $sql);
			$save2 = odbc_exec($this->conn2, $sql2);
			$save3 = odbc_exec($this->conn2, $sql3);
		} else {
			$sql = "UPDATE t_utility_accounts SET ($data) = ($values) WHERE c_account_no = '$acc_no'";
			$save = odbc_exec($this->conn2, $sql);
		}
		if ($save) {
				#$rid = !empty($id) ? $id : odbc_insert_id($this->conn2);
				$resp['status'] = 'success';
				if (empty($id)) {
					$this->log_log('Customer Account', "ADD - $id : $lname $fname ");
					$resp['msg'] = "Account has been successfully added.";
				} else {
					$this->log_log('Customer Account', "UPDATE - $id : $lname $fname ");
					$resp['msg'] = "Account has been updated successfully.";
				}
			
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred.";
			$resp['err'] = odbc_errormsg($this->conn2) . " [$sql]";
		}

		return json_encode($resp);
	}

	

	function delete_account(){
		
		extract($_POST);
		$sql = "DELETE FROM t_utility_accounts where c_account_no ='$id'";
/* 		$sql2 = "DELETE FROM t_utility_flags where c_account_no ='$id'";
		$sql3 = "DELETE FROM t_utility_bills where c_account_no ='$id'";
		$sql4 = "DELETE FROM t_utility_payments where c_account_no ='$id'"; */
		$del = odbc_exec($this->conn2, $sql);
		if ($del) {
			$this->log_log('Customer Account', "DELETE - $id ");
			$resp['status'] = 'success';
			$resp['msg'] = "Account has been successfully deleted.";
			
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = odbc_errormsg($this->conn2) . " [$sql]";
		}
		return json_encode($resp);
	}
	function update_payment(){
		
		extract($_POST);
		$acc_no = $_POST['acc_no'];
		$car_no = $_POST['car_no'];
		$pay_date = $_POST['pay_date'];
		$or_no = isset($_POST['payment_or']) ? $_POST['payment_or'] : '';
		$mode = isset($_POST['mop']) ? $_POST['mop'] : '';
		$check_date = isset($_POST['check_date']) ? $_POST['check_date'] : NULL;
		$branch = isset($_POST['branch']) ? $_POST['branch'] : NULL;
		$ref = isset($_POST['ref_no']) ? $_POST['ref_no'] : NULL;



		if ($car_no != $or_no) {
			$checking = "SELECT * FROM t_utility_payments WHERE c_st_or_no = '$or_no'";
			$check = odbc_exec($this->conn2, $checking);
			if (odbc_fetch_row($check)) {
			/* 	echo "The payment OR number exists in the database."; */
				$resp['status'] = 'failed';
				$resp['msg'] = "The payment OR number already exists!";
				return json_encode($resp);
				exit;
			}
		}
		$pay_amount_paid = isset($_POST['pay_amount_paid']) ? (float)$_POST['pay_amount_paid'] : 0;
		$pay_discount = isset($_POST['pay_discount']) ? (float)$_POST['pay_discount'] : 0;
		$date_updated = date('Y-m-d H:i:s');


		$sql = "UPDATE t_utility_payments SET c_check_date = " . ($check_date ? "'$check_date'" : 'NULL') . ", date_updated = '$date_updated', c_st_or_no = '$or_no', c_st_pay_date = '$pay_date', c_st_amount_paid = '$pay_amount_paid', c_discount = '$pay_discount', c_branch = '$branch', c_ref_no = '$ref', c_mop = '$mode' WHERE c_account_no = '$acc_no' and c_st_or_no = '$car_no'";
		$update = odbc_exec($this->conn2, $sql);
		//echo $sql;
		if ($update) {
			$this->log_log('Utility Payment', "UPDATE - $acc_no : $car_no : $pay_date : $pay_amount_paid : $pay_discount");
			$resp['status'] = 'success';
			$resp['msg'] = "CAR has been updated successfully.";
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = odbc_errormsg($this->conn2) . " [$sql]";
		}
		return json_encode($resp);
	}

	function delete_payment(){
		
		extract($_POST);
		$cancel_reason =  $_POST['cancel_reason'];
		$cancel_date = date('Y-m-d H:i:s');
		require_once('../includes/session.php');
		$cancel_by = $_SESSION['alogin'];
		$del_details = "SELECT * FROM t_utility_payments WHERE c_st_or_no = '$car_no'";
		$result = odbc_exec($this->conn2, $del_details);
		if (!$result) {
			die("ODBC query execution failed: " . odbc_errormsg());
		}
		while ($row = odbc_fetch_array($result)) {
			$acc_no = $row['c_account_no'];
			$or_no = $row['c_st_or_no'];
			$pay_date = $row['c_st_pay_date'];
			$pay_amount_paid = $row['c_st_amount_paid'];
			$pay_discount = $row['c_discount'];
			$mop = $row['c_mop'];
			$ref_no = isset($row['c_ref_no']) ? $row['c_ref_no'] : '';
			$branch = isset($row['c_branch']) ? $row['c_branch'] : '';
			$check_date =  $row['c_check_date'];
			$encoded_by = $row['c_encoded_by'];
			$date_encoded = $row['date_encoded'];
			$date_updated = $row['date_updated'];
			$payment_type = $row['payment_type'];

		}
		$sql = "DELETE FROM t_utility_payments WHERE c_st_or_no = '$car_no'";
	
		$delete = odbc_exec($this->conn2, $sql);
		//echo $sql;
		if ($delete) {
			$cancelled = "INSERT INTO t_cancelled_payments (c_account_no,
									c_st_or_no,
									c_st_pay_date,
									c_st_amount_paid,
									c_discount,
									c_mop,
									c_ref_no,
									c_branch,
									c_check_date,
									c_encoded_by,
									date_encoded,
									date_updated,
									payment_type,
									cancelled_date,
									cancel_reason,
									cancel_by) 
									VALUES (
										'$acc_no',
										'$or_no',
										'$pay_date',
										$pay_amount_paid,
										$pay_discount,
										'$mop',
										'$ref_no',
										'$branch',
										". ($check_date !== NULL ? "'$check_date'" : 'NULL') .",
										'$encoded_by',
										'$date_encoded',
										'$date_updated',
										'$payment_type',
										'$cancel_date',
										'$cancel_reason',
										'$cancel_by'
									)";
			//echo $cancelled;
       		if (odbc_exec($this->conn2, $cancelled)) {
				$this->log_log('Payment Cancelled', "Cancelled payment with OR No.: $or_no");
				$resp['status'] = 'success';
				$resp['msg'] = "Paymnet has been cancelled successfully.";
				
			} else {
				// Handle INSERT query error
				$resp['status'] = 'error';
				$resp['msg'] = "Error in inserting into t_cancelled_payments: " . odbc_errormsg($this->conn2);
			}
			
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = odbc_errormsg($this->conn2) . " [$sql]";
		}
		return json_encode($resp);
	}

	function adjust_bill(){
		
		extract($_POST);
		$acc_no = $_POST['acc_no'];
		$adjust_date = $_POST['adj_date'];
		$adjust_type = $_POST['type'];
		$adjust_for = $_POST['adjust_for'];
		$amount = (float)$_POST['amount'];
		require_once('../includes/session.php');
		$encoded_by = $_SESSION['alogin'];
		$date_encoded = date('Y-m-d H:i:s');
		$date_updated = date('Y-m-d H:i:s');
		$discount = 0;
		$notes = $_POST['notes'];

		if ($notes == ""):
			$resp['status'] = 'failed';
			$resp['msg'] = "Please provide notes!!";
			return json_encode($resp);
			exit;
		endif;

		if ($amount == 0):
			$resp['status'] = 'failed';
			$resp['msg'] = "Please input amount!!!";
			return json_encode($resp);
			exit;
		endif;
		do {
			$random_no = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
			$unique_no = $adjust_for . '-BA'. $random_no;
			if ($adjust_for == 'MTF'):
					$adjust_for1 = 'GCF';
			else:
				$adjust_for1 = $adjust_for;
			endif;
			$payment_type = $adjust_for1 . '-' .$adjust_type;
			// Check if the number exists in the database
			$query = "SELECT * FROM t_utility_payments WHERE c_st_or_no = '$unique_no'";
			/* $result = $mysqli->query($query); */
			$result = odbc_prepare($this->conn2, $query);
			odbc_execute($result);
			if (!odbc_fetch_row($result)) {
				break; // The number is unique, exit the loop
			}
		} while (true);
		

		
		$params = "'$acc_no', '$unique_no', '$adjust_date', '$amount', '$discount', '$encoded_by','$payment_type','$date_encoded'";
		// Create the INSERT query using parameterized query
		$insert_query = "INSERT INTO t_utility_payments (c_account_no, c_st_or_no, c_st_pay_date, c_st_amount_paid, c_discount, c_encoded_by, payment_type, date_encoded) VALUES ($params)";

		if ($adjust_type == "BA"):
			$adjust_desc = 'Bill Adjustment';
		elseif ($adjust_type == 'SA'):
			$adjust_desc = 'Surcharge Adjustment';
		else:
			$adjust_desc = 'Refund';
		endif;

		$params2 = "'$unique_no','$acc_no', '$adjust_date', '$adjust_desc','$notes'";
		$insert_adjustment = "INSERT INTO t_adjustment VALUES ($params2)";
		$adjustment = odbc_exec($this->conn2, $insert_adjustment);

		if (odbc_exec($this->conn2, $insert_query)) {
			$this->log_log('Utility Bill Adjustment'," $acc_no | $unique_no | $notes");
			$resp['status'] = 'success';
			$resp['msg'] = "Bill Adjustment has been successfully added.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred.";
			$resp['err'] = odbc_errormsg($this->conn2) . " [$insert_query]";
		}
		
		return json_encode($resp);
	}

	function adjust_payment(){
		
		extract($_POST);
		$acc_no = $_POST['acc_no'];
		$new_acc = $_POST['new_acc'];
		$adjust_date = $_POST['adj_date'];
		$adjust_type = 'ADJ';
		$adjust_from = isset($_POST['adjust_from']) ? $_POST['adjust_from'] : '';
		$adjust_to = isset($_POST['adjust_to']) ? $_POST['adjust_to'] : '';
		$amount = (float)$_POST['amount'];
		require_once('../includes/session.php');
		$encoded_by = $_SESSION['alogin'];
		$date_encoded = date('Y-m-d H:i:s');
		$date_updated = date('Y-m-d H:i:s');
		$discount = 0;
		$notes = $_POST['notes'];

		if ($notes == "" || $adjust_from == ""  || $adjust_to == ""):
			$resp['status'] = 'failed';
			$resp['msg'] = "Please complete the details!!";
			return json_encode($resp);
			exit;
		endif;

		if ($amount == 0):
			$resp['status'] = 'failed';
			$resp['msg'] = "Please input amount!!!";
			return json_encode($resp);
			exit;
		endif;
		do {
			$random_no = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
			$unique_no1 = $adjust_from . '-' . $adjust_type . $random_no;
			if ($adjust_from == 'MTF'):
				$adjust_from1 = 'GCF';
			else:
				$adjust_from1 = $adjust_from;
			endif;
			$payment_type1 = $adjust_from1 . '-' . $adjust_type;
			$unique_no2 = $adjust_to . '-' . $adjust_type . $random_no;
			if ($adjust_to == 'MTF'):
				$adjust_to1 = 'GCF';
			else:
				$adjust_to1 = $adjust_to;
			endif;
			$payment_type2 = $adjust_to . '-' . $adjust_type;
			// Check if the number exists in the database
			$query = "SELECT * FROM t_utility_payments WHERE c_st_or_no = '$unique_no1' or c_st_or_no = '$unique_no2'";
			/* $result = $mysqli->query($query); */
			$result = odbc_prepare($this->conn2, $query);
			odbc_execute($result);
			if (!odbc_fetch_row($result)) {
				break; // The number is unique, exit the loop
			}
		} while (true);
		
		$amount_from = -$amount;
		$amount_to = $amount;
		
		$params_from = "'$acc_no', '$unique_no1', '$adjust_date', '$amount_from', '$discount', '$encoded_by','$date_encoded','$payment_type1'";
		$params_to = "'$new_acc', '$unique_no2', '$adjust_date', '$amount_to', '$discount', '$encoded_by','$date_encoded','$payment_type2'";
		// Create the INSERT query using parameterized query
		$insert_query_from = "INSERT INTO t_utility_payments (c_account_no, c_st_or_no, c_st_pay_date, c_st_amount_paid, c_discount, c_encoded_by, date_encoded, payment_type) VALUES ($params_from)";
		$insert_query_to = "INSERT INTO t_utility_payments (c_account_no, c_st_or_no, c_st_pay_date, c_st_amount_paid, c_discount, c_encoded_by, date_encoded, payment_type) VALUES ($params_to)";


		$params2_from = "'$unique_no1','$acc_no', '$adjust_date', 'PAYMENT ADJUSTMENT','$notes'";
		$params2_to = "'$unique_no2','$new_acc', '$adjust_date', 'PAYMENT ADJUSTMENT','$notes'";
		$insert_adjustment_from = "INSERT INTO t_adjustment VALUES ($params2_from)";
		$insert_adjustment_to = "INSERT INTO t_adjustment VALUES ($params2_to)";
		$adjustment = odbc_exec($this->conn2, $insert_adjustment_from);
		$adjustment = odbc_exec($this->conn2, $insert_adjustment_to);

		if (odbc_exec($this->conn2, $insert_query_from) && odbc_exec($this->conn2, $insert_query_to)) {
			$this->log_log('Utility Payment Adjustment'," $acc_no | $new_acc| $unique_no1 | $unique_no2 | $notes");
			$resp['status'] = 'success';
			$resp['msg'] = "Bill Adjustment has been successfully added.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred.";
			$resp['err'] = odbc_errormsg($this->conn2) . " [$insert_query_from] [$insert_query_to]";
		}
		
		return json_encode($resp);
	}

	function bounce_payment(){
		
		extract($_POST);
		$acc_no = $_POST['acc_no'];
		$car_no = $_POST['payment_or'];
		$payment_type = substr($car_no, 0, 3) . '-ADJCM';
		$check_date = $_POST['check_date'];
		$amount = (float)$_POST['pay_amount_paid'];
		$discount = (float)$_POST['pay_discount'];
		require_once('../includes/session.php');
		$encoded_by = $_SESSION['alogin'];
		$date_encoded = date('Y-m-d H:i:s');
		$date_updated = date('Y-m-d H:i:s');
		$reason = "Bounced Check";
		$notes = $reason . ' ' . str_replace(['MTF', 'STL'], '', $car_no);
		$pay_type = (substr($car_no, 0, 3) == 'MTF') ? 'GCF-CM' : 'STL-CM';
		$cm_amount = -($amount + $discount);

		
		$query = "SELECT c_or_no FROM t_adjustment WHERE c_adjustment_type = 'Bounced Check' ORDER BY c_or_no DESC LIMIT 1";
		$result = odbc_prepare($this->conn2, $query);
		odbc_execute($result);
		// Fetch the result
		if ($row = odbc_fetch_array($result)) {
			$last_cm_no = $row['c_or_no'];
			preg_match('/([A-Za-z]+)(\d+)/', $last_cm_no, $matches);
			$alphabetic_part = $matches[1];
			$numeric_part = $matches[2];
			$incremented_numeric_part = sprintf('%04d', intval($numeric_part) + 1);
			$cm_no = $payment_type . $incremented_numeric_part;

		} else {
			$cm_no = $payment_type .'0001';
		}

		
		$params_to = "'$acc_no', '$cm_no', '$date_encoded', '$cm_amount','0','$encoded_by','$date_encoded','$pay_type'";
		$insert_query_to = "INSERT INTO t_utility_payments (c_account_no, c_st_or_no, c_st_pay_date, c_st_amount_paid, c_discount, c_encoded_by, date_encoded, payment_type) VALUES ($params_to)";

		#$cm_params = "'$cm_no', '$acc_no', '$car_no', '$check_date', '$cm_amount','$reason','$encoded_by','$date_encoded','$date_updated','1'";
		#$insert_cm = "INSERT INTO t_credit_memo (c_cm_no,c_account_no, c_car_no, c_date, c_amount, c_reason, c_employee_id, c_date_created, c_date_modified, c_status) VALUES ($cm_params)";
		$cm_params = "'$cm_no','$acc_no', '$date_encoded', '$reason','$notes'";
		$insert_cm = "INSERT INTO t_adjustment VALUES ($cm_params)";



		if (odbc_exec($this->conn2, $insert_cm) && odbc_exec($this->conn2, $insert_query_to)) {
			$this->log_log('Utility Credit Memo'," $acc_no | $cm_no | $reason");
			$resp['status'] = 'success';
			$resp['msg'] = "Credit Memo has been successfully added.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred.";
			$resp['err'] = odbc_errormsg($this->conn2) . " [$insert_cm] [$insert_query_to]";
		}
		
		return json_encode($resp);
	}



	function save_payment(){
		
		extract($_POST);
		$l_gcf = 0;
		$l_stl = 0;
		$acc_no = $_POST['acc_no'];
		$pay_date = $_POST['pay_date'];
		$or_no = isset($_POST['payment_or']) ? $_POST['payment_or'] : '';
		$main_amount_paid = isset($_POST['main_amount_paid']) ? (float)$_POST['main_amount_paid'] : 0;
		$main_discount = isset($_POST['main_discount']) ? (float)$_POST['main_discount'] : 0;
		$stl_amount_paid = isset($_POST['stl_amount_paid']) ? (float)$_POST['stl_amount_paid'] : 0;
		$stl_discount = isset($_POST['stl_discount']) ? (float)$_POST['stl_discount'] : 0;

		// If the value is an empty string, set it to 0
		$main_amount_paid = ($main_amount_paid === '') ? 0 : $main_amount_paid;
		$main_discount = ($main_discount === '') ? 0 : $main_discount;
		$stl_amount_paid = ($stl_amount_paid === '') ? 0 : $stl_amount_paid;
		$stl_discount = ($stl_discount === '') ? 0 : $stl_discount;

		$mode_of_payment = $_POST['mode_payment'];
		/* if ($mode_of_payment == 1 || $mode_of_payment == 4) {
			$mode_of_payment = 1;
		} */
		$ref_no = isset($_POST['ref_no']) ? $_POST['ref_no'] : '';
		$branch = isset($_POST['branch']) ? $_POST['branch'] : '';
		$check_date = isset($_POST['check_date']) ? $_POST['check_date'] : NULL;
		require_once('../includes/session.php');
		$encoded_by = $_SESSION['alogin'];
		$date_encoded = date('Y-m-d H:i:s');
		$date_updated = date('Y-m-d H:i:s');

		if ($or_no == ""):
				$resp['status'] = 'failed';
				$resp['msg'] = "Please input OR No.";
				return json_encode($resp);
				exit;
		endif;

		if (!ctype_digit($or_no) || strlen($or_no) !== 6) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Please input a 6-digit number for OR No.";
			echo json_encode($resp);
			exit;
		}

		if ($main_amount_paid != 0):
				$l_gcf = 1;
				$main_or_no = 'MTF-CAR' . $_POST['payment_or'];
				$payment_type1 = "GCF-PAY";
		endif;
		if  ($stl_amount_paid != 0):
				$l_stl = 1;
				$stl_or_no = 'STL-CAR' . $_POST['payment_or'];
				$payment_type2 = "STL-PAY";
		endif;

		$check_payment = "SELECT * FROM t_utility_payments WHERE c_st_or_no ILIKE ?";
		$check_payment_query = odbc_prepare($this->conn2, $check_payment);
		odbc_execute($check_payment_query, array('%' . $or_no . '%'));

		if (!odbc_fetch_row($check_payment_query)) {
		
			if ($l_gcf == 1) {
				$params = "'$acc_no', '$main_or_no', '$pay_date', '$main_amount_paid', '$main_discount','$mode_of_payment','$ref_no','$branch', " . ($check_date ? "'$check_date'" : 'NULL') . ",'$encoded_by','$date_encoded','$date_updated','$payment_type1'";
				$insert_query_gcf = "INSERT INTO t_utility_payments (c_account_no, c_st_or_no, c_st_pay_date, c_st_amount_paid, c_discount, c_mop , c_ref_no, c_branch, c_check_date, c_encoded_by, date_encoded, date_updated, payment_type) VALUES ($params)";
			}
			
			if ($l_stl == 1) {
				$params2 = "'$acc_no', '$stl_or_no', '$pay_date', '$stl_amount_paid', '$stl_discount','$mode_of_payment','$ref_no','$branch', " . ($check_date ? "'$check_date'" : 'NULL') . ",'$encoded_by','$date_encoded','$date_updated','$payment_type2'";
				$insert_query_stl = "INSERT INTO t_utility_payments (c_account_no, c_st_or_no, c_st_pay_date, c_st_amount_paid, c_discount, c_mop , c_ref_no, c_branch, c_check_date, c_encoded_by, date_encoded, date_updated, payment_type) VALUES ($params2)";
			
			}
			
			if (isset($insert_query_gcf) && isset($insert_query_stl)) {
				// Both conditions are true, so execute both queries
				if (odbc_exec($this->conn2, $insert_query_gcf) && odbc_exec($this->conn2, $insert_query_stl)) {
					$this->log_log('Utility Payment', "SAVE GCF - $acc_no : $main_or_no : $pay_date : $main_amount_paid : $main_discount : $mode_of_payment : $ref_no :$branch : $check_date");
					$this->log_log('Utility Payment', "SAVE STL - $acc_no : $stl_or_no : $pay_date : $stl_amount_paid : $stl_discount : $mode_of_payment : $ref_no :$branch : $check_date");
					$resp['status'] = 'success';
					$resp['msg'] = "Both payments have been successfully added.";
				} else {
					$resp['status'] = 'failed';
					$resp['msg'] = "An error occurred.";
					$resp['err'] = odbc_errormsg($this->conn2);
				}
			} elseif (isset($insert_query_gcf)) {
				// Only $l_gcf is true, so execute the GCF query
				if (odbc_exec($this->conn2, $insert_query_gcf)) {
					$this->log_log('Utility Payment', "SAVE GCF - $acc_no : $main_or_no : $pay_date : $main_amount_paid : $main_discount : $mode_of_payment : $ref_no :$branch : $check_date");
					$resp['status'] = 'success';
					$resp['msg'] = "GCF Payment has been successfully added.";
				} else {
					$resp['status'] = 'failed';
					$resp['msg'] = "An error occurred.";
					$resp['err'] = odbc_errormsg($this->conn2);
				}
			} elseif (isset($insert_query_stl)) {
				// Only $l_stl is true, so execute the STL query
				if (odbc_exec($this->conn2, $insert_query_stl)) {
					$this->log_log('Utility Payment', "SAVE STL - $acc_no : $stl_or_no : $pay_date : $stl_amount_paid : $stl_discount : $mode_of_payment : $ref_no :$branch : $check_date");
					$resp['status'] = 'success';
					$resp['msg'] = "STL Payment has been successfully added.";
				} else {
					$resp['status'] = 'failed';
					$resp['msg'] = "An error occurred.";
					$resp['err'] = odbc_errormsg($this->conn2);
				}
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = "Please input either of the two options (GCF or STL).";
				return json_encode($resp);
				exit;
			}
			

		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "OR number already exists!";
			return json_encode($resp);
			exit;
		}
		
		return json_encode($resp);
	}

	function delete_bill(){
		
		extract($_POST);
		$sql = "DELETE FROM t_utility_bill WHERE c_due_date = '$date' and c_bill_type = '$type' and c_account_no = '$id'";
		$delete = odbc_exec($this->conn2, $sql);
		//echo $sql;
		if ($delete) {
			$this->log_log('Utility Bill', "DELETE - $date : $type : $id ");
			$resp['status'] = 'success';
			$resp['msg'] = "BILL has been deleted successfully.";
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = odbc_errormsg($this->conn2) . " [$sql]";
		}
		return json_encode($resp);
	}


	function save_bill(){
		
		extract($_POST);
		$acc_no = $_POST['acc_no'];
		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];
		$due_date = $_POST['due_date'];
		$bill_type = $_POST['type'];
		$amount_due = $_POST['amount'];
		$prev_bal = $_POST['prev_bal'];
		
		$params = "'$acc_no', '$start_date', '$end_date', '$due_date', '$bill_type', '$amount_due', '$prev_bal'";
		$insert_query = "INSERT INTO t_utility_bill VALUES ($params)";
		if (odbc_exec($this->conn2, $insert_query)) {
			$this->log_log('Utility Bill', "SAVE - $acc_no : $start_date : $bill_type : $amount_due : $prev_bal ");
			$resp['status'] = 'success';
			$resp['msg'] = "Utility Bill has been successfully added.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred.";
			$resp['err'] = odbc_errormsg($this->conn2) . " [$insert_query]";
		}
		
		return json_encode($resp);
	}

	function delete_adjustment(){
		
		extract($_POST);
		$sql = "DELETE FROM t_utility_payments WHERE c_st_pay_date = '$paydate' and c_st_or_no = '$or_no' and c_account_no = '$id'";
		$delete = odbc_exec($this->conn2, $sql);
		//echo $sql;
		if ($delete) {
			$this->log_log('Utility Payment Adjustment', "DELETE - $paydate : $or_no : $id ");
			$resp['status'] = 'success';
			$resp['msg'] = "Adjustment has been deleted successfully.";
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = odbc_errormsg($this->conn2) . " [$sql]";
		}
		return json_encode($resp);
	}

	function submit_report(){
		extract($_POST);
		$date_submitted = date('Y-m-d H:i:s');
		$status = "Submitted";
		
		$params = "'$id','$cash','$check','$online', '$voucher', '$total', '$status', '$date_submitted'";
		$report_query = "INSERT INTO summary_report (transaction_date, total_cash, total_check, total_online, total_voucher, total, status, date_submitted) VALUES ($params)";

		if (odbc_exec($this->conn2, $report_query)) {
			$resp['status'] = 'success';
			$this->log_log('Summary Report Submit', "Add - $id : $total ");
			$resp['msg'] = "Summary Report has been successfully submitted.";
	
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred.";
			$resp['err'] = odbc_errormsg($this->conn2) . " [$report_query]";
		}		
		return json_encode($resp);
	}

	public function __destruct() {
        // Close the database connection when the object is destroyed
        odbc_close($this->conn2);
    }

}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);

switch ($action) {
	
	case 'save_account':
		echo $Master->save_account();
	break;
	case 'delete_account':
		echo $Master->delete_account();
	break;
	case 'save_payment':
		echo $Master->save_payment();
	break;
	case 'update_payment':
		echo $Master->update_payment();
	break;
	case 'delete_payment':
		echo $Master->delete_payment();
	break;
	case 'save_mtf_payment':
		echo $Master->save_mtf_payment();
	break;
	case 'save_stl_payment':
		echo $Master->save_stl_payment();
	break;
	case 'delete_bill':
		echo $Master->delete_bill();
	break;
	case 'save_bill':
		echo $Master->save_bill();
	break;
	case 'adjust_bill':
		echo $Master->adjust_bill();
	break;	
	case 'adjust_payment':
		echo $Master->adjust_payment();
	break;
	case 'save_user':
		echo $Master->save_user();
	break;
	case 'add_ticket':
		echo $Master->add_ticket();
	break;

	case 'update_request':
		echo $Master->update_request();
	break;

	case 'delete_adjustment':
		echo $Master->delete_adjustment();
	break;

	case 'submit_report':
		echo $Master->submit_report();
	break;

	case 'bounce_payment':
		echo $Master->bounce_payment();
	break;

	default:
		break;
}