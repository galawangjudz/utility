#!/usr/bin/env python

#import pygtk
#pygtk.require("2.0")
#import pg, sys,gtk, libglade, _gnomeui, gnome	### old_version
#import pg, sys, gtk, gtk.glade, gnome, pango, pgdb
#import gobject
#from gtk import *
#from graphx import *
from string import *
#from gnome.ui import *
import os
import sys
from time import *
from calendar import *
#from utils import *
#from gengtkcode import *
from datetime import *

#import reportlab.rl_config
##reportlab.rl_config.warnOnMissingFontGlyphs = 0
#from reportlab.pdfbase import pdfmetrics
#from reportlab.pdfbase.ttfonts import TTFont

#pdfmetrics.registerFont(TTFont('Armata', 'Armata-Regular.ttf'))
#pdfmetrics.registerFont(TTFont('Novecento', 'Novecentowide-Bold.ttf'))


def main():
	rc_parse("gtkrc")
	mainloop()

def get_row(cursor):
    # Fetch a single row
    row = cursor.fetchone()
    return row

def atoi(a_value):
	try:
		i_value = int(a_value)
	except ValueError:
		i_value = 0
	return i_value

def atof(a_value):
	try:
		f_value = float(a_value)
	except ValueError:
		f_value = 0.0
	return f_value

def atof1(a_value):
	try:
		f_value = float(a_value)
	except ValueError:
		f_value = -1
	return f_value

def itoa(i_value):
	return "%d" % i_value

def ftoa(f_value):
	return "%.2f" % f_value

def ftoa1(f_value):
	return "%.8f" % f_value

def mtoi(m_value):
	m_value = replace(m_value, ',', '')
	return atoi(m_value)

def itom(i_value):
	m_value = ''
	while (i_value > 1000):
		t_value = i_value % 1000
		i_value = i_value // 1000
		m_value = ",%03d%s" % (t_value, m_value)
	###
	if (i_value == 1000):
		i_value = 1
		t_value = 0
		m_value = ",%03d%s" % (t_value, m_value)
	###
	m_value = "%d%s.00" % (i_value, m_value)
	return m_value

def itom1(i_value):
	i_value = mtof(ftom(i_value))
	m_value = ''
	d_len = atoi(len(ftoa(i_value)))
	d_val1 = ftoa(i_value)
	d_value  = d_val1[(d_len - 2):(d_len)]
	if (atoi(d_value) > 50):
		i_value = i_value + 1
	while (i_value > 1000):
		t_value = i_value % 1000
		i_value = i_value // 1000
		m_value = ",%03d%s" % (t_value, m_value)
###
	if (i_value == 1000):
		i_value = 1
		t_value = 0
		m_value = ",%03d%s" % (t_value, m_value)
###
	m_value = "%d%s.00" % (i_value, m_value)
	return m_value

def itom2(i_value):
	i_value = mtof(ftom(i_value))
	m_value = ''
	d_len = atoi(len(ftoa(i_value)))
	
	d_val1 = ftoa(i_value)
	d_value  = d_val1[(d_len - 2):(d_len)]
	if (atoi(d_value) > 50):
		i_value = i_value + 1
	while (i_value > 1000):
		t_value = i_value % 1000
		i_value = i_value // 1000
		m_value = ",%03d%s" % (t_value, m_value)
	###
	if (i_value == 1000):
		i_value = 1
		t_value = 0
		m_value = ",%03d%s" % (t_value, m_value)
	###
	m_value = "%d%s" % (i_value, m_value)
	return m_value

def mtof(m_value):
	m_value = replace(m_value, ',', '')
	return atof(m_value)

def mtof1(m_value):
	m_value = replace(m_value, ',', '')
	return atof1(m_value)

def mtoa(m_value):
	m_value = replace(m_value, ',', '')
	return m_value

def ftom(f_value):
	a_value = ftoa(f_value)
	i_value = atof(a_value[0:len(a_value)-3])
	m_value = a_value[len(a_value)-3:len(a_value)]
	while (i_value > 1000):
		t_value = i_value % 1000
		i_value = i_value // 1000
		m_value = ",%03d%s" % (t_value, m_value)
	###
	if (i_value == 1000):
		i_value = 1
		t_value = 0
		m_value = ",%03d%s" % (t_value, m_value)
	###
	m_value = "%d%s" % (i_value, m_value)
	return m_value

