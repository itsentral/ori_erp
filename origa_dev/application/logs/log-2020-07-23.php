<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2020-07-23 01:39:40 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-07-23 01:46:27 --> Severity: error --> Exception: syntax error, unexpected '=>' (T_DOUBLE_ARROW), expecting ')' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/controllers/Purchase.php 73
ERROR - 2020-07-23 01:46:31 --> Severity: error --> Exception: syntax error, unexpected '=>' (T_DOUBLE_ARROW), expecting ')' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/controllers/Purchase.php 73
ERROR - 2020-07-23 08:47:00 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 08:48:12 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 08:48:36 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 08:57:34 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 08:57:46 --> Query error: Unknown column 'modified_by' in 'field list' - Invalid query: UPDATE `tran_material_purchase_header` SET `total_material` = 39686, `modified_by` = '1', `modified_date` = '2020-07-23 08:57:46'
WHERE `no_po` = 'PO2007001'
ERROR - 2020-07-23 01:58:21 --> 404 Page Not Found: ../modules/purchase/controllers/Purchase/material_purchase
ERROR - 2020-07-23 01:59:03 --> 404 Page Not Found: ../modules/purchase/controllers/Purchase/material_purchase
ERROR - 2020-07-23 09:11:07 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 09:11:09 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 09:14:32 --> Severity: Notice --> Undefined offset: 2 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/models/Purchase_model.php 245
ERROR - 2020-07-23 09:14:32 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'asc  LIMIT 0 ,10' at line 17 - Invalid query: 
			SELECT
      (@row:=@row+1) AS nomor,
				a.*,
        a.weight AS weight
			FROM
				material_planning_footer a,
        (SELECT @row:=0) r
		    WHERE
          a.weight > 0
          AND (no_po is null OR no_po = '')
          AND a.sts_purchase='N'
          AND a.category='request'
          AND (
				        a.material LIKE '%%'
	            )

		 ORDER BY  asc  LIMIT 0 ,10 
ERROR - 2020-07-23 09:33:22 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 09:34:30 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 09:40:48 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-23 09:41:06 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-23 09:41:34 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-23 09:42:31 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-23 09:42:42 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-23 09:43:58 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-23 09:44:52 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-23 09:45:48 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-23 09:46:39 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-23 02:48:34 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-07-23 02:48:42 --> 404 Page Not Found: /index
ERROR - 2020-07-23 10:08:30 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-23 10:09:21 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-23 10:09:52 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-23 10:10:09 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-23 10:11:44 --> Severity: Notice --> Undefined variable: datgroupmenu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/menus/views/menus_form.php 70
ERROR - 2020-07-23 10:19:39 --> Severity: Notice --> Undefined index: id_supplier /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/controllers/Purchase.php 113
ERROR - 2020-07-23 10:19:39 --> Severity: Notice --> Undefined index: check /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/controllers/Purchase.php 114
ERROR - 2020-07-23 10:19:39 --> Severity: Warning --> Invalid argument supplied for foreach() /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/controllers/Purchase.php 116
ERROR - 2020-07-23 10:19:39 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/controllers/Purchase.php 150
ERROR - 2020-07-23 10:19:39 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/controllers/Purchase.php 150
ERROR - 2020-07-23 10:19:39 --> Could not find the language line "insert_batch() called with no data"
ERROR - 2020-07-23 03:36:53 --> 404 Page Not Found: /index
ERROR - 2020-07-23 03:37:03 --> 404 Page Not Found: /index
ERROR - 2020-07-23 10:53:19 --> Query error: Unknown column 'b.nm_gudang' in 'field list' - Invalid query: 
			SELECT
				a.*,
				b.nm_gudang
			FROM
			   ms_material a
		   WHERE 1=1 AND (
				a.code_company LIKE '%%'
				OR a.unit LIKE '%%'
				OR a.nm_material LIKE '%%'
	        )
		
