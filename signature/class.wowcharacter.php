<?php

class wowcharacter{
  
  public $xml = null;
  public $realm = null;
  public $name = null;
  public $lang = null;
  
  public function wowcharacter($realm,$char,$lang='en-us'){
    if(empty($realm))throw new Exception('noName');// name is mandatory
    $this->realm = $realm;
    if(empty($char))throw new Exception('noRealm');// realm is mandatory      
    $this->name = $char;
    if(!ereg('([a-z]{2})-([a-z]{2})',$lang,$this->lang))throw new Exception('invalidLang');// check lang
    $this->load();
  }

  public function get($path,$parent=null){
    if(empty($parent))$parent=$this->xml->characterInfo;
    /*Abbreviations:*/
    if(ereg('stats:([a-zA-Z0-9]*)',$path,$regs))$path='characterTab:baseStats:'.$regs[1].':effective';
    $path = explode(':',$path);
    if(count($path)>1){
      $key = array_shift($path);
      return $this->get(implode(':',$path),$parent->$key);;
    }else{
      list($path,$options) = explode('|',$path[0]);
      if(!empty($parent[$path]))$path = strval($parent[$path]);else $path = strval($parent[intval($path)]);
      if($options)switch($options){
        case 'upper': $path = strtoupper($path);break;
        case 'lower': $path = strtolower($path);break;
        case 'int': $path = intval($path);break;
      }
      return $path;
    }
  }
  
  public function parse($str){
    while(ereg('{\$([a-zA-Z0-9:|]*)}',$str,$regs)) $str = str_replace('{$'.$regs[1].'}',$this->get($regs[1]),$str);
    return $str;
  }
  
  public function load(){
    $data = sprintf('%s/%s/armory.%s%s.xml',$this->realm,$this->name,$this->lang[1],$this->lang[2]);// cache file
    $xml = null;// xml data
    // query armory if no cache or cache too old
    if( !file_exists($data) || ( time() - filemtime($data) > 3600 ) ){
      libxml_use_internal_errors(true); libxml_clear_errors();
      libxml_set_streams_context(stream_context_create(array('http' => array(
        'user_agent' => sprintf('Mozilla/5.0 (Windows; U; Windows NT 5.1; %s; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6',$this->lang[1]),
        'header' => sprintf('Accept-language: %s',$this->lang[0]),
      ))));
      $xml = simplexml_load_file(utf8_encode(sprintf("http://armory.wow-europe.com/character-sheet.xml?r=%s&n=%s",$this->realm,$this->name)));
    }
    // try local cache
    if( empty($xml) || $xml->errorCode['value'] || $xml->characterInfo['errCode'] ) $xml = simplexml_load_file($data);
    // final check
    if(empty($xml))throw new Exception(sprintf('%s [%s,%s]','armoryNoReply',$this->realm,$this->name));
    if($xml->errorCode['value'])throw new Exception(sprintf('%s [%s,%s]',$xml->errorCode['value'],$this->realm,$this->name));
    if($xml->characterInfo['errCode'])throw new Exception(sprintf('%s [%s,%s]',$xml->characterInfo['errCode'],$this->realm,$this->name));
    // save data
    file_put_contents($data,$xml->asXML());
    $this->xml = $xml;
  }
  
}

?>
