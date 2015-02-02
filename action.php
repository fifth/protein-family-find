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
	$objPHPExcel = new PHPExcel();//create an excel file
	$objPHPExcel->setActiveSheetIndex(0);//set current sheet
	$objPHPExcel->getActiveSheet()->setTitle('sheet1');//set the name of the sheet

	// read the excel file
	// $input_file = "keywords_table_unipid.xlsx";//file path
	$input_file = "sample.xlsx";//file path
	$objPHPExcel = PHPExcel_IOFactory::load($input_file); 
	$sheetData = $objPHPExcel->getSheet(0)->toArray(null, true, true, true);
	$sheetData[1]['C']='Family';
	$sheetData[1]['D']='type';
	//file reading end

	//search and get the family information down to local files
	$i=2;
	echo "<table>";
	while ($sheetData[$i]['A']) {
		$content=file_get_contents("http://www.uniprot.org/uniprot/".$sheetData[$i]['B']);//get the information page of the protein
		//get the part of "family_and_domains"
			//type 3: either of above, default type
			$type=3;
		if (preg_match("/\<div\sclass\=\"section\s\"\sid\=\"family\_and\_domains\"\>[\s\S]*?\<div\sclass\=\"section\s\"\sid\=\"sequences\"\>/", $content, $matches)) {
			$content=$matches[0];
		}
		//jurdge the type of the family information
		if (preg_match("/Belongs\sto[\s\S]*?family\<\/a\>/", $content, $matches)) {
			//type 1: belongs to certain family
			$content=$matches[0];
			if (preg_match("/superfamily/", $content, $matches)) {
				if (preg_match("/(?<=\>)[\s\S]*?(?=\ssuperfamily)/", $content, $matches)) {
					$content=$matches[0];
					$type=1.5;
				}
			} else if (preg_match("/(?<=\>)[\s\S]*?(?=\sfamily)/", $content, $matches)) {
				$content=$matches[0];
				$type=1;
			}
		} else if (preg_match("/Family\sand\sdomain\sdatabases[\s\S]*?\<\/table\>/", $content, $matches)) {
			//type 2: have a certain family
			$content=$matches[0];
			if (preg_match("/InterPro[\s\S]*?\<\/tr\>/", $content, $matches)) {
				$content=$matches[0];
				if (preg_match("/(?<=\<\/a\>\s)[\s\S]*?(?=\.)/", $content, $matches)) {
				$content=$matches[0];
				$type=2;
				}
			}
		}
		if ($type==3) {
			$content='';
		}
		$sheetData[$i]['C']=$content;
		$sheetData[$i]['D']=$type;
		echo '<tr><td>'.$sheetData[$i]['A'].'</td><td>'.$sheetData[$i]['B'].'</td><td>'.$sheetData[$i]['C'].'</td><td>'.$sheetData[$i]['D'].'</td></tr>';
		$i++;
	}		
	//information get end


	//set the value of each cell
	// for ($i=1; $i<=count($result); $i++) {
	// 	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $result[$i]['A']);
	// 	$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $result[$i]['B']);
	// 	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $result[$i]['C']);
	// 	$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $result[$i]['D']);
	// 	// echo $result[$i]['A'].' '.$result[$i]['B'].' '.$result[$i]['C'].' '.$result[$i]['D'].'<br />';
	// }
	//file stroage

	//output 
	//store as excel(2007 and before) format
	// $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
	//or store as excel(2010 and after) format 
	// $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
	// $objWriter->save('output'.".xls");
	//output and
?>