ERROR - 2020-07-23 10:53:33 --> Severity: Notice --> Undefined index: search /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/models/Warehouse_material_model.php 43
ERROR - 2020-07-23 10:53:33 --> Severity: Notice --> Undefined index: order /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/models/Warehouse_material_model.php 44
ERROR - 2020-07-23 10:53:33 --> Severity: Notice --> Undefined index: order /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/models/Warehouse_material_model.php 45
ERROR - 2020-07-23 10:53:33 --> Severity: Notice --> Undefined index: start /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/models/Warehouse_material_model.php 46
ERROR - 2020-07-23 10:53:33 --> Severity: Notice --> Undefined index: length /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/models/Warehouse_material_model.php 47
ERROR - 2020-07-23 10:53:33 --> Query error: Unknown column 'b.nm_gudang' in 'field list' - Invalid query: 
			SELECT
				a.*,
				b.nm_gudang
			FROM
			   ms_material a
		   WHERE 1=1 AND (
				a.code_company LIKE '%%'
				OR a.unit LIKE '%%'
				OR a.nm_material LIKE '%%'
	        )
		
ERROR - 2020-07-23 10:53:33 --> Severity: Warning --> Cannot modify header information - headers already sent by (output started at /home/u643669649/domains/sentral.xyz/public_html/origa/system/core/Exceptions.php:272) /home/u643669649/domains/sentral.xyz/public_html/origa/system/core/Common.php 573
ERROR - 2020-07-23 10:53:33 --> Query error: Unknown column 'b.nm_gudang' in 'field list' - Invalid query: 
			SELECT
				a.*,
				b.nm_gudang
			FROM
			   ms_material a
		   WHERE 1=1 AND (
				a.code_company LIKE '%%'
				OR a.unit LIKE '%%'
				OR a.nm_material LIKE '%%'
	        )
		
ERROR - 2020-07-23 10:53:55 --> Query error: Unknown column 'b.nm_gudang' in 'field list' - Invalid query: 
			SELECT
				a.*,
				b.nm_gudang
			FROM
			   ms_material a
		   WHERE 1=1 AND (
				a.code_company LIKE '%%'
				OR a.unit LIKE '%%'
				OR a.nm_material LIKE '%%'
	        )
		