def xtoi(value):
        if int(value) == 0:
                return value
        else:
                value_str = str(value)
                length = len(value_str)

                if length <= 3:
                        l_format = value
                elif length == 4:
                        l_format = '%s,%s' % (value_str[0:1], value_str[1:4])
                elif length == 5:
                        l_format = '%s,%s' % (value_str[0:2], value_str[2:5])
                elif length == 6:
                        l_format = '%s,%s' % (value_str[0:3], value_str[3:6])
        
                return l_format

def GnomeOkDialog(l_mesg, win):
	dialog = gtk.MessageDialog(win,
					gtk.DIALOG_MODAL | gtk.DIALOG_DESTROY_WITH_PARENT,
					gtk.MESSAGE_INFO, gtk.BUTTONS_OK,
					'%s' % l_mesg)
	dialog.run()
	dialog.destroy()

def GnomeErrorDialog(l_mesg, win):
	dialog = gtk.MessageDialog(win,
					gtk.DIALOG_MODAL | gtk.DIALOG_DESTROY_WITH_PARENT,
					gtk.MESSAGE_ERROR, gtk.BUTTONS_CANCEL,
					'%s' % l_mesg)

	dialog.run()
	dialog.destroy()

def GnomeQuestionDialog(l_mesg,l_command,win):
	dialog = gtk.MessageDialog(win,
					gtk.DIALOG_MODAL | gtk.DIALOG_DESTROY_WITH_PARENT,
					gtk.MESSAGE_QUESTION, gtk.BUTTONS_YES_NO,
					'%s' % l_mesg)

	response = dialog.run()
	l_response = 1
	if response == gtk.RESPONSE_YES:
		l_response = 0
	elif response == gtk.RESPONSE_NO:
		l_response = 1
	dialog.destroy()
	return l_command(l_response)
		

def load_site_cbo(list, cnx):
	l_sql = "SELECT c_acronym FROM t_projects ORDER BY c_acronym"
	l_qry = cnx.query(l_sql)
	l_rows = l_qry.ntuples()
	l_rslt = l_qry.getresult()
	l_list = []
	if (l_rows > 0):
		for l_row in range(l_rows):
			l_list.append(strip(l_rslt[l_row][0]))
	list.set_popdown_strings(l_list)


def get_unit_status(lid, cnx):
	cnx.execute("SELECT c_unit_status FROM t_house where c_lid ~*'^%s'" % lid)
	l_house = get_row(cnx)
	if cnx.rowcount > 0:
		return l_house.unit_status
	else:
		return 'None'
		

def get_site_code(acronym, cnx):	
	if len(g_site) == 0:
		l_sql = "SELECT c_acronym, c_code FROM t_projects ORDER BY c_acronym ASC"
		l_qry = cnx.query(l_sql)
		if l_qry.ntuples() > 0:
			l_rsl = l_qry.getresult()
			for a in range(l_qry.ntuples()):
				acronym_strip = replace(upper(l_rsl[a][0]), '-', '')
				g_site[acronym_strip] = l_rsl[a][1]
	
	acronym_strip = replace(upper(acronym), '-', '')
	if g_site.has_key(acronym_strip):
		return g_site[acronym_strip]
	else:
		return 0

def get_site_acronym(code, cnx):
	if len(g_acro) == 0:
		l_sql = "SELECT c_code, c_acronym FROM t_projects ORDER BY c_code ASC"
		l_qry = cnx.query(l_sql)
		if l_qry.ntuples() > 0:
			l_rsl = l_qry.getresult()
			for a in range(l_qry.ntuples()):
				g_acro[l_rsl[a][0]] = l_rsl[a][1]

	code = int(code)
	if g_acro.has_key(code):
		return g_acro[code]
	else:
		return 'none'
	
def get_site_name(code, cnx):
	l_sql = "SELECT c_name FROM t_projects WHERE c_code = '%s'" % code
	l_qry = cnx.query(l_sql)
	if (l_qry.ntuples() > 0):
		l_rslt = l_qry.getresult()
		return l_rslt[0][0]
	else:
		return 'None'
	
def load_network_cbo(list,cnx):
	l_sql = "SELECT DISTINCT(c_network) FROM t_network ORDER by c_network"
	l_qry = cnx.query(l_sql)
	l_rows = l_qry.ntuples()
	l_rslt = l_qry.getresult()
	l_list = []
	if (l_rows > 0):
		for l_row in range(l_rows):
			l_value = strip(l_rslt[l_row][0])
			l_list.append(l_value)
	list.set_popdown_strings(l_list)

