<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2020-09-01 01:10:36 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-09-01 02:21:35 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-09-01 02:22:24 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-09-01 09:25:59 --> Query error: Commands out of sync; you can't run this command now - Invalid query: UPDATE `ci_sessions` SET `timestamp` = 1598927159, `data` = '__ci_last_regenerate|i:1598922636;requested_page|s:33:\"index.phpengineget_planning2020-9\";previous_page|s:28:\"engine/add_material_planning\";app_session|a:15:{s:7:\"id_user\";s:1:\"1\";s:8:\"username\";s:4:\"json\";s:8:\"password\";s:60:\"$2y$10$3mCG9kfP43JcnKHV0cJOd.Pa/OOhYKWj0oXDxsnEL4jPCM.sT4dvO\";s:5:\"email\";s:18:\"arwant@sentral.cpm\";s:10:\"nm_lengkap\";s:4:\"Json\";s:6:\"alamat\";s:13:\"Jakarta Timur\";s:4:\"kota\";s:7:\"Jakarta\";s:2:\"hp\";s:12:\"085743482411\";s:5:\"kdcab\";s:0:\"\";s:2:\"ip\";s:15:\"114.124.161.208\";s:14:\"login_terakhir\";s:19:\"2020-08-31 07:51:02\";s:8:\"st_aktif\";s:1:\"1\";s:5:\"photo\";N;s:10:\"created_on\";s:19:\"2016-09-28 05:47:57\";s:7:\"deleted\";s:1:\"0\";}'
WHERE `id` = 'f595340f6b9cf2ec7f8fdd1a83afd507a1abd1a7'
ERROR - 2020-09-01 09:25:59 --> Query error: Commands out of sync; you can't run this command now - Invalid query: SELECT RELEASE_LOCK('f595340f6b9cf2ec7f8fdd1a83afd507a1abd1a7') AS ci_session_lock
ERROR - 2020-09-01 09:27:29 --> Query error: Commands out of sync; you can't run this command now - Invalid query: UPDATE `ci_sessions` SET `timestamp` = 1598927249, `data` = '__ci_last_regenerate|i:1598922636;requested_page|s:33:\"index.phpengineget_planning2020-9\";previous_page|s:28:\"engine/add_material_planning\";app_session|a:15:{s:7:\"id_user\";s:1:\"1\";s:8:\"username\";s:4:\"json\";s:8:\"password\";s:60:\"$2y$10$3mCG9kfP43JcnKHV0cJOd.Pa/OOhYKWj0oXDxsnEL4jPCM.sT4dvO\";s:5:\"email\";s:18:\"arwant@sentral.cpm\";s:10:\"nm_lengkap\";s:4:\"Json\";s:6:\"alamat\";s:13:\"Jakarta Timur\";s:4:\"kota\";s:7:\"Jakarta\";s:2:\"hp\";s:12:\"085743482411\";s:5:\"kdcab\";s:0:\"\";s:2:\"ip\";s:15:\"114.124.161.208\";s:14:\"login_terakhir\";s:19:\"2020-08-31 07:51:02\";s:8:\"st_aktif\";s:1:\"1\";s:5:\"photo\";N;s:10:\"created_on\";s:19:\"2016-09-28 05:47:57\";s:7:\"deleted\";s:1:\"0\";}'
WHERE `id` = 'f595340f6b9cf2ec7f8fdd1a83afd507a1abd1a7'
ERROR - 2020-09-01 09:27:29 --> Query error: Commands out of sync; you can't run this command now - Invalid query: SELECT RELEASE_LOCK('f595340f6b9cf2ec7f8fdd1a83afd507a1abd1a7') AS ci_session_lock
ERROR - 2020-09-01 09:38:05 --> Query error: Unknown column 'a.status_date' in 'where clause' - Invalid query: 
  			SELECT
  				a.*
  			FROM
  				daycode a
  		    WHERE a.tanggal <> ''    AND DATE(a.status_date) BETWEEN '2020-09-01' AND '2020-09-01' 
  			ORDER BY
  				a.id ASC
  		
ERROR - 2020-09-01 10:41:24 --> Severity: Notice --> Undefined index: tanggal /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/daycode/models/Daycode_model.php 49
ERROR - 2020-09-01 10:41:24 --> Severity: Notice --> Undefined index: bulan /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/daycode/models/Daycode_model.php 50
ERROR - 2020-09-01 10:41:24 --> Severity: Notice --> Undefined index: tahun /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/daycode/models/Daycode_model.php 51
ERROR - 2020-09-01 10:41:36 --> Severity: Notice --> Undefined index: tanggal /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/daycode/models/Daycode_model.php 49
ERROR - 2020-09-01 10:41:36 --> Severity: Notice --> Undefined index: bulan /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/daycode/models/Daycode_model.php 50
ERROR - 2020-09-01 10:41:36 --> Severity: Notice --> Undefined index: tahun /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/daycode/models/Daycode_model.php 51
ERROR - 2020-09-01 03:56:38 --> Severity: error --> Exception: syntax error, unexpected ':' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 255
ERROR - 2020-09-01 10:58:39 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 237
ERROR - 2020-09-01 10:58:39 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 237
ERROR - 2020-09-01 10:58:39 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 237
ERROR - 2020-09-01 10:58:39 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 237
ERROR - 2020-09-01 10:58:39 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 237
ERROR - 2020-09-01 10:58:39 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 237
ERROR - 2020-09-01 10:58:39 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 237
ERROR - 2020-09-01 10:58:39 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 237
ERROR - 2020-09-01 10:58:39 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 237
ERROR - 2020-09-01 10:58:39 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 237
ERROR - 2020-09-01 10:59:31 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:31 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:31 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:31 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:31 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:31 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:31 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:31 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:31 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:31 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:35 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:35 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:35 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:35 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:35 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:35 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:35 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:35 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:35 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:35 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:52 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:52 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:52 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:52 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:52 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:52 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:52 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:52 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:52 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 10:59:52 --> Severity: Notice --> Undefined variable: total_data /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 241
ERROR - 2020-09-01 11:01:15 --> Severity: Notice --> Undefined index: totalData /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/produksi/models/Produksi_model.php 224
ERROR - 2020-09-01 04:48:59 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-09-01 06:30:16 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-09-01 07:20:12 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-09-01 10:12:04 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-09-01 10:12:15 --> 404 Page Not Found: /index
ERROR - 2020-09-01 10:39:50 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
