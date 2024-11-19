<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2020-07-16 02:06:10 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-07-16 02:48:44 --> Severity: Warning --> require_once(/home/u643669649/domains/sentral.xyz/public_html/origa/application/libraries/includes/functions.php): failed to open stream: No such file or directory /home/u643669649/domains/sentral.xyz/public_html/origa/application/libraries/Mpdf.php 38
ERROR - 2020-07-16 02:48:44 --> Severity: Compile Error --> require_once(): Failed opening required '/home/u643669649/domains/sentral.xyz/public_html/origa/application/libraries/includes/functions.php' (include_path='.:/opt/alt/php70/usr/share/pear') /home/u643669649/domains/sentral.xyz/public_html/origa/application/libraries/Mpdf.php 38
ERROR - 2020-07-16 04:38:11 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-07-16 06:32:40 --> 404 Page Not Found: /index
ERROR - 2020-07-16 06:41:12 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-07-16 06:41:18 --> 404 Page Not Found: /index
ERROR - 2020-07-16 14:02:31 --> Severity: Notice --> Undefined property: CI::$engine_model /home/u643669649/domains/sentral.xyz/public_html/origa/application/third_party/MX/Controller.php 59
ERROR - 2020-07-16 14:02:31 --> Severity: error --> Exception: Call to a member function get_data() on null /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 589
ERROR - 2020-07-16 14:02:55 --> Severity: Notice --> Undefined property: CI::$produksi_model /home/u643669649/domains/sentral.xyz/public_html/origa/application/third_party/MX/Controller.php 59
ERROR - 2020-07-16 14:02:55 --> Severity: error --> Exception: Call to a member function get_data() on null /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 589
ERROR - 2020-07-16 14:03:21 --> Severity: Notice --> Undefined variable: data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 589
ERROR - 2020-07-16 14:03:47 --> Severity: Notice --> Undefined variable: data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 589
ERROR - 2020-07-16 14:07:32 --> Severity: Notice --> Undefined property: CI::$engine_model /home/u643669649/domains/sentral.xyz/public_html/origa/application/third_party/MX/Controller.php 59
ERROR - 2020-07-16 14:07:32 --> Severity: error --> Exception: Call to a member function get_data() on null /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 692
ERROR - 2020-07-16 07:12:28 --> 404 Page Not Found: ../modules/produksi/controllers/Produksi/get_planning
ERROR - 2020-07-16 14:16:32 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-16 14:17:09 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-16 14:17:45 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-16 14:18:41 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-16 10:00:18 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-07-16 10:00:26 --> 404 Page Not Found: /index
ERROR - 2020-07-16 10:00:28 --> 404 Page Not Found: /index
ERROR - 2020-07-16 10:00:31 --> 404 Page Not Found: /index
ERROR - 2020-07-16 17:11:56 --> Severity: Notice --> Undefined offset: 3 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/sales/models/Sales_model.php 149
ERROR - 2020-07-16 17:11:56 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'asc  LIMIT 0 ,10' at line 13 - Invalid query: 
			SELECT
      (@row:=@row+1) AS nomor,
				a.*,
        b.name_customer
			FROM
				sales_order_header a LEFT JOIN master_customer b ON a.code_cust=b.id_customer,
        (SELECT @row:=0) r
		   WHERE 1=1 AND a.deleted='N' AND (
				no_so LIKE '%%'
				OR name_customer LIKE '%%'
        OR shipping LIKE '%%'
	        )
		 ORDER BY   asc  LIMIT 0 ,10 
ERROR - 2020-07-16 17:11:58 --> Severity: Notice --> Undefined offset: 3 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/sales/models/Sales_model.php 149
ERROR - 2020-07-16 17:11:58 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'desc  LIMIT 0 ,10' at line 13 - Invalid query: 
			SELECT
      (@row:=@row+1) AS nomor,
				a.*,
        b.name_customer
			FROM
				sales_order_header a LEFT JOIN master_customer b ON a.code_cust=b.id_customer,
        (SELECT @row:=0) r
		   WHERE 1=1 AND a.deleted='N' AND (
				no_so LIKE '%%'
				OR name_customer LIKE '%%'
        OR shipping LIKE '%%'
	        )
		 ORDER BY   desc  LIMIT 0 ,10 
ERROR - 2020-07-16 17:38:11 --> Severity: Notice --> Undefined variable: tahun /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 710
ERROR - 2020-07-16 17:38:11 --> Severity: Notice --> Undefined variable: bulan /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 710
ERROR - 2020-07-16 17:38:11 --> Severity: Notice --> Undefined variable: tahun /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 711
ERROR - 2020-07-16 17:38:11 --> Severity: Notice --> Undefined variable: bulan /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 711
ERROR - 2020-07-16 17:38:28 --> Severity: Notice --> Undefined variable: header_num /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 755
ERROR - 2020-07-16 17:41:05 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'BERWEEN 2020-07-16 AND 2020-07-24' at line 1 - Invalid query: SELECT * FROM get_pro_plan_product WHERE delivery_date BERWEEN 2020-07-16 AND 2020-07-24 
ERROR - 2020-07-16 10:42:56 --> Severity: error --> Exception: syntax error, unexpected 'box' (T_STRING), expecting ',' or ')' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 712
ERROR - 2020-07-16 17:43:33 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'BERWEEN '2020-07-16' AND '2020-07-18'' at line 1 - Invalid query: SELECT * FROM get_pro_plan_product WHERE delivery_date BERWEEN '2020-07-16' AND '2020-07-18' 
ERROR - 2020-07-16 18:02:49 --> Severity: error --> Exception: Call to a member function diff() on string /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 710
ERROR - 2020-07-16 18:02:58 --> Severity: error --> Exception: Call to a member function diff() on string /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 710
ERROR - 2020-07-16 11:07:58 --> Severity: error --> Exception: syntax error, unexpected ';' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 732
ERROR - 2020-07-16 18:08:23 --> Severity: Notice --> Undefined variable: header_num /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 731
ERROR - 2020-07-16 18:08:23 --> Severity: Warning --> Division by zero /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 731
ERROR - 2020-07-16 12:00:49 --> Severity: error --> Exception: syntax error, unexpected ';', expecting ']' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 699
ERROR - 2020-07-16 12:01:04 --> Severity: error --> Exception: syntax error, unexpected ';', expecting ']' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 699
ERROR - 2020-07-16 20:08:15 --> Severity: Notice --> Undefined index: date-akhir /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 125
ERROR - 2020-07-16 13:18:02 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-07-16 23:25:48 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
