<?php

Class Master{
	private $conn2;

    public function __construct() {
        require_once('../includes/config.php');
        $this->conn2 = odbc_connect($dsn, $user, $pass);
    }

	function add_ticket(){
		extract($_POST);
		$subject=$_POST['subject'];  
		$dept=$_POST['department'];  
		$request=$_POST['request']; 
		$priority=$_POST['priority'];  
		$purpose=$_POST['purpose'];

		if (empty($subject) || empty($purpose) || empty($priority) || empty($dept)) {
			$resp['status'] = 'error';
			$resp['msg'] = "Please fill in all required fields";
			return json_encode($resp);
			exit;
		}

		$status = 0; // Set initial status to 0
		$date_created = date('Y-m-d H:i:s');
		require_once('../includes/session.php');
		$loginID = $_SESSION['alogin'];
		
		
		$params = "'$subject', '$dept', '$request', '$priority', '$purpose','$loginID',,'$status', '$date_created'";
		if (empty($id)) {
			$ticket_query = "INSERT INTO tickets (subject, department_id, request, priority, description, employee_id,status, date_created) VALUES ($params)";
		}else{
			$data ="subject,department_id,request,priority,description";
			$values = "'$subject','$dept','$request','$priority','$purpose'";
			$ticket_query = "UPDATE tickets SET ($data) = ($values) WHERE id = ".$id;
		}
		if (odbc_exec($this->conn2, $ticket_query)) {
			/* $this->log_log('Utility Bill', "DELETE - $date : $type : $id "); */
			$resp['status'] = 'success';
			$resp['msg'] = "Utility Ticket has been successfully saved.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred.";
			$resp['err'] = odbc_errormsg($this->conn2) . " [$ticket_query]";
		}
		
		return json_encode($resp);
	}
	

	public function log_log($module, $notes){
		$dsn = "pgadmin4"; 
		$user = "glicelo";   
		$pass = "admin12345";   
		$conn2 = odbc_connect($dsn, $user, $pass);
		require_once('../includes/session.php');
		$employee_id = $_SESSION['alogin'];
		$date = date('Y-m-d');
		$time = date('H:i:s');
		$values = "'$employee_id', '$date','$time','$module','$notes'";
		$insert = "INSERT INTO t_utility_logs (c_username, c_date, c_time, c_module, c_notes) VALUES ($values)";
		$save = odbc_exec($conn2, $insert);
		if ($save) {
				$resp['status'] = 'success';
				$resp['msg'] = "Logs has been successfully inserted.";
		} else {
				$resp['status'] = 'failed';
				$resp['err'] = odbc_errormsg($conn2) . " [$insert]";
		}
	}

	public function save_account(){
		require_once('../includes/config.php');
		extract($_POST);
		
		$site = substr($acc_no, 0, 3);
		$blk = substr($acc_no, 3, 3);
		$lot = substr($acc_no, 6, 2);
		$no = substr($acc_no, 8);

		$check = "SELECT * FROM t_projects where c_code = $site";

		$result = odbc_exec($conn2, $check);

			if (!$result) {
				die("ODBC query execution failed: " . odbc_errormsg());
			}
			// Fetch and display the results
			while ($row = odbc_fetch_array($result)) {
				$acronym = $row['c_name'];
			}
		$pbl = sprintf("%s B-%d L-%d No. %d", $acronym, $blk, $lot, $no);
		
		$data = "c_account_no, c_site, c_block, c_lot, c_no, c_control_no, c_location, c_last_name, c_first_name, c_middle_name, c_address, c_city_prov, c_zipcode, c_status, c_remarks, c_date_applied, c_lot_area, c_types, c_end_date, c_email, c_contact_no";
		$values = "'$acc_no', '$site','$blk', '$lot', '$no', '$ctr', '$pbl', '$lname', '$fname', '$mname', '$add', '$city_prov', '$zip_code', '$status', '$remarks', '$date_applied', '$lot_area', '$type', '$mtf_end', '$email_add', '$contact_no' ";
		if (empty($id)) {
			$data2 = "c_account_no, c_billed_up_to_date, c_due_date, c_balance, c_begin_balance , c_begin_date";
			$values2 = "'$acc_no', '$date_applied','$date_applied','0.0','0.0','$date_applied'";
			$values3 = "'$acc_no', '$date_applied','$date_applied','$date_applied','MTF','0.0','0.0'";
			$values4 = "'$acc_no', '$date_applied','$date_applied','$date_applied','STL','0.0','0.0'";
			$sql = "INSERT INTO t_utility_accounts ($data) VALUES ($values)";
			$sql2 = "INSERT INTO t_utility_flags ($data2) VALUES ($values2)";
			$sql3 = "INSERT INTO t_utility_bill VALUES ($values3),($values4)";
			$save = odbc_exec($conn2, $sql);
			$save2 = odbc_exec($conn2, $sql2);
			$save3 = odbc_exec($conn2, $sql3);
		} else {
			$sql = "UPDATE t_utility_accounts SET ($data) = ($values) WHERE c_account_no = '$acc_no'";
			$save = odbc_exec($conn2, $sql);
		}
		if ($save) {
				#$rid = !empty($id) ? $id : odbc_insert_id($conn2);
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
			$resp['err'] = odbc_errormsg($conn2) . " [$sql]";
		}

		return json_encode($resp);
	}

	function delete_account(){
		require_once('../includes/config.php');
		extract($_POST);
		$sql = "DELETE FROM t_utility_accounts where c_account_no ='$id'";
/* 		$sql2 = "DELETE FROM t_utility_flags where c_account_no ='$id'";
		$sql3 = "DELETE FROM t_utility_bills where c_account_no ='$id'";
		$sql4 = "DELETE FROM t_utility_payments where c_account_no ='$id'"; */
		$del = odbc_exec($conn2, $sql);
		if ($del) {
			$this->log_log('Customer Account', "DELETE - $id ");
			$resp['status'] = 'success';
			$resp['msg'] = "Account has been successfully deleted.";
			
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = odbc_errormsg($conn2) . " [$sql]";
		}
		return json_encode($resp);
	}
	function update_payment(){
		require_once('../includes/config.php');
		extract($_POST);
		$acc_no = $_POST['acc_no'];
		$car_no = $_POST['car_no'];
		$pay_date = $_POST['pay_date'];
		$or_no = isset($_POST['payment_or']) ? $_POST['payment_or'] : '';

		if ($car_no != $or_no) {
			$checking = "SELECT * FROM t_utility_payments WHERE c_st_or_no = '$or_no'";
			$check = odbc_exec($conn2, $checking);
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

		$sql = "UPDATE t_utility_payments SET c_st_or_no = '$or_no', c_st_pay_date = '$pay_date', c_st_amount_paid = '$pay_amount_paid', c_discount = '$pay_discount' WHERE c_account_no = '$acc_no' and c_st_or_no = '$car_no'";
		$update = odbc_exec($conn2, $sql);
		//echo $sql;
		if ($update) {
			$this->log_log('Utility Payment', "UPDATE - $acc_no : $car_no : $pay_date : $pay_amount_paid : $pay_discount");
			$resp['status'] = 'success';
			$resp['msg'] = "CAR has been updated successfully.";
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = odbc_errormsg($conn2) . " [$sql]";
		}
		return json_encode($resp);
	}

	function delete_payment(){
		require_once('../includes/config.php');
		extract($_POST);
		$del_details = "SELECT * FROM t_utility_payments WHERE c_st_or_no = '$id'";
		$result = odbc_exec($conn2, $del_details);
		if (!$result) {
			die("ODBC query execution failed: " . odbc_errormsg());
		}
		while ($row = odbc_fetch_array($result)) {
			$acc_no = $row['c_account_no'];
			$or_no = $row['c_st_or_no'];
			$pay_date = $row['c_st_pay_date'];
			$pay_amount_paid = $row['c_st_amount_paid'];
			$pay_discount = $row['c_discount'];
		}
		$sql = "DELETE FROM t_utility_payments WHERE c_st_or_no = '$id'";
		$delete = odbc_exec($conn2, $sql);
		//echo $sql;
		if ($delete) {
			$this->log_log('Utility Payment', "DELETE - $acc_no : $or_no : $pay_date : $pay_amount_paid : $pay_discount");
			$resp['status'] = 'success';
			$resp['msg'] = "CAR has been deleted successfully.";
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = odbc_errormsg($conn2) . " [$sql]";
		}
		return json_encode($resp);
	}

	function adjust_bill(){
		require_once('../includes/config.php');
		extract($_POST);
		$acc_no = $_POST['acc_no'];
		$adjust_date = $_POST['adj_date'];
		$adjust_type = $_POST['type'];
		$adjust_for = $_POST['adjust_for'];
		$amount = (float)$_POST['amount'];
		require_once('../includes/session.php');
		$encoded_by = $_SESSION['alogin'];
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
			$unique_no = $adjust_for . '-' . $adjust_type . $random_no;
		
			// Check if the number exists in the database
			$query = "SELECT * FROM t_utility_payments WHERE c_st_or_no = '$unique_no'";
			/* $result = $mysqli->query($query); */
			$result = odbc_prepare($conn2, $query);
			odbc_execute($result);
			if (!odbc_fetch_row($result)) {
				break; // The number is unique, exit the loop
			}
		} while (true);
		
		
		$params = "'$acc_no', '$unique_no', '$adjust_date', '$amount', '$discount', '$encoded_by'";
		// Create the INSERT query using parameterized query
		$insert_query = "INSERT INTO t_utility_payments (c_account_no, c_st_or_no, c_st_pay_date, c_st_amount_paid, c_discount, c_encoded_by) VALUES ($params)";

		$params2 = "'$unique_no','$acc_no', '$adjust_date', 'BILL ADJUSTMENT','$notes'";
		$insert_adjustment = "INSERT INTO t_adjustment VALUES ($params2)";
		$adjustment = odbc_exec($conn2, $insert_adjustment);

		if (odbc_exec($conn2, $insert_query)) {
			$this->log_log('Utility Bill Adjustment'," $acc_no | $unique_no | $notes");
			$resp['status'] = 'success';
			$resp['msg'] = "Bill Adjustment has been successfully added.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred.";
			$resp['err'] = odbc_errormsg($conn2) . " [$insert_query]";
		}
		
		return json_encode($resp);
	}

	function adjust_payment(){
		require_once('../includes/config.php');
		extract($_POST);
		$acc_no = $_POST['acc_no'];
		$new_acc = $_POST['new_acc'];
		$adjust_date = $_POST['adj_date'];
		$adjust_type = 'ADJ';
		$adjust_from = $_POST['adjust_from'];
		$adjust_to = $_POST['adjust_to'];
		$amount = (float)$_POST['amount'];
		require_once('../includes/session.php');
		$encoded_by = $_SESSION['alogin'];
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
			$unique_no1 = $adjust_from . '-' . $adjust_type . $random_no;
			$unique_no2 = $adjust_to . '-' . $adjust_type . $random_no;
		
			// Check if the number exists in the database
			$query = "SELECT * FROM t_utility_payments WHERE c_st_or_no = '$unique_no1' or c_st_or_no = '$unique_no2'";
			/* $result = $mysqli->query($query); */
			$result = odbc_prepare($conn2, $query);
			odbc_execute($result);
			if (!odbc_fetch_row($result)) {
				break; // The number is unique, exit the loop
			}
		} while (true);
		
		$amount_from = -$amount;
		$amount_to = $amount;
		
		$params_from = "'$acc_no', '$unique_no1', '$adjust_date', '$amount_from', '$discount', '$encoded_by'";
		$params_to = "'$new_acc', '$unique_no2', '$adjust_date', '$amount_to', '$discount', '$encoded_by'";
		// Create the INSERT query using parameterized query
		$insert_query_from = "INSERT INTO t_utility_payments (c_account_no, c_st_or_no, c_st_pay_date, c_st_amount_paid, c_discount, c_encoded_by) VALUES ($params_from)";
		$insert_query_to = "INSERT INTO t_utility_payments (c_account_no, c_st_or_no, c_st_pay_date, c_st_amount_paid, c_discount, c_encoded_by) VALUES ($params_to)";


		$params2_from = "'$unique_no1','$acc_no', '$adjust_date', 'PAYMENT ADJUSTMENT','$notes'";
		$params2_to = "'$unique_no2','$new_acc', '$adjust_date', 'PAYMENT ADJUSTMENT','$notes'";
		$insert_adjustment_from = "INSERT INTO t_adjustment VALUES ($params2_from)";
		$insert_adjustment_to = "INSERT INTO t_adjustment VALUES ($params2_to)";
		$adjustment = odbc_exec($conn2, $insert_adjustment_from);
		$adjustment = odbc_exec($conn2, $insert_adjustment_to);

		if (odbc_exec($conn2, $insert_query_from) && odbc_exec($conn2, $insert_query_to)) {
			$this->log_log('Utility Payment Adjustment'," $acc_no | $new_acc| $unique_no1 | $unique_no2 | $notes");
			$resp['status'] = 'success';
			$resp['msg'] = "Bill Adjustment has been successfully added.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred.";
			$resp['err'] = odbc_errormsg($conn2) . " [$insert_query]";
		}
		
		return json_encode($resp);
	}



	function save_payment(){
		require_once('../includes/config.php');
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
		$mode_of_payment = $_POST['mode_payment'];
		$ref_no = isset($_POST['ref_no']) ? $_POST['ref_no'] : '';
		$branch = isset($_POST['branch']) ? $_POST['branch'] : '';
		$check_date = isset($_POST['check_date']) ? $_POST['check_date'] : NULL;
		require_once('../includes/session.php');
		$encoded_by = $_SESSION['alogin'];
		 
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
		endif;
		if  ($stl_amount_paid != 0):
				$l_stl = 1;
				$stl_or_no = 'STL-CAR' . $_POST['payment_or'];
		endif;

		$check_payment = "SELECT * FROM t_utility_payments WHERE c_st_or_no ILIKE ?";
		$check_payment_query = odbc_prepare($conn2, $check_payment);
		odbc_execute($check_payment_query, array('%' . $or_no . '%'));

		if (!odbc_fetch_row($check_payment_query)) {
		
			if ($l_gcf == 1) {
				$params = "'$acc_no', '$main_or_no', '$pay_date', '$main_amount_paid', '$main_discount','$mode_of_payment','$ref_no','$branch', " . ($check_date ? "'$check_date'" : 'NULL') . ",'$encoded_by'";
				$insert_query_gcf = "INSERT INTO t_utility_payments (c_account_no, c_st_or_no, c_st_pay_date, c_st_amount_paid, c_discount, c_mop , c_ref_no, c_branch, c_check_date, c_encoded_by) VALUES ($params)";
			}
			
			if ($l_stl == 1) {
				$params2 = "'$acc_no', '$stl_or_no', '$pay_date', '$stl_amount_paid', '$stl_discount','$mode_of_payment','$ref_no','$branch', " . ($check_date ? "'$check_date'" : 'NULL') . ",'$encoded_by'";
				$insert_query_stl = "INSERT INTO t_utility_payments (c_account_no, c_st_or_no, c_st_pay_date, c_st_amount_paid, c_discount, c_mop , c_ref_no, c_branch, c_check_date, c_encoded_by) VALUES ($params2)";
			
			}
			
			if (isset($insert_query_gcf) && isset($insert_query_stl)) {
				// Both conditions are true, so execute both queries
				if (odbc_exec($conn2, $insert_query_gcf) && odbc_exec($conn2, $insert_query_stl)) {
					$this->log_log('Utility Payment', "SAVE GCF - $acc_no : $main_or_no : $pay_date : $main_amount_paid : $main_discount : $mode_of_payment : $ref_no :$branch : $check_date");
					$this->log_log('Utility Payment', "SAVE STL - $acc_no : $stl_or_no : $pay_date : $stl_amount_paid : $stl_discount : $mode_of_payment : $ref_no :$branch : $check_date");
					$resp['status'] = 'success';
					$resp['msg'] = "Both payments have been successfully added.";
				} else {
					$resp['status'] = 'failed';
					$resp['msg'] = "An error occurred.";
					$resp['err'] = odbc_errormsg($conn2);
				}
			} elseif (isset($insert_query_gcf)) {
				// Only $l_gcf is true, so execute the GCF query
				if (odbc_exec($conn2, $insert_query_gcf)) {
					$this->log_log('Utility Payment', "SAVE GCF - $acc_no : $main_or_no : $pay_date : $main_amount_paid : $main_discount : $mode_of_payment : $ref_no :$branch : $check_date");
					$resp['status'] = 'success';
					$resp['msg'] = "GCF Payment has been successfully added.";
				} else {
					$resp['status'] = 'failed';
					$resp['msg'] = "An error occurred.";
					$resp['err'] = odbc_errormsg($conn2);
				}
			} elseif (isset($insert_query_stl)) {
				// Only $l_stl is true, so execute the STL query
				if (odbc_exec($conn2, $insert_query_stl)) {
					$this->log_log('Utility Payment', "SAVE STL - $acc_no : $stl_or_no : $pay_date : $stl_amount_paid : $stl_discount : $mode_of_payment : $ref_no :$branch : $check_date");
					$resp['status'] = 'success';
					$resp['msg'] = "STL Payment has been successfully added.";
				} else {
					$resp['status'] = 'failed';
					$resp['msg'] = "An error occurred.";
					$resp['err'] = odbc_errormsg($conn2);
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
		require_once('../includes/config.php');
		extract($_POST);
		$sql = "DELETE FROM t_utility_bill WHERE c_due_date = '$date' and c_bill_type = '$type' and c_account_no = '$id'";
		$delete = odbc_exec($conn2, $sql);
		//echo $sql;
		if ($delete) {
			$this->log_log('Utility Bill', "DELETE - $date : $type : $id ");
			$resp['status'] = 'success';
			$resp['msg'] = "BILL has been deleted successfully.";
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = odbc_errormsg($conn2) . " [$sql]";
		}
		return json_encode($resp);
	}


	function save_bill(){
		require_once('../includes/config.php');
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
		if (odbc_exec($conn2, $insert_query)) {
			$this->log_log('Utility Bill', "SAVE - $acc_no : $start_date : $bill_Type : $amount_due : $prev_bal ");
			$resp['status'] = 'success';
			$resp['msg'] = "Utility Bill has been successfully added.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred.";
			$resp['err'] = odbc_errormsg($conn2) . " [$insert_query]";
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

	default:
		break;
}