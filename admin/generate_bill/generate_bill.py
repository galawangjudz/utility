from common import *


import locale
from datetime import datetime, timedelta
from dateutil.relativedelta import relativedelta



# Set the locale to Philippines to use the appropriate formatting
locale.setlocale(locale.LC_ALL, 'en_PH')

class InsertBill(object):
    def __init__(self, cnx2, out ,bill_date):
        self.cnx2 = cnx2
        self.out = out
        l_csr1 = self.cnx2.cursor()
        l_csr2 = self.cnx2.cursor()
        l_csr3 = self.cnx2.cursor()
        #l_date_now = datetime.now().strftime("%Y-%m-%d")
        print(bill_date)
        l_date_now = bill_date
        select = (
        "select subquery.c_account_no, subquery.c_types, subquery.c_lot_area, subquery.monthly_interval_count, "
        "subquery.months_interval as c_months_interval, MAX(tb.c_end_date) as c_max_end_date "
        "FROM (SELECT tua.c_account_no, tua.c_types, tua.c_lot_area, count(*) as monthly_interval_count, "
        "( EXTRACT(YEAR FROM AGE(DATE('%s'), MAX(tub.c_end_date))) * 12 + EXTRACT(MONTH FROM AGE(DATE('%s'), "
        "MAX(tub.c_end_date)))) as months_interval FROM t_utility_bill tub "
        "INNER JOIN t_utility_accounts tua ON tub.c_account_no = tua.c_account_no "
        "WHERE tua.c_status = 'Active' and (tua.c_site != '101' and tua.c_site != '150' and tua.c_site != '999') "
        "GROUP BY tua.c_account_no, tua.c_types, tua.c_lot_area) as subquery "
        "JOIN t_utility_bill tb ON subquery.c_account_no = tb.c_account_no "
        "WHERE subquery.months_interval >= 1 and subquery.months_interval < '12' "
        "GROUP BY subquery.c_account_no, subquery.monthly_interval_count, subquery.months_interval, "
        "subquery.c_types, subquery.c_lot_area"
    ) % (l_date_now, l_date_now)

        print(select)
        l_csr1.execute(select)
        cust = get_row(l_csr1)
        #print select
        l_cust_list = []
        while cust:
            l_activate = 0
            l_account_no        =       cust[0]
            l_times_month       =       int(cust[4])
            l_edate             =       cust[5]
            l_type  	    = 	    cust[1]
            l_lot_area 	    = 	    cust[2]
            l_data = (l_account_no, l_lot_area, l_edate, l_type, l_times_month)
            l_cust_list.append(l_data)
            cust = get_row(l_csr1)
        ctr = 1
        counter = 0
        l_list = []
        while ctr <= len(l_cust_list):
            l_activate = 0
            l_account_no    = 	str(l_cust_list[ctr-1][0])
            l_lot_area      = 	l_cust_list[ctr-1][1]
            l_edate         =	str(l_cust_list[ctr-1][2])
            l_type          =	l_cust_list[ctr-1][3]
            l_times_month   =   l_cust_list[ctr-1][4]
            l_code 		= l_account_no[0:3]	
            l_list = self.calculate_dates_and_insert_bills(self,l_edate,l_account_no, l_times_month, l_type,l_lot_area)
		
            ctr += 1

    def calculate_dates_and_insert_bills(self,cnx2,l_edate, l_account_no, l_times_month, l_type, l_lot_area):
        l_csr2 = self.cnx2.cursor()
        l_csr3 = self.cnx2.cursor()
        l_csr4 = self.cnx2.cursor()
        l_csr5 = self.cnx2.cursor()
        l_csr6 = self.cnx2.cursor()
        l_csr7 = self.cnx2.cursor()
        l_csr8 = self.cnx2.cursor()
        l_csr9 = self.cnx2.cursor()
        l_csr10 = self.cnx2.cursor()


        l_list = []
        try:
            # Convert l_edate to a datetime object
            l_edate = datetime.strptime(l_edate, "%Y-%m-%d")

            # Add 1 day to l_edate
           

            print(l_account_no, l_times_month)

            l_edate += timedelta(days=1)
            
            for times in range(1, l_times_month + 1):
                # Calculate end_dte as one month - 1 day from l_edate
                end_dte = l_edate + relativedelta(months=1) - timedelta(days=1)

                # Calculate due_dte by adding 16 days to end_dte
                due_dte = end_dte + timedelta(days=16)

                # Format the dates as "MM/DD/YYYY" strings
                l_sdate = l_edate.strftime("%m/%d/%Y")
                l_edate = end_dte.strftime("%m/%d/%Y")
                l_ddate = due_dte.strftime("%m/%d/%Y")

                l_bal = self.get_prev_bill(l_account_no)
                date1 = datetime.strptime(l_sdate, '%m/%d/%Y')
                date2 = datetime.strptime('01/01/2024', '%m/%d/%Y') 

                if l_type != 'STL Only':
                    l_mainte_bal = self.get_mtf_prev_bal(cnx2, l_account_no)
                    if l_mainte_bal > 0 and date1 >= date2:
                        l_bill_type = 'DLQ_MTF'
                        get_last_mtf_bill = "SELECT c_amount_due as c_last_mtf_amt_due FROM t_utility_bill where c_account_no = '%s' and c_bill_type = 'MTF' ORDER BY c_due_date DESC LIMIT 1" % (l_account_no)
                        l_csr2.execute(get_last_mtf_bill)
                        data = get_row(l_csr2)
                        l_stlcount = txn._cursor.rowcount
                        l_amt_due = data.last_mtf_amt_due
                        if l_amt_due > l_mainte_bal:
                            l_amt_due = l_mainte_bal
                        else:
                            l_amt_due = l_amt_due	
                        l_sur_mtf = float(l_amt_due * 0.05)
                        l_mtf_sur_flag = l_bal + l_sur_mtf
                        l_previous_mtf = l_bal


                        insert_stl_sur = "insert into t_utility_bill (c_account_no,c_start_date,c_end_date,c_due_date,\
                        c_bill_type,c_amount_due,c_prev_balance) VALUES ('%s','%s','%s','%s','%s','%s','%s')"\
                        % (l_account_no,l_sdate,l_edate,l_ddate,l_bill_type,l_sur_mtf,l_previous_mtf)
                        
                        try:
                            
                            l_csr3.execute(insert_stl_sur)
                            l_csr3.connection.commit()
                            print("Insert surcharge successful")
                        except Exception as e:
                            print(f"Error inserting data surcharge: {e}")
                            return

                        l_prev_bal1 = l_mtf_sur_flag
                    else:
                        l_prev_bal1  = l_bal

                    get_curr_mtf = "select c_rate from t_maintenance_rate where c_start_effective_date <= '%s'\
				    and c_end_effective_date >= '%s'" % (l_sdate,l_sdate)
                    l_csr4.execute(get_curr_mtf)
                    data = get_row(l_csr4)
                    l_count = l_csr4.rowcount
                    if l_count == 0:
                        l_mtf_amt = 0.0
                        l_mtf_flag = l_prev_bal1 + l_mtf_amt
                        l_previous_mtf = l_prev_bal1	
                    else:
                        l_mtf_amt = float(l_lot_area * int(data[0]))
                        l_mtf_flag = l_prev_bal1 + l_mtf_amt
                        l_previous_mtf = l_prev_bal1
                        l_bill_type = 'MTF'
                        insert_mtf_bill = "insert into t_utility_bill (c_account_no,c_start_date,c_end_date,c_due_date,\
					    c_bill_type,c_amount_due,c_prev_balance) VALUES ('%s','%s','%s','%s','%s','%s','%s')"\
					        % (l_account_no,l_sdate,l_edate,l_ddate,l_bill_type,l_mtf_amt,l_previous_mtf)
                        l_activate = 1
                       
                        try:
                            l_csr5.execute(insert_mtf_bill)
                            l_csr5.connection.commit()
                            print("Insert mtf bill successful")
                        except Exception as e:
                            print(f"Error inserting mtf bill data: {e}")
                          
                            return
                    l_prev_bal2 = l_mtf_flag
                else:
                    l_prev_bal2 = l_bal


                l_street_bal = self.get_stl_prev_bal(cnx2, l_account_no)
                if l_street_bal > 0 and date1 >= date2:
                    l_bill_type = 'DLQ_STL'
                    get_last_stl_bill = "SELECT c_amount_due as c_last_stl_amt_due from t_utility_bill where c_account_no = '%s' and c_bill_type = 'STL' ORDER BY c_due_date DESC LIMIT 1" %(l_account_no)
                    #get_last_stl_bill = "SELECT c_amount_due as c_last_stl_amt_due from t_utility_bill where c_account_no = '%s' and c_bill_type = 'MTF' \
                    #			and c_end_date = (select c_end_date from t_utility_bill where  c_account_no = '%s' and c_bill_type = 'DLQ_MTF' order by c_end_date DESC Limit 1);" %(l_account_no,l_account_no)
                    l_csr6.execute(get_last_stl_bill)
                    data = get_row(l_csr6)
                    l_stlcount = l_csr6.rowcount
                    l_amt_due2 = data.last_stl_amt_due
                    if l_amt_due2 > l_street_bal:
                        l_amt_due2 = l_street_bal
                    else:
                        l_amt_due2 = l_amt_due2

                    l_sur_stl = float(l_amt_due2 * 0.05)
                    l_stl_sur_flag = l_prev_bal2 + l_sur_stl
                    l_previous_stl = l_prev_bal2

                    insert_stl_sur = "insert into t_utility_bill (c_account_no,c_start_date,c_end_date,c_due_date,\
                    c_bill_type,c_amount_due,c_prev_balance) VALUES ('%s','%s','%s','%s','%s','%s','%s')"\
                    % (l_account_no,l_sdate,l_edate,l_ddate,l_bill_type,l_sur_stl,l_previous_stl)				
                    try:
                        l_csr7.execute(insert_stl_sur)
                        l_csr7.connection.commit()
                        print("Insert surcharge STL successful")
                    except Exception as e:
                        print(f"Error inserting surcharge STL data: {e}")
                        return
                    l_prev_bal3 = l_stl_sur_flag
                else:
                    l_prev_bal3 = l_prev_bal2

	     		######## STL Amount ###########
                l_code = l_account_no[0:3]
                get_curr_stl = "select c_data from t_street_light_rate where c_code = '%s' and\
                c_start_effectivity_date <= '%s' and c_end_effectivity_date >= '%s'" % (l_code,l_sdate,l_sdate)
                l_csr8.execute(get_curr_stl)
                data = get_row(l_csr8)
                data_count = l_csr8.rowcount
                if data_count == 0:
                    l_stl_amt = 0
                    l_stl_flag = l_prev_bal3 + l_stl_amt
                    l_previous_stl = l_prev_bal3
                    l_prev_bal = l_previous_stl
                    l_bal = l_stl_flag
                else:
                    l_stl_amt = int(data[0].replace("s", ""))
                    l_stl_flag = l_prev_bal3 + l_stl_amt
                    l_previous_stl = l_prev_bal3
                    l_prev_bal = l_previous_stl
                    l_bal = l_stl_flag
                    l_bill_type2 = 'STL'
                    insert_stl_bill = "insert into t_utility_bill (c_account_no,c_start_date,c_end_date,c_due_date,\
                    c_bill_type,c_amount_due,c_prev_balance)VALUES ('%s','%s','%s','%s','%s','%s','%s')"\
                    % (l_account_no,l_sdate,l_edate,l_ddate,l_bill_type2,l_stl_amt,l_previous_stl)
                    l_activate = 1
                    try:
                        l_csr9.execute(insert_stl_bill)
                        l_csr9.connection.commit()
                        print("Insert STL bill successful")
                    except Exception as e:
                        print(f"Error inserting stl bill data: {e}")
                        
                update_utl_flags = "update t_utility_flags set c_billed_up_to_date = '%s', c_due_date = '%s',\
			    c_balance = '%s',c_begin_balance = '%s', c_begin_date = '%s' where c_account_no = '%s' "\
			    %(l_edate,l_ddate,l_bal, '0.00','2015-01-01', l_account_no)
                try:
                    if l_activate == 1:
                        l_csr10.execute(update_utl_flags)
                        l_csr10.connection.commit()
                        print("Updating flag successful")
                        l_datako = l_account_no, l_edate
                        l_list.append(l_datako)
                    else:
                        pass
                except Exception as e:
                    print(f"Error updating flag data: {e}")
                l_edate = end_dte
                l_activate = 0  
                l_edate += timedelta(days=1)
        except OSError as e:
                # Handle exceptions here (log or raise)
                print("Error:", str(e))
                raise

        return l_list

    def get_prev_bill(self, l_account_no):
        try:
            query = """
                SELECT COALESCE(SUM(c_amount_due - c_st_amount_paid - c_discount), 0) AS c_tot_bal
                FROM (
                    SELECT c_account_no, c_amount_due, c_due_date, NULL AS c_st_pay_date,
                        0 AS c_st_amount_paid, 0 AS c_discount, c_bill_type
                    FROM t_utility_bill AS hed
                    WHERE c_bill_type IN ('STL', 'DLQ_STL', 'MTF', 'DLQ_MTF')
                    UNION ALL
                    SELECT c_account_no, 0 AS c_amount_due, NULL AS c_due_date, c_st_pay_date,
                        c_st_amount_paid, c_discount, 
                        CASE WHEN c_st_or_no ~* '^MTF' THEN 'MTF' ELSE 'STL' END AS c_bill_type
                    FROM t_utility_payments
                    WHERE (c_st_or_no ~* '^MTF' OR c_st_or_no ~* '^STL')
                ) AS my_table
                WHERE c_account_no = %s
            """
            
            l_csr = self.cnx2.cursor()

            # Execute the query with the cursor
            l_csr.execute(query, (l_account_no,))
            result = l_csr.fetchone()

            if result is not None:
                l_tot_amt_due = float(result[0])
                return l_tot_amt_due
            else:
                # Return zero when no records are found
                return 0.0

        except Exception as e:
            # Handle exceptions here (log or raise)
            print("Error:", str(e))
            raise


    def get_mtf_prev_bal(self,cnx2, l_account_no):
        try:
            query = """
                SELECT COALESCE(SUM(CASE WHEN (c_bill_type = 'MTF' OR c_bill_type = 'DLQ_MTF') THEN c_amount_due - c_st_amount_paid - c_discount ELSE 0 END), 0) AS c_mtf_bal
                FROM (
                    SELECT hed.c_account_no,
                        hed.c_amount_due,
                        hed.c_due_date,
                        NULL AS c_st_pay_date,
                        0 AS c_st_amount_paid,
                        0 AS c_discount,
                        hed.c_bill_type
                    FROM t_utility_bill AS hed
                    WHERE hed.c_bill_type IN ('MTF', 'DLQ_MTF') AND hed.c_due_date <= (
                        SELECT MAX(c_due_date) AS last_due_date
                        FROM t_utility_bill
                        WHERE c_bill_type = 'MTF' AND c_account_no = %s
                    )
                    UNION ALL
                    SELECT tp.c_account_no,
                        0 AS c_amount_due,
                        NULL AS c_due_date,
                        tp.c_st_pay_date,
                        tp.c_st_amount_paid,
                        tp.c_discount,
                        CASE WHEN tp.c_st_or_no ~* '^MTF' THEN 'MTF' ELSE 'STL' END AS c_bill_type
                    FROM t_utility_payments tp
                    WHERE (tp.c_st_or_no ~* '^MTF') AND tp.c_st_pay_date <= (
                        SELECT DATE(MAX(c_due_date) + INTERVAL '2 DAY')
                        FROM t_utility_bill
                        WHERE c_bill_type = 'MTF' AND c_account_no = %s
                    )
                ) AS my_table
                WHERE c_account_no = %s
            """
            
            # Use self.cnx2 to create a cursor
            l_csr = self.cnx2.cursor()

            # Execute the query with the cursor
            l_csr.execute(query, (l_account_no, l_account_no, l_account_no))
            result = l_csr.fetchone()

            if result is not None:
                l_tot_amt_due = float(result[0])
                return l_tot_amt_due
            else:
                # Return zero when no records are found
                return 0.0

        except Exception as e:
            # Handle exceptions here (log or raise)
            print("Error:", str(e))
            raise


    def get_stl_prev_bal(self,cnx2, l_account_no):
        try:
            query = """
                SELECT COALESCE(SUM(CASE WHEN (c_bill_type = 'STL' OR c_bill_type = 'DLQ_STL') THEN c_amount_due - c_st_amount_paid - c_discount ELSE 0 END), 0) AS c_stl_bal
                FROM (
                    SELECT hed.c_account_no,
                        hed.c_amount_due,
                        hed.c_due_date,
                        NULL AS c_st_pay_date,
                        0 AS c_st_amount_paid,
                        0 AS c_discount,
                        hed.c_bill_type
                    FROM t_utility_bill AS hed
                    WHERE hed.c_bill_type IN ('STL', 'DLQ_STL') AND hed.c_due_date <= (
                        SELECT MAX(c_due_date) AS last_due_date
                        FROM t_utility_bill
                        WHERE c_bill_type = 'STL' AND c_account_no = %s
                    )
                    UNION ALL
                    SELECT tp.c_account_no,
                        0 AS c_amount_due,
                        NULL AS c_due_date,
                        tp.c_st_pay_date,
                        tp.c_st_amount_paid,
                        tp.c_discount,
                        CASE WHEN tp.c_st_or_no ~* '^STL' THEN 'STL' ELSE 'MTF' END AS c_bill_type
                    FROM t_utility_payments tp
                    WHERE (tp.c_st_or_no ~* '^STL') AND tp.c_st_pay_date <= (
                        SELECT DATE(MAX(c_due_date) + INTERVAL '2 DAY')
                        FROM t_utility_bill
                        WHERE c_bill_type = 'STL' AND c_account_no = %s
                    )
                ) AS my_table
                WHERE c_account_no = %s
            """

            # Use self.cnx2 to create a cursor
            l_csr = self.cnx2.cursor()

            # Execute the query with the cursor
            l_csr.execute(query, (l_account_no, l_account_no, l_account_no))
            result = l_csr.fetchone()

            if result is not None:
                l_tot_amt_due = float(result[0])
                return l_tot_amt_due
            else:
                # Return zero when no records are found
                return 0.0

        except Exception as e:
            # Handle exceptions here (log or raise)
            print("Error:", str(e))
            raise




if __name__ == '__main__':
    cnx2 = initialize()
    #InsertBill(cnx2)
    bill_date = sys.argv[1]
    InsertBill(cnx2,sys.stdout,bill_date)