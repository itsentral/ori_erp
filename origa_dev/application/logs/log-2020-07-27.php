<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2020-07-27 02:22:16 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-07-27 09:26:12 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-27 09:31:34 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-27 09:31:47 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-27 09:33:11 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-27 09:33:38 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-27 09:35:43 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-27 02:42:27 --> 404 Page Not Found: /index
ERROR - 2020-07-27 03:01:35 --> 404 Page Not Found: ../modules/quality_control/controllers/Quality_control/index
ERROR - 2020-07-27 03:03:29 --> 404 Page Not Found: ../modules/quality_control/controllers/Quality_control/index
ERROR - 2020-07-27 03:06:22 --> 404 Page Not Found: ../modules/quality_control/controllers/Quality_control/index
ERROR - 2020-07-27 03:08:28 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-07-27 03:08:52 --> 404 Page Not Found: ../modules/quality_control/controllers/Quality_control/index
ERROR - 2020-07-27 03:08:54 --> 404 Page Not Found: ../modules/quality_control/controllers/Quality_control/index
ERROR - 2020-07-27 03:10:53 --> 404 Page Not Found: ../modules/quality_control/controllers/Quality_control/index
ERROR - 2020-07-27 03:11:04 --> 404 Page Not Found: /index
ERROR - 2020-07-27 03:11:06 --> 404 Page Not Found: ../modules/quality_control/controllers/Quality_control/index
ERROR - 2020-07-27 03:11:12 --> 404 Page Not Found: ../modules/quality_control/controllers/Quality_control/index
ERROR - 2020-07-27 10:35:03 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'a.ket = 'not yet'
              AND a.id_product <> '0'  AND b.id_product = 'In' at line 15 - Invalid query: 
            SELECT
              (@row:=@row+1) AS nomor,
              a.id_produksi,
              a.tanggal_produksi,
              b.id_costcenter,
              a.id_product,
              a.`code`,
              a.remarks,
              a.ket
            FROM
              report_produksi_daily_detail a
              LEFT JOIN report_produksi_daily_header b ON a.id_produksi = b.id_produksi,
              (SELECT @row:=0) r
            WHERE 1=1
              a.ket = 'not yet'
              AND a.id_product <> '0'  AND b.id_product = 'Invalid date' AND (
              a.id_produksi LIKE '%%'
              OR c.id_product LIKE '%%'
              OR b.id_costcenter LIKE '%%'
              )
              
ERROR - 2020-07-27 10:35:12 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'a.ket = 'not yet'
              AND a.id_product <> '0'  AND b.id_product = 'In' at line 15 - Invalid query: 
            SELECT
              (@row:=@row+1) AS nomor,
              a.id_produksi,
              a.tanggal_produksi,
              b.id_costcenter,
              a.id_product,
              a.`code`,
              a.remarks,
              a.ket
            FROM
              report_produksi_daily_detail a
              LEFT JOIN report_produksi_daily_header b ON a.id_produksi = b.id_produksi,
              (SELECT @row:=0) r
            WHERE 1=1
              a.ket = 'not yet'
              AND a.id_product <> '0'  AND b.id_product = 'Invalid date' AND (
              a.id_produksi LIKE '%%'
              OR c.id_product LIKE '%%'
              OR b.id_costcenter LIKE '%%'
              )
              
ERROR - 2020-07-27 10:35:56 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'a.ket = 'not yet'
              AND a.id_product <> '0'  AND b.id_costcenter = ' at line 15 - Invalid query: 
            SELECT
              (@row:=@row+1) AS nomor,
              a.id_produksi,
              a.tanggal_produksi,
              b.id_costcenter,
              a.id_product,
              a.`code`,
              a.remarks,
              a.ket
            FROM
              report_produksi_daily_detail a
              LEFT JOIN report_produksi_daily_header b ON a.id_produksi = b.id_produksi,
              (SELECT @row:=0) r
            WHERE 1=1
              a.ket = 'not yet'
              AND a.id_product <> '0'  AND b.id_costcenter = 'Invalid date' AND (
              a.id_produksi LIKE '%%'
              OR c.id_product LIKE '%%'
              OR b.id_costcenter LIKE '%%'
              )
              
