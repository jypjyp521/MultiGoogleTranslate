<?php 
/**
 * 
 *@author 尘缘
 *@copyright 2005-2009 admin@4wei.cn
 *@2012/5/10   xfeng修正
 *@version 1.0.0
 *@example 
 	//demo 1
	$Google->text = '这是一个基于Google在线翻译的工具';
	$Google->from = 'zh-CN';
	$Google->to = 'en';
	$Google->translate();
	//demo 2
	$Google = new GoogleTranslate('这是一个基于Google在线翻译的工具', 'zh-CN', 'en');
	echo $Google->result;
	//demo 3
	$Google = new GoogleTranslate();
	$Google->translate('这是一个基于Google在线翻译的工具', 'zh-CN', 'en');
	echo $Google->result;
?>

 */
class GoogleTranslate
{
	var $snoopy;
	var $result;
	var $dict;
	var $trans;
	var $lang=array(
			//"中文(繁体)"=>"zh-TW",
			"中文(简体)"=>"zh-CN",
			//"阿尔巴尼亚语"=>"sq",
			//"阿拉伯语"=>"ar",
			//"爱尔兰语"=>"ga",
			//"爱沙尼亚语"=>"et",
			//"白俄罗斯语"=>"be",
			//"保加利亚语"=>"bg",
			//"冰岛语"=>"is",
			//"波兰语"=>"pl",
			//"波斯语"=>"fa",
			//"布尔文(南非荷兰语)"=>"af",
			//"丹麦语"=>"da",
			"德语"=>"de",
			"俄语"=>"ru",
			"法语"=>"fr",
			"菲律宾语"=>"tl",
			//"芬兰语"=>"fi",
			//"海地克里奥尔语 ALPHA"=>"ht",
			"韩语"=>"ko",
			//"荷兰语"=>"nl",
			//"加利西亚语"=>"gl",
			//"加泰罗尼亚语"=>"ca",
			//"捷克语"=>"cs",
			//"克罗地亚语"=>"hr",
			//"拉脱维亚语"=>"lv",
			//"立陶宛语"=>"lt",
			//"罗马尼亚语"=>"ro",
			//"马耳他语"=>"mt",
			//"马来语"=>"ms",
			//"马其顿语"=>"mk",
			//"挪威语"=>"no",
			//"葡萄牙语"=>"pt",
			"日语"=>"ja",
			//"瑞典语"=>"sv",
			//"塞尔维亚语"=>"sr",
			//"斯洛伐克语"=>"sk",
			//"斯洛文尼亚语"=>"sl",
			//"斯瓦希里语"=>"sw",
			//"泰语"=>"th",
			//"土耳其语"=>"tr",
			//"威尔士语"=>"cy",
			//"乌克兰语"=>"uk",
			//"西班牙语"=>"es",
			//"希伯来语"=>"iw",
			//"希腊语"=>"el",
			//"匈牙利语"=>"hu",
			"意大利语"=>"it",
			//"意第绪语"=>"yi",
			//"印地语"=>"hi",
			//"印尼语"=>"id",
			"英语"=>"en",
			//"越南语"=>"vi",
		);
	var $text = '';
	var $from = 'zh-CN';
	var $to = 'en';
	var $GoogleURL = 'http://translate.google.com/translate_a/t';
	
	//php4构造函数
	function GoogleTranslate($text, $from, $to)
	{
		$this->__construct($text, $from, $to);
	}
	
	//php5构造函数
	function __construct($text='', $from='', $to='')
	{
		$snoopy = 'libs/Snoopy.class.php';
		if (is_file($snoopy))
		{
			require_once $snoopy;
			$this->snoopy = new Snoopy();
			$this->snoopy->agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.1.8) Gecko/20100202 Firefox/3.5.8';
			$this->snoopy->accept= 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
			$this->snoopy->referer = 'http://translate.google.cn/';
			$this->snoopy->rawheaders['Accept-Language'] = 'zh-cn,zh;q=0.5';
		}else{
			echo "file '$snoopy' do not exists!";
			exit;
		}
		
		$text && $this->text = $text;
		$from && $this->from = $from;
		$to && $this->to = $to;
		
		$this->text && $this->translate();
		
	}
	
	function setLang()
	{
		$Lang = $this->lang;

		if(!in_array($this->from, $Lang))
		{
			echo '尚不支持您要翻译的语种！';
			exit();
		}elseif(!in_array($this->to, $Lang))
		{
			echo '尚不支持您要翻译的目标语种！';
			exit();
		}
	}
	
	function translate($text='', $from='', $to='')
	{
		//exit($this->text);
		$text && $this->text = $text;
		$from && $this->from = $from;
		$to && $this->to = $to;
		$this->setLang();
		
		$vars['client'] = 't';
		$vars['hl'] = 'zh-CN';
		$vars['otf'] = '2';
		$vars['pc'] = '0';
		$vars['sl'] = $this->from;
		$vars['tl'] = $this->to;
		$vars['text'] = $this->text;
		
		
		if(!$this->text)
		{
			$this->result = NULL;
			return NULL;
		}
		$this->snoopy->submit($this->GoogleURL, $vars);
		$this->json = $this->snoopy->results;
		
		
		$this->setResult();
	}
	
	function setResult()
	{
		//echo ($this->json);
		$json = json_decode(preg_replace('/,{2,}/', ',', $this->json), true);
		//print_r($json);exit;
		$this->result = $this->trans = NULL;
		
      	foreach((array)$json[0] as $v){
      		$this->result.=$v[0];
      	}
      	
      	return $this->result;
      	
	}
	
}
?>