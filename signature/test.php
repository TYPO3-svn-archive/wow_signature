<pre><?

require_once("class.wowcharacter.php");

$realm = 'blackhand';

$name = 'jobe';

$char = new wowcharacter($realm,$name,'de-de');

var_dump(
  $char,
  date("d.m.Y H:i:s",filemtime('blackhand/jobe/armory.de-de.xml'))
);

?></pre>