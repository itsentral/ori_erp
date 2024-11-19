<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2020-10-15 01:06:03 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-15 01:23:00 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-15 02:45:59 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-15 03:29:04 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-15 05:57:32 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-15 06:17:43 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-15 06:27:28 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-15 07:20:47 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-15 15:12:00 --> Severity: Notice --> Undefined index: detail /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 627
ERROR - 2020-10-15 15:12:00 --> Severity: Warning --> Invalid argument supplied for foreach() /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 627
ERROR - 2020-10-15 08:25:51 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-15 15:36:40 --> Severity: Notice --> Undefined offset: 8 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 349
ERROR - 2020-10-15 15:36:40 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'asc  LIMIT 0 ,100' at line 33 - Invalid query: 
          SELECT
            (@row:=@row+1) AS nomor,
            a.id,
            a.id_produksi,
            a.id_produksi_h,
            a.tanggal_produksi,
            b.id_costcenter,
            c.nama_costcenter,
            a.id_product,
            e.nama AS nm_product,
            f.nama AS nm_project,
            a.id_process,
            d.nm_process,
            a.`code`,
            a.ket,
            a.remarks,
            b.created_by,
            b.created_date
          FROM
            report_produksi_daily_detail a
            LEFT JOIN report_produksi_daily_header b ON a.id_produksi_h = b.id_produksi_h
            LEFT JOIN ms_costcenter c ON b.id_costcenter = c.id_costcenter
            LEFT JOIN ms_process d ON a.id_process = d.id
            LEFT JOIN ms_inventory_category2 e ON a.id_product = e.id_category2
            LEFT JOIN ms_inventory_category1 f ON e.id_category1 = f.id_category1,
            (SELECT @row:=0) r
          WHERE 1=1 AND a.sts_daycode = 'N'     AND a.sts_daycode = 'N' AND (
    				c.nama_costcenter LIKE '%%'
    				OR a.tanggal_produksi LIKE '%%'
            OR e.nama LIKE '%%'
            OR a.code LIKE '%%'
  	       )
  		 ORDER BY  asc  LIMIT 0 ,100 
ERROR - 2020-10-15 09:35:16 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-15 12:36:33 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-15 13:08:10 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-15 13:32:08 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-15 14:04:08 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-15 14:11:02 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
