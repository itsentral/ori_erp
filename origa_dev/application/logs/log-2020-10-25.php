<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2020-10-25 01:13:39 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-25 01:58:09 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-25 09:55:16 --> Severity: Notice --> Undefined property: CI::$sb /home/u643669649/domains/sentral.xyz/public_html/origa/application/third_party/MX/Controller.php 59
ERROR - 2020-10-25 09:55:16 --> Severity: error --> Exception: Call to a member function query() on null /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 28
ERROR - 2020-10-25 09:58:00 --> Severity: error --> Exception: syntax error, unexpected '{' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/views/index.php 65
ERROR - 2020-10-25 10:07:13 --> Severity: error --> Exception: syntax error, unexpected '" "' (T_CONSTANT_ENCAPSED_STRING), expecting ',' or ';' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/views/index.php 63
ERROR - 2020-10-25 06:59:43 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-25 11:07:43 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-25 11:26:59 --> Severity: error --> Exception: syntax error, unexpected ';', expecting ',' or ')' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 401
ERROR - 2020-10-25 18:36:40 --> Query error: Unknown column 'd.nama_product' in 'field list' - Invalid query: 
  			SELECT
          (@row:=@row+1) AS nomor,
          a.id_produksi,
          a.id_produksi_h,
          a.tanggal_produksi,
          b.id_costcenter,
          a.id_product,
          d.id_category1,
          d.nama_product
  			FROM
          report_produksi_daily_detail a
          LEFT JOIN report_produksi_daily_header b ON a.id_produksi_h = b.id_produksi_h
          LEFT JOIN ms_inventory_category2 d ON a.id_product = d.id_category2,
          (SELECT @row:=0) r
  		   WHERE 1=1 AND a.ket <> 'not yet' AND (
      				b.id_costcenter LIKE '%%'
              OR a.id_product LIKE '%%'
  	        )
        GROUP BY
          a.tanggal_produksi,
          b.id_costcenter,
          a.id_product
  		
ERROR - 2020-10-25 19:01:04 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