ERROR - 2020-07-27 10:36:43 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'a.ket = 'not yet'
              AND a.id_product <> '0'  AND (
              a' at line 15 - Invalid query: 
            SELECT
              (@row:=@row+1) AS nomor,
              a.id_produksi,
              a.tanggal_produksi,
              b.id_costcenter,
              a.id_product,
              a.`code`,
              a.remarks,
              a.ket
            FROM
              report_produksi_daily_detail a
              LEFT JOIN report_produksi_daily_header b ON a.id_produksi = b.id_produksi,
              (SELECT @row:=0) r
            WHERE 1=1
              a.ket = 'not yet'
              AND a.id_product <> '0'  AND (
              a.id_produksi LIKE '%%'
              OR c.id_product LIKE '%%'
              OR b.id_costcenter LIKE '%%'
              )
              
ERROR - 2020-07-27 10:37:09 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'a.ket = 'not yet'
              AND a.id_product <> '0'  AND (
              a' at line 15 - Invalid query: 
            SELECT
              (@row:=@row+1) AS nomor,
              a.id_produksi,
              a.tanggal_produksi,
              b.id_costcenter,
              a.id_product,
              a.`code`,
              a.remarks,
              a.ket
            FROM
              report_produksi_daily_detail a
              LEFT JOIN report_produksi_daily_header b ON a.id_produksi = b.id_produksi,
              (SELECT @row:=0) r
            WHERE 1=1
              a.ket = 'not yet'
              AND a.id_product <> '0'  AND (
              a.id_produksi LIKE '%%'
              OR a.id_product LIKE '%%'
              OR b.id_costcenter LIKE '%%'
              )
              
ERROR - 2020-07-27 03:43:22 --> Severity: error --> Exception: syntax error, unexpected ';', expecting ',' or ')' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/quality_control/models/Quality_control_model.php 73
ERROR - 2020-07-27 03:44:04 --> Severity: error --> Exception: syntax error, unexpected ';' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/quality_control/models/Quality_control_model.php 73
ERROR - 2020-07-27 03:45:23 --> Severity: error --> Exception: syntax error, unexpected ';' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/quality_control/models/Quality_control_model.php 73
ERROR - 2020-07-27 10:49:51 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'LEFT JOIN ms_inventory_category2 c ON a.id_product = c.id_category2,
          ' at line 15 - Invalid query: 
            SELECT
              (@row:=@row+1) AS nomor,
              a.id_produksi,
              a.tanggal_produksi,
              b.id_costcenter,
              a.id_product,
              a.`code`,
              a.remarks,
              a.ket,
              c.nama,
              d.nama_costcenter
            FROM
              report_produksi_daily_detail a
              LEFT JOIN report_produksi_daily_header b ON a.id_produksi = b.id_produksi,
              LEFT JOIN ms_inventory_category2 c ON a.id_product = c.id_category2,
              LEFT JOIN ms_costcenter d ON b.id_costcenter = d.id_costcenter,
              (SELECT @row:=0) r
            WHERE
              a.ket = 'not yet'
              AND a.id_product <> '0'  AND (
              a.id_produksi LIKE '%%'
              OR a.id_product LIKE '%%'
              OR b.id_costcenter LIKE '%%'
              )
              
ERROR - 2020-07-27 10:49:57 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'LEFT JOIN ms_inventory_category2 c ON a.id_product = c.id_category2,
          ' at line 15 - Invalid query: 
            SELECT
              (@row:=@row+1) AS nomor,
              a.id_produksi,
              a.tanggal_produksi,
              b.id_costcenter,
              a.id_product,
              a.`code`,
              a.remarks,
              a.ket,
              c.nama,
              d.nama_costcenter
            FROM
              report_produksi_daily_detail a
              LEFT JOIN report_produksi_daily_header b ON a.id_produksi = b.id_produksi,
              LEFT JOIN ms_inventory_category2 c ON a.id_product = c.id_category2,
              LEFT JOIN ms_costcenter d ON b.id_costcenter = d.id_costcenter,
              (SELECT @row:=0) r
            WHERE
              a.ket = 'not yet'
              AND a.id_product <> '0'  AND (
              a.id_produksi LIKE '%%'
              OR a.id_product LIKE '%%'
              OR b.id_costcenter LIKE '%%'
              )
              
ERROR - 2020-07-27 12:45:19 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'OR a.id_produksi LIKE '%%'
              OR a.id_product LIKE '%%'
           ' at line 24 - Invalid query: 
            SELECT
              (@row:=@row+1) AS nomor,
              a.id_produksi,
              a.tanggal_produksi,
              b.id_costcenter,
              a.id_product,
              a.`code`,
              a.remarks,
              a.ket,
              c.nama,
              d.nama_costcenter
            FROM
              report_produksi_daily_detail a
              LEFT JOIN report_produksi_daily_header b ON a.id_produksi = b.id_produksi
              LEFT JOIN ms_inventory_category2 c ON a.id_product = c.id_category2
              LEFT JOIN ms_costcenter d ON b.id_costcenter = d.id_costcenter,
              (SELECT @row:=0) r
            WHERE
              a.ket = 'not yet'
              AND a.id_product <> '0'
              
              AND (
              
              OR a.id_produksi LIKE '%%'
              OR a.id_product LIKE '%%'
              OR b.id_costcenter LIKE '%%'

              )
              
ERROR - 2020-07-27 12:47:29 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'OR a.id_produksi LIKE '%%'
              OR a.id_product LIKE '%%'
           ' at line 23 - Invalid query: 
            SELECT
              (@row:=@row+1) AS nomor,
              a.id_produksi,
              a.tanggal_produksi,
              b.id_costcenter,
              a.id_product,
              a.`code`,
              a.remarks,
              a.ket,
              c.nama,
              d.nama_costcenter
            FROM
              report_produksi_daily_detail a
              LEFT JOIN report_produksi_daily_header b ON a.id_produksi = b.id_produksi
              LEFT JOIN ms_inventory_category2 c ON a.id_product = c.id_category2
              LEFT JOIN ms_costcenter d ON b.id_costcenter = d.id_costcenter,
              (SELECT @row:=0) r
            WHERE
              a.ket = 'not yet'
              AND a.id_product <> '0'
              
              AND (
              OR a.id_produksi LIKE '%%'
              OR a.id_product LIKE '%%'
              OR b.id_costcenter LIKE '%%'
              
              )
              
ERROR - 2020-07-27 12:55:19 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near ')' at line 24 - Invalid query: 
            SELECT
              (@row:=@row+1) AS nomor,
              a.id_produksi,
              a.tanggal_produksi,
              b.id_costcenter,
              a.id_product,
              a.`code`,
              a.remarks,
              a.ket,
              c.nama,
              d.nama_costcenter
            FROM
              report_produksi_daily_detail a
              LEFT JOIN report_produksi_daily_header b ON a.id_produksi = b.id_produksi
              LEFT JOIN ms_inventory_category2 c ON a.id_product = c.id_category2
              LEFT JOIN ms_costcenter d ON b.id_costcenter = d.id_costcenter,
              (SELECT @row:=0) r
            WHERE
              a.ket = 'not yet'
              AND a.id_product <> '0'
              
              AND (
              
              )
              
ERROR - 2020-07-27 08:02:04 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-07-27 08:02:12 --> 404 Page Not Found: /index
ERROR - 2020-07-27 17:33:43 --> Query error: Unknown column 'a.no_plan' in 'where clause' - Invalid query: 
      SELECT
        (@row:=@row+1) AS nomor,
        b.id_costcenter,
        a.id_product,
        COUNT( a.id_product ) AS qty,
        c.nama,
        d.nama_costcenter
      FROM
        report_produksi_daily_detail a
        LEFT JOIN report_produksi_daily_header b ON a.id_produksi_h = b.id_produksi_h
        LEFT JOIN ms_inventory_category2 c ON a.id_product = c.id_category2
        LEFT JOIN ms_costcenter d ON b.id_costcenter = d.id_costcenter,
        (SELECT @row:=0) r
      WHERE 1=1  AND (
         a.no_plan LIKE '%%'
         OR c.nama LIKE '%%'
         OR b.costcenter LIKE '%%'
      )
      GROUP BY
        b.id_costcenter,
        a.id_product
    
ERROR - 2020-07-27 17:33:47 --> Query error: Unknown column 'a.no_plan' in 'where clause' - Invalid query: 
      SELECT
        (@row:=@row+1) AS nomor,
        b.id_costcenter,
        a.id_product,
        COUNT( a.id_product ) AS qty,
        c.nama,
        d.nama_costcenter
      FROM
        report_produksi_daily_detail a
        LEFT JOIN report_produksi_daily_header b ON a.id_produksi_h = b.id_produksi_h
        LEFT JOIN ms_inventory_category2 c ON a.id_product = c.id_category2
        LEFT JOIN ms_costcenter d ON b.id_costcenter = d.id_costcenter,
        (SELECT @row:=0) r
      WHERE 1=1  AND (
         a.no_plan LIKE '%%'
         OR c.nama LIKE '%%'
         OR b.costcenter LIKE '%%'
      )
      GROUP BY
        b.id_costcenter,
        a.id_product
    
ERROR - 2020-07-27 17:36:04 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'a.ket = 'good'
        AND a.id_product <> '0'
        AND (
          c.nama LI' at line 15 - Invalid query: 
      SELECT
        (@row:=@row+1) AS nomor,
        b.id_costcenter,
        a.id_product,
        COUNT( a.id_product ) AS qty,
        c.nama,
        d.nama_costcenter
      FROM
        report_produksi_daily_detail a
        LEFT JOIN report_produksi_daily_header b ON a.id_produksi_h = b.id_produksi_h
        LEFT JOIN ms_inventory_category2 c ON a.id_product = c.id_category2
        LEFT JOIN ms_costcenter d ON b.id_costcenter = d.id_costcenter,
        (SELECT @row:=0) r
      WHERE 1=1
        a.ket = 'good'
        AND a.id_product <> '0'
        AND (
          c.nama LIKE '%%'
          OR d.nama_costcenter LIKE '%%'
        )
      GROUP BY
        b.id_costcenter,
        a.id_product
    
ERROR - 2020-07-27 17:51:32 --> Query error: Unknown column 'id_cayegory2' in 'order clause' - Invalid query: SELECT * FROM ms_inventory_category2 ORDER BY id_cayegory2 ASC
ERROR - 2020-07-27 17:51:45 --> Query error: Unknown column 'id_cayegory2' in 'order clause' - Invalid query: SELECT * FROM ms_inventory_category2 ORDER BY id_cayegory2 ASC
ERROR - 2020-07-27 17:52:19 --> Query error: Unknown column 'id_cayegory2' in 'order clause' - Invalid query: SELECT * FROM ms_inventory_category2 ORDER BY id_cayegory2 ASC
ERROR - 2020-07-27 17:52:38 --> Query error: Unknown column 'id_cayegory2' in 'order clause' - Invalid query: SELECT * FROM ms_inventory_category2 ORDER BY id_cayegory2 ASC
ERROR - 2020-07-27 13:25:55 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