def load_division_cbo(list,net,cnx):
	l_sql = "SELECT c_division FROM t_network WHERE c_network = '%s' ORDER by c_division" % (net)
	l_qry = cnx.query(l_sql)
	l_rows = l_qry.ntuples()
	l_rslt = l_qry.getresult()
	l_list = []
	if (l_rows > 0):
		for l_row in range(l_rows):
			l_value = strip(l_rslt[l_row][0])
			l_list.append(l_value)
	list.set_popdown_strings(l_list)
			
def load_house_model_cbo(list, cnx):
	l_sql = "SELECT c_model FROM t_model_house ORDER BY c_model"
	l_qry = cnx.query(l_sql)
	l_rows = l_qry.ntuples()
	l_rslt = l_qry.getresult()
	l_list = []
	if (l_rows > 0):
		for l_row in range(l_rows):
			l_list.append(strip(l_rslt[l_row][0]))
	list.set_popdown_strings(l_list)

def load_user_cbo(list, cnx):
	l_sql = "SELECT c_realname FROM t_users ORDER BY c_realname"
	l_qry = cnx.query(l_sql)
	l_rows = l_qry.ntuples()
	l_rslt = l_qry.getresult()
	l_list = []
	if (l_rows > 0):
		for l_row in range(l_rows):
			l_list.append(strip(l_rslt[l_row][0]))
	list.set_popdown_strings(l_list)

def get_house_acronym(code, cnx):
	l_sql = "SELECT c_acronym FROM t_model_house WHERE c_model = '%s'" % code
	l_qry = cnx.query(l_sql)
	if (l_qry.ntuples() > 0):
		l_rslt = l_qry.getresult()
		return l_rslt[0][0]
	else:
		return 'none'

def get_house_model(code, cnx):
	l_sql = "SELECT c_model FROM t_model_house WHERE c_acronym = '%s'" % code
	l_qry = cnx.query(l_sql)
	if (l_qry.ntuples() > 0): 
		l_rslt = l_qry.getresult()
		return l_rslt[0][0]
	else:  
		return 'none'

def get_house_model_new(code, cnx):
	cnx.execute("SELECT * FROM t_model_house WHERE c_acronym = '%s'" % code)
	l_house = get_row(cnx)
	if cnx.rowcount > 0:
		return l_house.model
	else:
		return 'None'
		
def get_date(date1):
	t_y = atoi(date1[0:4]) 
	t_m = atoi(date1[5:7])
	t_d = atoi(date1[8:10])
	#print itoa(t_y) + '    ' + itoa(t_m)
	return t_y, t_m, t_d, 0, 0, 0

def char_rep(char1,num1):
	char2 = '' 
	for num1 in range(0,num1):
		char2 = char2 + char1
	return char2

def log_log(users,module,notes,cnx):
	l_date = strftime("%B %d, %Y", localtime())
	l_time = strftime("%I:%M:%S %p", localtime())
	l_sql = "INSERT INTO t_log (c_name, c_date, c_time, c_module,c_notes) VALUES('%s','%s','%s','%s','%s')" % (users,l_date,l_time,module,notes)
	try:
		cnx.query(l_sql)
	except pg.InternalError:
		GnomeOkDialog('Error in Log Record!',self.win)
		return

def initialize():

	import psycopg2
		#dbname='STLDB_v1',
		#user='postgres',
		#password='admin12345',
		#host='localhost',
		#port='5432'
	connection = psycopg2.connect(


		dbname='UTLDB_TEST',
		user='postgres',
		password='admin12345',
		host='localhost',
		port='5432'
	)

	return connection

def initialize_alter():
	#connection = pgdb.connect('fsmb002:DailyDB_Francis:::::')
	connection = pgdb.connect('fsmb002:CMIS_VALIDATION:::::')
	return connection


def print_bold():
	os.system("echo -e '\027\022' | lpr -l")
	os.system("echo -e '\033\105' | lpr -l")

def print_condensed():
	os.system("echo -e '\027\017' | lpr -l")
	os.system("echo -e '\033\106' | lpr -l")

def print_draft():
	os.system("echo -e '\027\022' | lpr -l")
	os.system("echo -e '\033\106' | lpr -l")

def print_formfeed():
	os.system("echo  | lpr -l")
	
def load_year_cbo(list, l_year):
	
	l_list = []
	l_yer = int(l_year)
	while (l_yer > 1993):
		l_yr = '%s' % l_yer
		l_list.append(l_yr)
		l_yer -= 1
		
	list.set_popdown_strings(l_list)
	
