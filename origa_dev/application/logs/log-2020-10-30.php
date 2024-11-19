<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2020-10-30 01:05:52 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-30 01:14:43 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-30 02:53:05 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-30 03:14:12 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-30 04:10:15 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-30 04:40:03 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-30 07:01:58 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-30 14:03:03 --> Severity: Notice --> Undefined offset: 4 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/sales/models/Sales_model.php 154
ERROR - 2020-10-30 14:03:03 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'asc  LIMIT 0 ,10' at line 13 - Invalid query: 
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
ERROR - 2020-10-30 14:03:08 --> Severity: Notice --> Undefined offset: 4 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/sales/models/Sales_model.php 154
ERROR - 2020-10-30 14:03:08 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'desc  LIMIT 0 ,10' at line 13 - Invalid query: 
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
ERROR - 2020-10-30 14:03:11 --> Severity: Notice --> Undefined offset: 4 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/sales/models/Sales_model.php 154
ERROR - 2020-10-30 14:03:11 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'asc  LIMIT 0 ,10' at line 13 - Invalid query: 
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
ERROR - 2020-10-30 14:03:13 --> Severity: Notice --> Undefined offset: 4 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/sales/models/Sales_model.php 154
ERROR - 2020-10-30 14:03:13 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'asc  LIMIT 10 ,10' at line 13 - Invalid query: 
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
		 ORDER BY   asc  LIMIT 10 ,10 
ERROR - 2020-10-30 14:03:16 --> Severity: Notice --> Undefined offset: 4 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/sales/models/Sales_model.php 154
ERROR - 2020-10-30 14:03:16 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'asc  LIMIT 10 ,10' at line 13 - Invalid query: 
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
		 ORDER BY   asc  LIMIT 10 ,10 
ERROR - 2020-10-30 14:03:35 --> Severity: Notice --> Undefined offset: 4 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/sales/models/Sales_model.php 154
ERROR - 2020-10-30 14:03:35 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'asc  LIMIT 10 ,10' at line 13 - Invalid query: 
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
		 ORDER BY   asc  LIMIT 10 ,10 
ERROR - 2020-10-30 07:05:07 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-30 07:27:37 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-30 08:14:00 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-30 08:35:30 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-30 09:12:29 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-30 10:37:20 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-30 11:02:45 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
