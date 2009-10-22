<?php
require_once("class.wowcharacter.php");

class wowsignature{
  
  public $xml = null;
  private $width = null;
  private $height = null;
  private $main = null;
  private $path = null;
  private $character = null;
  private $lang = null;
  
  public function wowsignature($realm,$char,$lang='en-us'){
    if(empty($realm))throw new Exception('noName');// name is mandatory
    if(empty($char))throw new Exception('noRealm');// realm is mandatory
    if(!ereg('([a-z]{2})-([a-z]{2})',$lang,$this->lang))throw new Exception('invalidLang');// check lang
    $this->character = new wowcharacter($realm,$char,$this->lang[0]);
    $this->path = strtolower(sprintf('%s/%s/',$realm,$char));
    $this->xml = simplexml_load_file($this->path.sprintf('index.%s%s.xml',$this->lang[1],$this->lang[2]));
    if(empty($this->xml))throw new Exception(sprintf('%s [%s,%s]','xmlError',$realm,$char));
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
        $this->character->parse(strval($val['value'])),
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
  
}

?>
