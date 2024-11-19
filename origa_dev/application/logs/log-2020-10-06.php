<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2020-10-06 01:05:09 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-06 01:29:48 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-06 08:34:59 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'asc  LIMIT 0 ,150' at line 12 - Invalid query: 
            SELECT
              (@row:=@row+1) AS nomor,
              a.*,
              b.name_customer
            FROM
              delivery_header a LEFT JOIN master_customer b ON a.code_cust=b.id_customer,
              (SELECT @row:=0) r
            WHERE 1=1 AND a.deleted='N' AND (
              a.no_delivery LIKE '%%'
              OR b.name_customer LIKE '%%'
            )
             ORDER BY   asc  LIMIT 0 ,150 
ERROR - 2020-10-06 01:38:49 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-06 01:54:46 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-06 09:45:16 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'asc  LIMIT 0 ,250' at line 15 - Invalid query: 
      SELECT
        (@row:=@row+1) AS nomor,
        a.id_product,
        c.nama,
        a.qty_stock
      FROM
        warehouse_product a
        LEFT JOIN ms_inventory_category2 c ON a.id_product = c.id_category2,
        (SELECT @row:=0) r
      WHERE 1=1 AND a.category='product'
        
        AND (
          a.id_product LIKE '%%'
        )
     ORDER BY   asc  LIMIT 0 ,250 
ERROR - 2020-10-06 09:45:18 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'desc  LIMIT 0 ,250' at line 15 - Invalid query: 
      SELECT
        (@row:=@row+1) AS nomor,
        a.id_product,
        c.nama,
        a.qty_stock
      FROM
        warehouse_product a
        LEFT JOIN ms_inventory_category2 c ON a.id_product = c.id_category2,
        (SELECT @row:=0) r
      WHERE 1=1 AND a.category='product'
        
        AND (
          a.id_product LIKE '%%'
        )
     ORDER BY   desc  LIMIT 0 ,250 
ERROR - 2020-10-06 09:45:19 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'asc  LIMIT 0 ,250' at line 15 - Invalid query: 
      SELECT
        (@row:=@row+1) AS nomor,
        a.id_product,
        c.nama,
        a.qty_stock
      FROM
        warehouse_product a
        LEFT JOIN ms_inventory_category2 c ON a.id_product = c.id_category2,
        (SELECT @row:=0) r
      WHERE 1=1 AND a.category='product'
        
        AND (
          a.id_product LIKE '%%'
        )
     ORDER BY   asc  LIMIT 0 ,250 
ERROR - 2020-10-06 09:45:19 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'desc  LIMIT 0 ,250' at line 15 - Invalid query: 
      SELECT
        (@row:=@row+1) AS nomor,
        a.id_product,
        c.nama,
        a.qty_stock
      FROM
        warehouse_product a
        LEFT JOIN ms_inventory_category2 c ON a.id_product = c.id_category2,
        (SELECT @row:=0) r
      WHERE 1=1 AND a.category='product'
        
        AND (
          a.id_product LIKE '%%'
        )
     ORDER BY   desc  LIMIT 0 ,250 
ERROR - 2020-10-06 09:45:24 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'desc  LIMIT 0 ,10' at line 15 - Invalid query: 
      SELECT
        (@row:=@row+1) AS nomor,
        a.id_product,
        c.nama,
        a.qty_stock
      FROM
        warehouse_product a
        LEFT JOIN ms_inventory_category2 c ON a.id_product = c.id_category2,
        (SELECT @row:=0) r
      WHERE 1=1 AND a.category='product'
        
        AND (
          a.id_product LIKE '%%'
        )
     ORDER BY   desc  LIMIT 0 ,10 
ERROR - 2020-10-06 09:45:31 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'desc  LIMIT 0 ,150' at line 15 - Invalid query: 
      SELECT
        (@row:=@row+1) AS nomor,
        a.id_product,
        c.nama,
        a.qty_stock
      FROM
        warehouse_product a
        LEFT JOIN ms_inventory_category2 c ON a.id_product = c.id_category2,
        (SELECT @row:=0) r
      WHERE 1=1 AND a.category='product'
        
        AND (
          a.id_product LIKE '%%'
        )
     ORDER BY   desc  LIMIT 0 ,150 
ERROR - 2020-10-06 09:45:42 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'desc  LIMIT 0 ,100' at line 15 - Invalid query: 
      SELECT
        (@row:=@row+1) AS nomor,
        a.id_product,
        c.nama,
        a.qty_stock
      FROM
        warehouse_product a
        LEFT JOIN ms_inventory_category2 c ON a.id_product = c.id_category2,
        (SELECT @row:=0) r
      WHERE 1=1 AND a.category='product'
        
        AND (
          a.id_product LIKE '%%'
        )
     ORDER BY   desc  LIMIT 0 ,100 
ERROR - 2020-10-06 02:56:43 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-06 04:45:27 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-06 04:45:33 --> 404 Page Not Found: /index
ERROR - 2020-10-06 04:45:45 --> 404 Page Not Found: /index
ERROR - 2020-10-06 06:19:57 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-06 06:20:05 --> 404 Page Not Found: /index
ERROR - 2020-10-06 07:11:34 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-06 07:11:39 --> 404 Page Not Found: /index
ERROR - 2020-10-06 14:23:26 --> Query error: Commands out of sync; you can't run this command now - Invalid query: UPDATE `ci_sessions` SET `timestamp` = 1601969006, `data` = '__ci_last_regenerate|i:1601968293;requested_page|s:14:\"produksireport\";previous_page|s:21:\"sales/add_so/SO200020\";app_session|a:15:{s:7:\"id_user\";s:1:\"1\";s:8:\"username\";s:4:\"json\";s:8:\"password\";s:60:\"$2y$10$3mCG9kfP43JcnKHV0cJOd.Pa/OOhYKWj0oXDxsnEL4jPCM.sT4dvO\";s:5:\"email\";s:16:\"json@sentral.com\";s:10:\"nm_lengkap\";s:4:\"Json\";s:6:\"alamat\";s:13:\"Jakarta Timur\";s:4:\"kota\";s:7:\"Jakarta\";s:2:\"hp\";s:12:\"085743482411\";s:5:\"kdcab\";s:3:\"100\";s:2:\"ip\";s:14:\"103.228.117.98\";s:14:\"login_terakhir\";s:19:\"2020-10-06 01:54:52\";s:8:\"st_aktif\";s:1:\"1\";s:5:\"photo\";N;s:10:\"created_on\";s:19:\"2016-09-28 05:47:57\";s:7:\"deleted\";s:1:\"0\";}'
WHERE `id` = 'fae087afd9d74f9f7c50a9763dbfc65dc6d62e8e'
ERROR - 2020-10-06 14:23:26 --> Query error: Commands out of sync; you can't run this command now - Invalid query: SELECT RELEASE_LOCK('fae087afd9d74f9f7c50a9763dbfc65dc6d62e8e') AS ci_session_lock
ERROR - 2020-10-06 07:28:58 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-06 08:06:17 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-06 09:53:51 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
