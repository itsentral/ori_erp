<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	date_default_timezone_set("Asia/Bangkok");

	function whiteCenterBold(){
		$styleArray = array(					  
            'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            ),
			'font' => array(
				'bold' => true,
			),
            'borders' => array(
              'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb'=>'000000')
                )
            )
        );
		return $styleArray;
	}

    function whiteRightBold(){
		$styleArray = array(					  
            'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
            ),
			'font' => array(
				'bold' => true,
			),
            'borders' => array(
              'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb'=>'000000')
                )
            )
        );  
		return $styleArray;
	}

    function whiteCenter(){
		$styleArray = array(					  
            'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            ),
            'borders' => array(
              'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb'=>'000000')
                )
            )
        );
		return $styleArray;
	}

  function mainTitle(){
    $styleArray = array(	
      'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb'=>'e0e0e0'),
      ),
      'font' => array(
        'bold' => true,
      ),
      'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
      )
    );
    return $styleArray;
  }

  function tableHeader(){
    $styleArray = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'e0e0e0'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
    return $styleArray;
  }

  function tableBodyCenter(){
    $styleArray = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		);
    return $styleArray;
  }

  function tableBodyLeft(){
    $styleArray = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		);
    return $styleArray;
  }

  function tableBodyRight(){
    $styleArray = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		);
    return $styleArray;
  }