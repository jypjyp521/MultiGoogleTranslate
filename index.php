<?php
header("Content-type: text/html;charset=utf-8");
//==============================================================================

require_once 'libs/PHPExcel/PHPExcel.php';
require_once 'libs/PHPExcel/PHPExcel/IOFactory.php';
require_once 'libs/GoogleTranslate.class.php';
//==============================================================================

$link = mysql_connect("127.0.0.1:3306","root","");

mysql_select_db("test",$link);

mysql_query("set names utf8");


//==============================================================================

function fetch_array($sql){
	$row = Array();
	$query = mysql_query($sql);
	
	if(($query = mysql_query($sql))==true)
	{
		while(($result = mysql_fetch_array($query,MYSQL_ASSOC))==true)
		array_push($row,$result);
	}
	return $row;
}

function _flush($msg)
{
	print_r ($msg);
	ob_flush();
	flush();
}

//==============================================================================


$act = $_GET['act'] && in_array($_GET['act'],array("index","import","export","test")) ? $_GET['act'] : 'index';




//==============================================================================
if($act == 'index'){
	$row = fetch_array("select * from translate where `to` = ''");
	$Google = new GoogleTranslate();
	if($_POST)
	{
		foreach($row as $v){
			$Google->text = $v['from'];
			$Google->from = $_POST['from'];
			$Google->to = $_POST['to'];
			$Google->translate();
			echo "<hr>";
			_flush($Google->result);
			$result = addslashes($Google->result);
			mysql_query("update translate set `to` ='".$result."' where `id` =".$v['id']." ");
		}
	}
	?>
	<form method="post" action="">
		from:<br>
		<select name="from">
		<?php
		foreach($Google->lang as $k=>$v){
			echo "<option value='".$v."' ".($Google->from == $v ? 'selected':'')." >".$k."</option>";
		}
		?>
		</select>
		<br>
		to:<br>
		<select name="to">
		<?php
		foreach($Google->lang as $k=>$v){
			echo "<option value='".$v."' ".($Google->to == $v ? 'selected':'')." >".$k."</option>";
		}
		?>
		</select>
		<br><br>
		<input type="submit" value="开始批量翻译"/>
		<input type="button" value="单条测试" onclick="location.href='?act=test'"/>
		<input type="button" value="导入数据" onclick="location.href='?act=import'"/>
		<input type="button" value="导出数据" onclick="location.href='?act=export'"/>
		
	</form>
<?php
}
//==============================================================================
if($act == 'import'){

	if($_POST){
		$pics = explode('.' , $_FILES["file"]["name"]);
		$num = count($pics); 
		if ($pics[$num-1] == 'xls'){
			if ($_FILES["file"]["error"] > 0){
				echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
			}else{
				 $file = 'xls/_import_'.time().'_'.rand(1111,9999).".xls";
				 if(file_exists($file)){
					  	echo $file . " already exists. ";
				 }else{
					 
					 	move_uploaded_file($_FILES["file"]["tmp_name"],$file);
					  	echo "Stored in: " . $file."<br>";
					  	if(!file_exists($file)){
					  		echo  " move_uploaded_file error. ";exit;
					  	}
					  	
					  	$reader = PHPExcel_IOFactory::createReader('Excel5'); // 读取 excel 文件
						$reader->setReadDataOnly(true);
						$reader = $reader->load($file);
						$data = $reader->getSheet(0)->toArray();
						//print_r($data);
						//exit;
						mysql_query("TRUNCATE TABLE translate");
						unset($data[1]);
						$i=0;
						foreach($data as $v){
							$i++."<br>";
							$v[1] = addslashes($v[0]);
							mysql_query("insert into `translate` values(null,'{$v[1]}','')");
						}
						echo "导入完成，共{$i}条数据";
						echo '<input type="button" value="开始翻译" onclick="location.href=\'?act=index\'"/>';
				}
			}
		  }else{
		  	echo "Invalid file";
		  }
	}


?>
<br>
<form action="" method="post" enctype="multipart/form-data">
	<label for="file">选择需要上传的xls文件:</label>
	<br /><br />
	<input type="file" name="file" id="file" /> 
	<br /><br />
<input type="submit" name="submit" value="上传导入" />
<input type="button" value="下载模板" onclick="location.href='xls/import_template.xls'"/>
</form>

<?php 
}
//==============================================================================
if($act == 'export'){
	function arrayToExcel($data){
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle('firstsheet');
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
		//add data
		$i = 2;
		foreach ($data as $line){
			//print_r($line);
			//$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $line['id']);
			//$objPHPExcel->getActiveSheet()->getCell('A'.$i)->setDataType('n');
			//$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $line['from']);
			//$objPHPExcel->getActiveSheet()->getCell('B'.$i)->setDataType('n');
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $line['to']);
			//$objPHPExcel->getActiveSheet()->getCell('C'.$i)->setDataType('n');
			$i++;
		}
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$file = 'xls/export_'.time().'.xls';
		$objWriter->save($file);
		return $file;
	}
	
	$row = fetch_array("select * from translate");
	
	$file = arrayToExcel($row);
	
	echo "导出 <a href='{$file}'>{$file}</a> 成功!";
?>
<?php 
}
//==============================================================================
if($act == 'test'){
	$Google = new GoogleTranslate();
	if($_POST['text'])
	{
		$Google->text = $_POST['text'];
		$Google->from = $_POST['from'];
		$Google->to = $_POST['to'];
		$Google->translate();
	}
?>
<form method="post" action="">
	from:<br>
	<select name="from">
	<?php
	foreach($Google->lang as $k=>$v){
		echo "<option value='".$v."' ".($Google->from == $v ? 'selected':'')." >".$k."</option>";
	}
	?>
	</select>
	<br>
	to:<br>
	<select name="to">
	<?php
	foreach($Google->lang as $k=>$v){
		echo "<option value='".$v."' ".($Google->to == $v ? 'selected':'')." >".$k."</option>";
	}
	?>
	</select>
	<br>
	from:<br>
    <textarea name="text" rows="10" cols="50"><?php
        echo $Google->text ? $Google->text : '';
    ?></textarea><br />
    result:<br>
    <textarea name="result" rows="10" cols="50"><?php
        echo $Google->result ? $Google->result : '';
    ?></textarea><br />
    <input type="submit" />
</form>
<?php 
}
//==============================================================================
if($act == 'help'){
?>


<?php 
}
?>