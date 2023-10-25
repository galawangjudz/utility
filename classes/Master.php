<?php

Class Master{

	function save_account(){
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
		
		$data = "c_account_no, c_site, c_block, c_lot, c_no, c_control_no, c_location, c_last_name, c_first_name, c_middle_name, c_address, c_city_prov, c_zipcode, c_status, c_remarks, c_date_applied, c_lot_area, c_types, c_end_date";
		$values = "'$acc_no', '$site','$blk', '$lot', '$no', '$ctr', '$pbl', '$lname', '$fname', '$mname', '$add', '$city_prov', '$zip_code', '$status', '$remarks', '$date_applied', '$lot_area', '$type', '$mtf_end'";
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
					$resp['msg'] = "Account has been successfully added.";
				} else {
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
			$resp['status'] = 'success';
			$resp['msg'] = "Account has been successfully deleted.";
			
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = odbc_errormsg($conn2) . " [$sql]";
		}
	}
	function update_payment(){
		require_once('../includes/config.php');
		extract($_POST);
		$acc_no = $_POST['acc_no'];
		$pay_date = $_POST['pay_date'];
		$pay_amount_paid = isset($_POST['pay_amount_paid']) ? (float)$_POST['pay_amount_paid'] : 0;
		$pay_discount = isset($_POST['pay_discount']) ? (float)$_POST['pay_discount'] : 0;
		$or_no = $_POST['payment_or'];

		$sql = "UPDATE t_utility_payments SET c_st_pay_date = '$pay_date', c_st_amount_paid = '$pay_amount_paid', c_discount = '$pay_discount' WHERE c_account_no = '$acc_no' and c_st_or_no = '$or_no'";
		$update = odbc_exec($conn2, $sql);
		//echo $sql;
		if ($update) {
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

		$sql = "DELETE FROM t_utility_payments WHERE c_st_or_no = '$id'";
		$delete = odbc_exec($conn2, $sql);
		//echo $sql;
		if ($delete) {
			$resp['status'] = 'success';
			$resp['msg'] = "CAR has been deleted successfully.";
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = odbc_errormsg($conn2) . " [$sql]";
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
		$main_amount_paid = isset($_POST['main_amount_paid']) ? (float)$_POST['main_amount_paid'] : 0;
		$main_discount = isset($_POST['main_discount']) ? (float)$_POST['main_discount'] : 0;
		$stl_amount_paid = isset($_POST['stl_amount_paid']) ? (float)$_POST['stl_amount_paid'] : 0;
		$stl_discount = isset($_POST['stl_discount']) ? (float)$_POST['stl_discount'] : 0;
		$or_no = $_POST['payment_or'];

		if ($or_no == ""):
				$resp['status'] = 'failed';
				$resp['msg'] = "Please input OR No.";
				return json_encode($resp);
				exit;
		endif;

		if ($main_amount_paid != 0):
				$l_gcf = 1;
				$main_or_no = 'MTF-' . $_POST['payment_or'];
		endif;
		if  ($stl_amount_paid != 0):
				$l_stl = 1;
				$stl_or_no = 'STL-' . $_POST['payment_or'];
		endif;

		$check_payment = "SELECT * FROM t_utility_payments WHERE c_st_or_no ILIKE ?";
		$check_payment_query = odbc_prepare($conn2, $check_payment);
		odbc_execute($check_payment_query, array('%' . $or_no . '%'));

		if (!odbc_fetch_row($check_payment_query)) {
		
			if ($l_gcf == 1) {
				$params = "'$acc_no', '$main_or_no', '$pay_date', '$main_amount_paid', '$main_discount'";
				$insert_query_gcf = "INSERT INTO t_utility_payments (c_account_no, c_st_or_no, c_st_pay_date, c_st_amount_paid, c_discount) VALUES ($params)";
			}
			
			if ($l_stl == 1) {
				$params2 = "'$acc_no', '$stl_or_no', '$pay_date', '$stl_amount_paid', '$stl_discount'";
				$insert_query_stl = "INSERT INTO t_utility_payments (c_account_no, c_st_or_no, c_st_pay_date, c_st_amount_paid, c_discount) VALUES ($params2)";
			}
			
			if (isset($insert_query_gcf) && isset($insert_query_stl)) {
				// Both conditions are true, so execute both queries
				if (odbc_exec($conn2, $insert_query_gcf) && odbc_exec($conn2, $insert_query_stl)) {
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

	function save_mtf_payment(){
		require_once('../includes/config.php');
		extract($_POST);
		$acc_no = $_POST['acc_no'];
		$main_pay_date = $_POST['main_pay_date'];
		$main_amount_paid = (float)$_POST['main_amount_paid'];
		$main_discount = (float)$_POST['main_discount'];
		$main_or_no = 'MTF-' . $_POST['gcf_or'];

		$check_payment = "SELECT * FROM t_utility_payments WHERE c_st_or_no = '$main_or_no'";
		$check_payment_query = odbc_prepare($conn2, $check_payment);
		odbc_execute($check_payment_query);

		if (!odbc_fetch_row($check_payment_query)) {
		
			$params = "'$acc_no', '$main_or_no', '$main_pay_date', '$main_amount_paid', '$main_discount'";
			// Create the INSERT query using parameterized query
			$insert_query = "INSERT INTO t_utility_payments (c_account_no, c_st_or_no, c_st_pay_date, c_st_amount_paid, c_discount) VALUES ($params)";

			if (odbc_exec($conn2, $insert_query)) {
				$resp['status'] = 'success';
				$resp['msg'] = "Grasscutting Payment has been successfully added.";
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = "An error occurred.";
				$resp['err'] = odbc_errormsg($conn2) . " [$insert_query]";
			}

		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "OR number already exists!";
			return json_encode($resp);
			exit;
		}
		
		return json_encode($resp);
	}



	function save_stl_payment(){
		require_once('../includes/config.php');
		extract($_POST);
		$acc_no = $_POST['acc_no'];
		$stl_pay_date = $_POST['stl_pay_date'];
		$stl_amount_paid = (float)$_POST['stl_amount_paid'];
		$stl_discount = (float)$_POST['stl_discount'];
		$stl_or_no = 'STL-' . $_POST['stl_or_no'];

		$check_payment = "SELECT * FROM t_utility_payments WHERE c_st_or_no = '$stl_or_no'";
		$check_payment_query = odbc_prepare($conn2, $check_payment);
		odbc_execute($check_payment_query);

		if (!odbc_fetch_row($check_payment_query)) {
		
			$params = "'$acc_no', '$stl_or_no', '$stl_pay_date', '$stl_amount_paid', '$stl_discount'";
			// Create the INSERT query using parameterized query
			$insert_query = "INSERT INTO t_utility_payments (c_account_no, c_st_or_no, c_st_pay_date, c_st_amount_paid, c_discount) VALUES ($params)";

			if (odbc_exec($conn2, $insert_query)) {
				$resp['status'] = 'success';
				$resp['msg'] = "Streetlight Payment has been successfully added.";
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = "An error occurred.";
				$resp['err'] = odbc_errormsg($conn2) . " [$insert_query]";
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
	
		$sql = "DELETE FROM t_utility_bill WHERE c_due_date = '$date' and c_bill_type = '$type' and c_account_no = '$id'";
		$delete = odbc_exec($conn2, $sql);
		//echo $sql;
		if ($delete) {
			$resp['status'] = 'success';
			$resp['msg'] = "BILL has been deleted successfully.";
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = odbc_errormsg($conn2) . " [$sql]";
		}
		return json_encode($resp);
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
	case 'save_user':
		echo $Master->save_user();
	break;
	default:
		break;
}