<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<style>body{background-color:#a9a9a9;text-align:center;}</style>
<?
if($_POST['save']){
  $fp = fopen(strtolower(sprintf('%s/%s/index.xml',$_POST['realm'],$_POST['char'])),'w');
  fwrite($fp,$_POST['xml']);
  fclose($fp);
}
?>
<body>

<form action="" method="post">
  <input type="text" name="realm" value="<?=$_POST['realm']?>" />
  <input type="text" name="char" value="<?=$_POST['char']?>" />
  <input type="submit" value="LOAD"/>
</form>

<?if($_POST['realm']&&$_POST['char']){?>

<hr>

<img src="<?=strtolower(sprintf('/wow/signature/de/%s/%s.png',$_POST['realm'],$_POST['char']))?>" />

<hr>

<form action="" method="post">
  <input type="hidden" name="realm" value="<?=$_POST['realm']?>" />
  <input type="hidden" name="char" value="<?=$_POST['char']?>" />
  <textarea style="width:100%;" rows="20" wrap="off" name="xml"><?=file_get_contents(strtolower(sprintf('%s/%s/index.xml',$_POST['realm'],$_POST['char'])))?></textarea>
  <input type="submit" name="save" value="SAVE"/>
</form>
<textarea style="width:100%;" rows="10" wrap="off" ><?require_once("class.wowcharacter.php");$char = new wowcharacter($_POST['realm'],$_POST['char']);print($char->xml->characterInfo->asXML());?></textarea>

<?}?>

</body>