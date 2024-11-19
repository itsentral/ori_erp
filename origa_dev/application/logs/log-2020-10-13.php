<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2020-10-13 00:58:17 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-13 01:26:24 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-13 02:11:33 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-13 02:11:43 --> 404 Page Not Found: /index
ERROR - 2020-10-13 02:12:30 --> 404 Page Not Found: /index
ERROR - 2020-10-13 09:38:04 --> Severity: Notice --> Undefined index: detail /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 627
ERROR - 2020-10-13 09:38:04 --> Severity: Warning --> Invalid argument supplied for foreach() /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/controllers/Produksi.php 627
ERROR - 2020-10-13 09:40:57 --> Query error: Unknown column 'a.category' in 'where clause' - Invalid query: 
			SELECT
      (@row:=@row+1) AS nomor,
				a.*,
        b.code_company,
        b.nm_material,
        b.satuan_packing,
        b.konversi,
        b.unit
			FROM
				material_request_detail a LEFT JOIN ms_material b ON a.material=b.code_material,
        (SELECT @row:=0) r
		    WHERE
          a.weight > 0
          AND (a.no_po is null OR a.no_po = '')
          AND a.sts_purchase='N'
          AND a.category='request'
          AND (
				        a.material LIKE '%%'
	            )

		
ERROR - 2020-10-13 09:55:45 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-10-13 09:55:48 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-10-13 10:00:02 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-10-13 10:03:53 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-10-13 10:04:55 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-10-13 10:05:16 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-10-13 10:05:32 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-10-13 10:06:21 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-10-13 10:06:57 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-10-13 10:08:12 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-10-13 10:08:54 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-10-13 10:09:07 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-10-13 10:09:15 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-10-13 03:09:20 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-13 10:09:40 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-10-13 06:59:12 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-13 06:59:34 --> 404 Page Not Found: /index
ERROR - 2020-10-13 06:59:40 --> 404 Page Not Found: /index
ERROR - 2020-10-13 09:19:23 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-13 10:09:23 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-10-13 10:09:29 --> 404 Page Not Found: /index
ERROR - 2020-10-13 13:48:09 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