ERROR - 2020-07-23 10:57:27 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 10:57:33 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 11:00:10 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 11:00:15 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 11:00:20 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 11:00:22 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 11:25:09 --> Severity: Notice --> Undefined variable: title /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 18
ERROR - 2020-07-23 11:25:09 --> Severity: Notice --> Undefined variable: data_gudang /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 55
ERROR - 2020-07-23 11:25:09 --> Severity: Warning --> Invalid argument supplied for foreach() /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 55
ERROR - 2020-07-23 11:25:09 --> Severity: Notice --> Undefined variable: data_gudang /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 67
ERROR - 2020-07-23 11:25:09 --> Severity: Warning --> Invalid argument supplied for foreach() /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 67
ERROR - 2020-07-23 11:25:09 --> Severity: Notice --> Undefined variable: akses_menu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 77
ERROR - 2020-07-23 11:25:39 --> Severity: Notice --> Undefined variable: data_gudang /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 54
ERROR - 2020-07-23 11:25:39 --> Severity: Warning --> Invalid argument supplied for foreach() /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 54
ERROR - 2020-07-23 11:25:39 --> Severity: Notice --> Undefined variable: data_gudang /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 66
ERROR - 2020-07-23 11:25:39 --> Severity: Warning --> Invalid argument supplied for foreach() /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 66
ERROR - 2020-07-23 11:25:39 --> Severity: Notice --> Undefined variable: akses_menu /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 76
ERROR - 2020-07-23 11:25:57 --> Severity: Notice --> Undefined variable: data_gudang /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 54
ERROR - 2020-07-23 11:25:57 --> Severity: Warning --> Invalid argument supplied for foreach() /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 54
ERROR - 2020-07-23 11:25:57 --> Severity: Notice --> Undefined variable: data_gudang /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 66
ERROR - 2020-07-23 11:25:57 --> Severity: Warning --> Invalid argument supplied for foreach() /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 66
ERROR - 2020-07-23 11:26:00 --> Severity: Notice --> Undefined variable: data_gudang /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 54
ERROR - 2020-07-23 11:26:00 --> Severity: Warning --> Invalid argument supplied for foreach() /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 54
ERROR - 2020-07-23 11:26:00 --> Severity: Notice --> Undefined variable: data_gudang /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 66
ERROR - 2020-07-23 11:26:00 --> Severity: Warning --> Invalid argument supplied for foreach() /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 66
ERROR - 2020-07-23 11:26:56 --> Severity: Notice --> Undefined variable: data_gudang /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 54
ERROR - 2020-07-23 11:26:56 --> Severity: Warning --> Invalid argument supplied for foreach() /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 54
ERROR - 2020-07-23 11:26:56 --> Severity: Notice --> Undefined variable: data_gudang /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 66
ERROR - 2020-07-23 11:26:56 --> Severity: Warning --> Invalid argument supplied for foreach() /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 66
ERROR - 2020-07-23 11:27:11 --> Severity: Notice --> Undefined variable: data_gudang /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 54
ERROR - 2020-07-23 11:27:11 --> Severity: Warning --> Invalid argument supplied for foreach() /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 54
ERROR - 2020-07-23 11:27:11 --> Severity: Notice --> Undefined variable: data_gudang /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 66
ERROR - 2020-07-23 11:27:11 --> Severity: Warning --> Invalid argument supplied for foreach() /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 66
ERROR - 2020-07-23 11:27:53 --> Severity: Notice --> Undefined variable: data_gudang /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 54
ERROR - 2020-07-23 11:27:53 --> Severity: Warning --> Invalid argument supplied for foreach() /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 54
ERROR - 2020-07-23 11:27:53 --> Severity: Notice --> Undefined variable: data_gudang /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 66
ERROR - 2020-07-23 11:27:53 --> Severity: Warning --> Invalid argument supplied for foreach() /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 66
ERROR - 2020-07-23 04:28:02 --> 404 Page Not Found: ../modules/warehouse_material/controllers/Warehouse_material/list_warehouse
ERROR - 2020-07-23 04:28:02 --> 404 Page Not Found: ../modules/warehouse_material/controllers/Warehouse_material/list_ipp
ERROR - 2020-07-23 11:28:05 --> Severity: Notice --> Undefined variable: data_gudang /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 54
ERROR - 2020-07-23 11:28:05 --> Severity: Warning --> Invalid argument supplied for foreach() /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 54
ERROR - 2020-07-23 11:28:05 --> Severity: Notice --> Undefined variable: data_gudang /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 66
ERROR - 2020-07-23 11:28:05 --> Severity: Warning --> Invalid argument supplied for foreach() /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/incoming.php 66
ERROR - 2020-07-23 04:40:01 --> 404 Page Not Found: ../modules/warehouse_material/controllers/Warehouse_material/list_warehouse_ipp
ERROR - 2020-07-23 11:44:25 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 11:44:35 --> Severity: Notice --> Undefined variable: no_po /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/adjustment.php 5
ERROR - 2020-07-23 11:44:35 --> Severity: Notice --> Undefined variable: gudang /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/adjustment.php 6
ERROR - 2020-07-23 11:44:35 --> Severity: Notice --> Undefined variable: qBQdetailNum /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/adjustment.php 22
ERROR - 2020-07-23 11:46:24 --> Severity: Notice --> Undefined variable: no_po /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/adjustment.php 5
ERROR - 2020-07-23 11:46:24 --> Severity: Notice --> Undefined variable: gudang /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/adjustment.php 6
ERROR - 2020-07-23 11:46:24 --> Severity: Notice --> Undefined variable: qBQdetailNum /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/adjustment.php 22
ERROR - 2020-07-23 11:48:39 --> Severity: Notice --> Undefined variable: no_po /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/adjustment.php 5
ERROR - 2020-07-23 11:48:39 --> Severity: Notice --> Undefined variable: gudang /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/adjustment.php 6
ERROR - 2020-07-23 11:48:39 --> Severity: Notice --> Undefined variable: qBQdetailNum /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/adjustment.php 22
ERROR - 2020-07-23 11:49:21 --> Severity: Notice --> Undefined variable: no_po /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/adjustment.php 5
ERROR - 2020-07-23 11:49:21 --> Severity: Notice --> Undefined variable: gudang /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/adjustment.php 6
ERROR - 2020-07-23 11:49:21 --> Severity: Notice --> Undefined variable: result /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/adjustment.php 22
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 172
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 172
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 178
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 178
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 179
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 179
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 180
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 180
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 181
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 181
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 182
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 182
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 187
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 187
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 188
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 188
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 189
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 189
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 190
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 190
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 191
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 191
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 193
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 193
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 194
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 194
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 195
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 195
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 196
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 196
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 197
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 197
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 198
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 198
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 199
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 199
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 172
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 172
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 178
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 178
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 179
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 179
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 180
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 180
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 181
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 181
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 182
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 182
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 187
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 187
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 188
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 188
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 189
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 189
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 190
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 190
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 191
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 191
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 193
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 193
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 194
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 194
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 195
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 195
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 196
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 196
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 197
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 197
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 198
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 198
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 199
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 199
ERROR - 2020-07-23 14:20:37 --> Severity: Notice --> Undefined variable: ArrTunggal /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 226
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 172
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 172
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 178
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 178
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 179
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 179
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 180
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 180
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 181
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 181
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 182
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 182
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 187
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 187
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 188
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 188
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 189
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 189
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 190
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 190
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 191
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 191
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 193
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 193
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 194
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 194
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 195
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 195
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 196
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 196
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 197
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 197
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 198
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 198
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 199
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 199
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 172
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 172
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 178
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 178
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 179
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 179
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 180
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 180
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 181
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 181
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 182
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 182
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 187
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 187
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 188
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 188
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 189
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 189
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 190
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 190
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 191
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 191
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 193
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 193
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 194
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 194
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 195
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 195
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 196
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 196
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 197
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 197
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 198
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 198
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 199
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 199
ERROR - 2020-07-23 14:21:19 --> Severity: Notice --> Undefined variable: ArrTunggal /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 226
ERROR - 2020-07-23 14:23:27 --> Severity: Notice --> Undefined variable: ArrTunggal /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 225
ERROR - 2020-07-23 07:27:57 --> 404 Page Not Found: ../modules/warehouse_material/controllers/Warehouse_material/material_adjustment
ERROR - 2020-07-23 15:12:43 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 15:12:57 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 15:13:02 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 15:24:31 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 15:27:10 --> Query error: Column 'deleted' in where clause is ambiguous - Invalid query: SELECT a.*, SUM(a.qty) AS qty, b.satuan_packing, b.konversi, b.unit FROM tran_material_purchase_detail a LEFT JOIN ms_material b ON a.id_material=b.code_material WHERE a.no_po='PO2007004' AND deleted='N' GROUP BY id_material
ERROR - 2020-07-23 15:27:12 --> Query error: Column 'deleted' in where clause is ambiguous - Invalid query: SELECT a.*, SUM(a.qty) AS qty, b.satuan_packing, b.konversi, b.unit FROM tran_material_purchase_detail a LEFT JOIN ms_material b ON a.id_material=b.code_material WHERE a.no_po='PO2007004' AND deleted='N' GROUP BY id_material
ERROR - 2020-07-23 15:27:18 --> Query error: Column 'deleted' in where clause is ambiguous - Invalid query: SELECT a.*, SUM(a.qty) AS qty, b.satuan_packing, b.konversi, b.unit FROM tran_material_purchase_detail a LEFT JOIN ms_material b ON a.id_material=b.code_material WHERE a.no_po='PO2007004' AND deleted='N' GROUP BY id_material
ERROR - 2020-07-23 15:27:50 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 15:29:40 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 15:30:03 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 15:30:47 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 15:30:56 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 15:31:02 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 15:31:07 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 08:43:43 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-07-23 15:45:53 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 15:47:57 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 15:48:02 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 15:58:25 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:00:31 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:00:34 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:00:36 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:00:38 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:00:42 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:02:52 --> Query error: Unknown column 'b.satuan_packing' in 'field list' - Invalid query: SELECT a.*, b.satuan_packing, b.konversi, b.unit FROM tran_material_purchase_detail a LEFT JOIN ms_material ON a.id_material=b.code_material WHERE a.no_po='PO2007001' AND a.deleted='N'
ERROR - 2020-07-23 16:05:16 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:05:21 --> Query error: Unknown column 'b.satuan_packing' in 'field list' - Invalid query: SELECT a.*, b.satuan_packing, b.konversi, b.unit FROM tran_material_purchase_detail a LEFT JOIN ms_material ON a.id_material=b.code_material WHERE a.no_po='PO2007002' AND a.deleted='N'
ERROR - 2020-07-23 16:13:18 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:13:33 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:17:37 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:17:51 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:18:20 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:18:23 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:19:02 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:19:05 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:19:15 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:20:44 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:21:14 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:21:27 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:21:34 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:23:27 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:27:45 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:30:12 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_stock_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 190
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_rusak_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 191
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_stock_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 213
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_stock_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 214
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_booking_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 215
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_booking_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 216
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_rusak_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 217
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_stock_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 190
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_rusak_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 191
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_stock_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 213
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_stock_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 214
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_booking_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 215
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_booking_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 216
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_rusak_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 217
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_stock_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 190
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_rusak_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 191
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_stock_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 213
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_stock_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 214
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_booking_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 215
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_booking_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 216
ERROR - 2020-07-23 16:45:20 --> Severity: Notice --> Undefined property: stdClass::$qty_rusak_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 217
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_stock_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 190
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_rusak_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 191
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_stock_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 213
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_stock_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 214
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_booking_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 215
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_booking_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 216
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_rusak_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 217
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_stock_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 190
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_rusak_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 191
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_stock_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 213
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_stock_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 214
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_booking_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 215
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_booking_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 216
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_rusak_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 217
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_stock_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 190
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_rusak_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 191
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_stock_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 213
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_stock_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 214
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_booking_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 215
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_booking_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 216
ERROR - 2020-07-23 16:46:44 --> Severity: Notice --> Undefined property: stdClass::$qty_rusak_packing /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/controllers/Warehouse_material.php 217
ERROR - 2020-07-23 16:55:46 --> Severity: Notice --> Undefined index: jumlah_mat_pack /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/detail_adjustment.php 32
ERROR - 2020-07-23 16:55:46 --> Severity: Notice --> Undefined index: jumlah_mat_pack /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/detail_adjustment.php 32
ERROR - 2020-07-23 16:55:46 --> Severity: Notice --> Undefined index: jumlah_mat_pack /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/views/detail_adjustment.php 32
ERROR - 2020-07-23 16:56:49 --> Severity: Notice --> Undefined variable: id_bq /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/purchase/views/detail_purchase.php 4
ERROR - 2020-07-23 17:59:15 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/models/Warehouse_material_model.php 73
ERROR - 2020-07-23 17:59:15 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/models/Warehouse_material_model.php 73
ERROR - 2020-07-23 17:59:26 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/models/Warehouse_material_model.php 73
ERROR - 2020-07-23 17:59:26 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/models/Warehouse_material_model.php 73
ERROR - 2020-07-23 18:00:31 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/models/Warehouse_material_model.php 73
ERROR - 2020-07-23 18:00:31 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/models/Warehouse_material_model.php 73
ERROR - 2020-07-23 18:04:56 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/models/Warehouse_material_model.php 73
ERROR - 2020-07-23 18:04:56 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/models/Warehouse_material_model.php 73
ERROR - 2020-07-23 18:06:09 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/models/Warehouse_material_model.php 74
ERROR - 2020-07-23 18:06:09 --> Severity: Notice --> Trying to get property of non-object /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/models/Warehouse_material_model.php 74
ERROR - 2020-07-23 18:08:40 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/models/Warehouse_material_model.php 74
ERROR - 2020-07-23 11:09:16 --> Severity: error --> Exception: syntax error, unexpected 'echo' (T_ECHO) /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/models/Warehouse_material_model.php 74
ERROR - 2020-07-23 18:10:23 --> Severity: Notice --> Undefined offset: 0 /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/warehouse_material/models/Warehouse_material_model.php 74
ERROR - 2020-07-23 13:13:57 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-07-23 13:33:54 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
