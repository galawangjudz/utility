<?php

Class Master{

	function save_user(){
		require_once('../includes/config.php');
	
	}
	
	function save_account(){
		require_once('../includes/config.php');
		extract($_POST);

		$data = "c_account_no, c_control_no, c_location, c_last_name, c_first_name, c_middle_name, c_address, c_city_prov, c_zipcode, c_status, c_remarks, c_date_applied, c_lot_area, c_types, c_end_date";
		$values = "'$acc_no', '$ctr', '$pbl', '$lname', '$fname', '$mname', '$add', '$city_prov', '$zip_code', '$status', '$remarks', '$date_applied', '$lot_area', '$type', '$mtf_end'";

		if (empty($id)) {
			$sql = "INSERT INTO t_utility_accounts ($data) VALUES ($values)";
			#$sql = "INSERT INTO t_utility_accounts ($data)";
			#echo $sql;
		} else {
			$sql = "UPDATE t_utility_accounts SET ($data) = ($values) WHERE c_account_no = '$acc_no'";
		}
	
		$save = odbc_exec($conn2, $sql); // Assuming $sql is your INSERT/UPDATE query

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


}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);

switch ($action) {
	
	case 'save_account':
		echo $Master->save_account();
	break;
	case 'save_mtf_payment':
		echo $Master->save_mtf_payment();
	break;
	case 'save_stl_payment':
		echo $Master->save_stl_payment();
	break;
	case 'save_user':
		echo $Master->save_user();
	break;
	default:
		break;
}