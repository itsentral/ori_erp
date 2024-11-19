<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2020-08-02 06:47:41 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
ERROR - 2020-08-02 14:10:56 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near '' at line 24 - Invalid query: 
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
      WHERE
        a.ket = 'good'
        AND a.id_product <> '0'  AND b.id_costcenter = '' 
        AND (
          c.nama LIKE '%1%'
          OR d.nama_costcenter LIKE '%1%'
        )
      GROUP BY
        b.id_costcenter,
        a.id_product
     ORDER BY   0  LIMIT 10 , 
ERROR - 2020-08-02 14:12:43 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near '' at line 24 - Invalid query: 
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
      WHERE
        a.ket = 'good'
        AND a.id_product <> '0'  AND b.id_costcenter = '' 
        AND (
          c.nama LIKE '%1%'
          OR d.nama_costcenter LIKE '%1%'
        )
      GROUP BY
        b.id_costcenter,
        a.id_product
     ORDER BY   0  LIMIT 10 , 
ERROR - 2020-08-02 14:16:34 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near '' at line 24 - Invalid query: 
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
      WHERE
        a.ket = 'good'
        AND a.id_product <> '0'  AND b.id_costcenter = '' 
        AND (
          c.nama LIKE '%1%'
          OR d.nama_costcenter LIKE '%1%'
        )
      GROUP BY
        b.id_costcenter,
        a.id_product
     ORDER BY   0  LIMIT 10 , 
ERROR - 2020-08-02 14:18:11 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near '' at line 24 - Invalid query: 
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
      WHERE
        a.ket = 'good'
        AND a.id_product <> '0'  AND b.id_costcenter = '' 
        AND (
          c.nama LIKE '%1%'
          OR d.nama_costcenter LIKE '%1%'
        )
      GROUP BY
        b.id_costcenter,
        a.id_product
     ORDER BY   0  LIMIT 10 , 
ERROR - 2020-08-02 14:22:32 --> Query error: Unknown column 'd.nama_costcenter' in 'where clause' - Invalid query: 
      SELECT
        (@row:=@row+1) AS nomor,
        b.id_costcenter,
        a.id_product,
        COUNT( a.id_product ) AS qty,
        c.nama
      FROM
        report_produksi_daily_detail a
        LEFT JOIN report_produksi_daily_header b ON a.id_produksi_h = b.id_produksi_h
        LEFT JOIN ms_inventory_category2 c ON a.id_product = c.id_category2,
        (SELECT @row:=0) r
      WHERE
        a.ket = 'good'
        AND b.id_costcenter = 'CC2000001'
        AND a.id_product <> '0'  AND b.id_costcenter = '' 
        AND (
          c.nama LIKE '%%'
          OR d.nama_costcenter LIKE '%%'
        )
      GROUP BY
        a.id_product
    
ERROR - 2020-08-02 14:23:52 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'asc ,0' at line 21 - Invalid query: 
      SELECT
        (@row:=@row+1) AS nomor,
        b.id_costcenter,
        a.id_product,
        COUNT( a.id_product ) AS qty,
        c.nama
      FROM
        report_produksi_daily_detail a
        LEFT JOIN report_produksi_daily_header b ON a.id_produksi_h = b.id_produksi_h
        LEFT JOIN ms_inventory_category2 c ON a.id_product = c.id_category2,
        (SELECT @row:=0) r
      WHERE
        a.ket = 'good'
        AND b.id_costcenter = 'CC2000001'
        AND a.id_product <> '0' 
        AND (
          c.nama LIKE '%%'
        )
      GROUP BY
        a.id_product
     ORDER BY   1  LIMIT asc ,0 
ERROR - 2020-08-02 08:26:24 --> Severity: Notice --> Use of undefined constant php - assumed 'php' /home/u643669649/domains/sentral.xyz/public_html/origa/application/modules/users/views/login_animate.php 6