def numtoword(num1,mode1):
	a_value = ftoa(num1)
	i_value = atoi(a_value[0:len(a_value)-3])
	m_value = atoi(a_value[len(a_value)-2:len(a_value)])
	t_value = "%09d" % i_value
	t_word = ''
	if (atoi(t_value[0:3]) != 0 ):
		t_word = t_word + hundreds(t_value[0:3]) + 'Million '
	if (atoi(t_value[3:6]) != 0 ):
		t_word = t_word + hundreds(t_value[3:6]) + 'Thousand '
	if (atoi(t_value[6:9]) != 0):
		t_word = t_word + hundreds(t_value[6:9])
		
	if (atoi(m_value) != 0):
		#       if (mode1 == 0):
		#               m_value = "%03d" % m_value
		#               t_word = t_word + 'And ' +  self.tens(m_value) 
		#       elif (mode1 == 1):
		if (mode1 == 0):
			m_value = "%03d" % m_value
			t_word = t_word + 'and ' +  itoa(atoi(m_value)) + '/100 '
	else:
		if (mode1 == 0):
			t_word = t_word + 'and ' + '00/100 '
		
	return t_word

def hundreds(h_value):
	h_value1 = atoi(h_value[0:1])
	if (h_value1 == 0):
		word1 = tens(h_value)
	else:
		word1 = ones(h_value1) + 'Hundred ' + tens(h_value)
	return word1

def tens(t_value):
	val1 = atoi((t_value)[1:2])
	val2 = atoi((t_value)[2:3])
	if (val1 == 0):
		tens1 = ones(val2)
		return tens1
	if (val1 == 1):
		if (val2 == 0):
			tens1 = 'Ten '
		elif (val2 == 1):
			tens1 = 'Eleven '
		elif (val2 == 2):
			tens1 = 'Twelve '
		elif (val2 == 3):
			tens1 = 'Thirteen '
		elif (val2 == 4):
			tens1 = 'Fourteen '
		elif (val2 == 5):
			tens1 = 'Fifteen '
		elif (val2 == 6):
			tens1 = 'Sixteen '
		elif (val2 == 7):
			tens1 = 'Seventeen '
		elif (val2 == 8):
			tens1 = 'Eighteen '
		elif (val2 == 9):
			tens1 = 'Nineteen '
			
	elif (val1 == 2):
		tens1 = ones(val2)
		tens1 = 'Twenty ' + tens1
	elif (val1 == 3):
		tens1 = ones(val2)
		tens1 = 'Thirty ' + tens1
	elif (val1 == 4):
		tens1 = ones(val2)
		tens1 = 'Forty ' + tens1
	elif (val1 == 5):
		tens1 = ones(val2)
		tens1 = 'Fifty ' + tens1
	elif (val1 == 6):
		tens1 = ones(val2)
		tens1 = 'Sixty ' + tens1
	elif (val1 == 7):
		tens1 = ones(val2)
		tens1 = 'Seventy ' + tens1
	elif (val1 == 8):
		tens1 = ones(val2)
		tens1 = 'Eighty ' + tens1
	elif (val1 == 9):
		tens1 = ones(val2)
		tens1 = 'Ninety ' + tens1
	return tens1
	
def ones(o_value):
	if (o_value == 1):
		ones1 = 'One '
	elif (o_value == 2):
		ones1 = 'Two '
	elif (o_value == 3):
		ones1 = 'Three '
	elif (o_value == 4):
		ones1 = 'Four '
	elif (o_value == 5):
		ones1 = 'Five '
	elif (o_value == 6):
		ones1 = 'Six '
	elif (o_value == 7):
		ones1 = 'Seven '
	elif (o_value == 8):
		ones1 = 'Eight '
	elif (o_value == 9):
		ones1 = 'Nine '
	else:
		ones1 = ''
		
	return ones1






def roundForReport(floatvalue):
	l_roundoff = (round(floatvalue, -3))/1000
	#l_roundoff = round(floatvalue/1000, -3)
	#l_ftom_roundoff = ftom(l_roundoff)
	l_ftom_roundoff = str(l_roundoff)
	return l_ftom_roundoff

def rndFra(floatvalue):
	l_roundoff = (round(floatvalue, -3))/1000
	#l_roundoff = round(floatvalue/1000, -3)
	#l_ftom_roundoff = ftom(l_roundoff)
	l_ftom_roundoff = str(l_roundoff)
	return l_ftom_roundoff
