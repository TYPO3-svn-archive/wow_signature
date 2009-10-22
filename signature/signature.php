<?php
try{

  require_once('class.wowsignature.php');

  switch($_GET['lang']){
    case 'en': $lang = 'en-us'; break;
    default: $lang = $_GET['lang'].'-'.$_GET['lang']; break;
  }

  $signature = new wowsignature($_GET['realm'],$_GET['char'],$lang);

  switch($_GET['type']){
    case 'gif': header("Content-type: image/gif"); imagegif($signature->create()); break;
    case 'jpeg': case 'jpg': header("Content-type: image/jpeg"); imagejpeg($signature->create()); break;
    default: header("Content-type: image/png"); imagepng($signature->create()); break;
  }

  print('<pre>');var_dump($signature);print('</pre>');

}catch (Exception $e){

  print("<pre>\n".$e->getMessage()."\n".$e->getTraceAsString()."\n</pre>\n");
  
}
?>
