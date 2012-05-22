<?php
header("Content-Type: text/html;charset=utf-8");
$snoopy = 'libs/Snoopy.class.php';

require_once 'libs/Snoopy.class.php';
$snoopy = new Snoopy();
$snoopy->agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.1.8) Gecko/20100202 Firefox/3.5.8';
$snoopy->accept= 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
$snoopy->referer = 'http://translate.google.cn/';
$snoopy->rawheaders['Accept-Language'] = 'zh-cn,zh;q=0.5';
$snoopy->_submit_method = 'GET';


		
function ggsearch($url_s, $keyword, $page = 1) {
		global $snoopy;
        $enKeyword = urlencode($keyword);

        $rsState = false;

        $page_num = ($page -1) * 10;


        if ($page <= 10) {
                $url = "http://www.google.com.hk/search";
                $url.="?q=".$enKeyword;
                $url.="&hl=en";
                $url.="&start=".$page_num;
                $snoopy->fetch($url);
		 		$contents = $snoopy->results;

				file_put_contents("111.html", $contents);
				
                $match = "!<div\s*id=\"search\">(.*)</div>\s+<\!--z-->!";  
                preg_match_all("$match", "$contents", $line);
                print_r($line);exit;
                while (list ($k, $v) = each($line[0])) {
                        preg_match_all("!<h3\s+class=\"r\"><a[^>]+>(.*?)</a>!", $v, $title);
                        
                        $num = count($title[1]);
                        for ($i = 0; $i < $num; $i++) {
                                if (strstr($title[0][$i], $url_s)) {
                                        $rsState = true;
                                        $j = $i +1;
                                        $sum = $j + (($page) * 10 - 10);
                                        //echo $contents;
                                        echo "关键字" . $keyword . "<br>" . "排名：" . '<font color="red" size="20" style="">' . $sum . '</font>' . "####" . "第" . '<font color="#00FFFF" size="18" style="">'.$page . '</font>'. " 页" . "第" .'<font color="#8000FF" size="15" style="">'.$j . '</font>'. "名" . $title[0][$i] . "<br>";
                                        echo "<a href='" . $url . "'>" . "点击搜索结果" . "</a>" . "<br>";
                                        echo "<hr>";
                                        break;
                                }
                        }
                }
                unset ($contents);
                if ($rsState === false) {
                        ggsearch($url_s, $keyword, ++ $page); //找不到搜索页面的继续往下搜索

                }
        } else {

                echo '关键字' . $keyword . '10页之内没有该网站排名' . '<br>';
                echo "<hr>";
        }
}
if (!empty ($_POST['submit'])) {

        $time = explode(' ', microtime());
        $start = $time[0] + $time[1];
        $more_key = trim($_POST['textarea']);
        $url_s = trim($_POST['url']);
        if (!empty ($more_key) && !empty ($url_s)) {
                /*判断输入字符的规律*/
                if (strstr($more_key, "\n")) {
                        $exkey = explode("\n", $more_key);
                }
                if(strstr($more_key, "|")) {
                        $exkey = explode("|", $more_key);
                }
                if(!strstr($more_key, "\n")&&!strstr($more_key, "|")){
                $exkey=array($more_key);
                }
/*判断是否有www或者http://之类的东西*/
                if (count(explode('.', $url_s)) <= 2) {

                        $url = ltrim($url_s, 'http://www');
                        $url = 'www.' . $url_s;
                }
                foreach ($exkey as $keyword) {
                        //$keyword;
                        ggsearch($url_s, $keyword);
                }
                $endtime = explode(' ', microtime());

                $end = $endtime[0] + $endtime[1];

                echo '<hr>';
                echo '程序运行时间: ';
                echo $end - $start;
                //die();
        }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>抓取排名</title>

</head>

<body>
<form action="" method="post">


                        <span>关键字：</span> <textarea name="textarea" rows="20" cols="40" wrap="off">
格式例如：keyword1|keyword2|keyword3
  或者:      keyword1
          keyword2
          keyword3
  </textarea>


                        <span>url地址：</span><input type="text" name="url">

                        <input type="submit" name="submit" value="搜索">


</form>
</body>