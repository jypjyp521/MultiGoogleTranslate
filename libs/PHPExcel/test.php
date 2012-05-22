<?php
	require_once 'PHPExcel.php';
	require_once 'PHPExcel/IOFactory.php';
	$reader = PHPExcel_IOFactory::createReader('Excel5'); // 读取 excel 文件
	$reader->setReadDataOnly(true);
	$reader = $reader->load("1111.xls");
	$data = $reader->getSheet(0)->toArray();
	print_r($data);
?>