<?php
class tx_wowsignature_signature{
  
  public $char = null;
  public $xml = null;
  private $width = null;
  private $height = null;
  private $main = null;
  private $path = null;
  private $lang = null;
  
  public function tx_wowsignature_signature($char){
    $this->path = 'uploads/tx_wowsignature/';
    $this->char = $char;
    $this->xml = simplexml_load_string($this->char->tx_wowsignature_sig_setup);
    if(empty($this->xml))throw new Exception('couldn\'t load data');
    $this->width = intval($this->xml["width"]);
    $this->height = intval($this->xml["height"]);
    $this->main = imagecreatetruecolor($this->width,$this->height);
  }
  
  private function text($image,$size,$angle,$x,$y,$color,$fontfile,$text,$alignright=false){// add an text
    $color = array_map('hexdec',str_split($color,2));// translate 6-digit-hex to rgb-array
    $color = imagecolorallocate($image,$color[0],$color[1],$color[2]);// allocate color
    if($alignright){
      $box = imagettfbbox($size,$angle,$fontfile,$text);
      $x = $x - ( $box[2] - $box[0] );
    }
    imagettftext($image,$size,$angle,$x,$y+$size,$color,$fontfile,utf8_decode($text));
  }

  private function image($image,$file,$x,$y){// add an image
    $pi = pathinfo($file);
    switch($pi['extension']){
      case 'png': $tmp = imagecreatefrompng($file); break;
      case 'gif': $tmp = imagecreatefromgif($file); break;
      case 'jpeg': case 'jpg': $tmp = imagecreatefromjpeg($file); break;
      default: throw new Exception('unsupportedFileFormat');
    }
    imagecopy($image,$tmp,$x,$y,0,0,imagesx($tmp),imagesy($tmp));
    imagedestroy($tmp);
  }

  private function parse($key,$val){
    switch($key){
      case 'image': $this->image(
        $this->main,
        $this->path.$val['src'],
        intval($val['x']),
        intval($val['y'])
      );break;
      case 'text': $this->text(
        $this->main,
        intval($val['size']),
        intval($val['angle']),
        intval($val['x']),
        intval($val['y']),
        ($val['color']?strval($val['color']):'ffffff'),
        ($val['font']?strtolower($this->path.$val['font'].'.ttf'):'tahomabd.ttf'),
        $this->char_parse(strval($val['value'])),
        !strcmp($val['align'],'right')
      );break;
      case 'random':
        $tmp = $val->xpath('*['.rand(1,count($val)).']');// get random child
        $this->parse($tmp[0]->getName(),$tmp[0]);// and parse it
      break;
    }
  }
  
  public function create(){
    foreach( $this->xml as $key => $val ) $this->parse($key,$val);
    return $this->main;
  }
  
  private function char_get($path,$parent=null){
    if(empty($parent))$parent=$this->char->xml->characterInfo;
    /*Abbreviations:*/
    if(ereg('stats:([a-zA-Z0-9]*)',$path,$regs))$path='characterTab:baseStats:'.$regs[1].':effective';
    $path = explode(':',$path);
    if(count($path)>1){
      $key = array_shift($path);
      return $this->char_get(implode(':',$path),$parent->$key);;
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
  
  private function char_parse($str){
    while(ereg('{\$([a-zA-Z0-9:|]*)}',$str,$regs)) $str = str_replace('{$'.$regs[1].'}',$this->char_get($regs[1]),$str);
    return $str;
  }
	
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_signature/inc/class.tx_wowsignature_signature.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_signature/inc/class.tx_wowsignature_signature.php']);
}
?>
