<?php
	/*
	php script for a project about proteins' family find
	use PHPexcel for file reading and writing 
		*can be used as a sample for self using
	use Regular Expression to search and get the protein family and get it down
	*/
	$type=0;//variety to store the type of family found
	$result=array();//variety to store output data
	// $result[1]=array();
	// 	$result[1]['A']='ID';
	// 	$result[1]['B']='Unip Acc.';
	// 	$result[1]['C']='Family';
	// for ($i=2; $i<=2; $i++) {
	// 	$result[$i]=array();
	// 	$result[$i]['A']='BioPep00001';
	// 	$result[$i]['B']='E7AIL2';
	// 	$result[$i]['C']='Brevinin';
	// }
	//include important files
	include 'PHPExcel_1.8.0_doc/Classes/PHPExcel.php';
	include 'PHPExcel_1.8.0_doc/Classes/PHPExcel/Writer/Excel2007.php';

	// read the excel file
	// $input_file = "keywords_table_unipid.xlsx";//file path
	$input_file = "sample.xlsx";//file path
	$objPHPExcel = PHPExcel_IOFactory::load($input_file); 
	$sheetData = $objPHPExcel->getSheet(0)->toArray(null, true, true, true);
	for ($i=1; $i<=count($sheetData); $i++) {
		// var_dump($sheetData[$i]);//file reading check
		$result[$i]=array();
		$result[$i]['A']=$sheetData[$i]['A'];
		$result[$i]['B']=$sheetData[$i]['B'];
	}
	$result[1]['C']='Family';
	$result[1]['D']='type';
	//file reading end

	//search and get the family information down to local files
	for ($i=2; $i<=count($result); $i++) {
		$content=file_get_contents("http://www.uniprot.org/uniprot/".$result[$i]['B']);//get the information page of the protein
		//get the part of "family_and_domains"
		if (preg_match("/\<div\sclass\=\"section\s\"\sid\=\"family\_and\_domains\"\>[\s\S]*?\<div\sclass\=\"section\s\"\sid\=\"sequences\"\>/", $content, $matches)) {
			$content=$matches[0];
		}
		//jurdge the type of the family information
		if (preg_match("/Belongs\sto[\s\S]*?family\<\/a\>/", $content, $matches)) {
			//type 1: belongs to certain family
			$content=$matches[0];
			if (preg_match("/(?<=\>)[\s\S]*?(?=\sfamily)/", $content, $matches)) {
				$content=$matches[0];
			}
			$type=1;
		} else if (preg_match("/Family\sand\sdomain\sdatabases[\s\S]*?\<\/table\>/", $content, $matches)) {
			//type 2: have a certain family
			$content=$matches[0];
			if (preg_match("/InterPro[\s\S]*?\<\/tr\>/", $content, $matches)) {
				$content=$matches[0];
				if (preg_match("/(?<=\<\/a\>\s)[\s\S]*?(?=\.)/", $content, $matches)) {
				$content=$matches[0];
				}
			}
			$type=2;
		} else {
			//type 3: either of above
			$content='';
			$type=3;

		}
		$result[$i]['C']=$content;
		$result[$i]['D']=$type;
	}		
	//information get end


	//output 
	$objPHPExcel = new PHPExcel();//create an excel file
	$objPHPExcel->setActiveSheetIndex(0);//set current sheet
	$objPHPExcel->getActiveSheet()->setTitle('sheet1');//set the name of the sheet
	//set the value of each cell
	for ($i=1; $i<=count($result); $i++) {
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $result[$i]['A']);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $result[$i]['B']);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $result[$i]['C']);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $result[$i]['D']);
	}
	//file stroage
	//store as excel(2007 and before) format
	$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
	//or store as excel(2010 and after) format 
	// $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
	$objWriter->save('output'.".xls");
	//output and
?>