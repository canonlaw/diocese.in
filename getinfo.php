<?php

header('Content-Type: text/html; charset=utf-8');
header("Access-Control-Allow-Origin: *");

//DB info
include('config.php');
$db = mysqli_connect('localhost',$dbuser,$dbpass,'diocese');
mysqli_set_charset($db,'utf8');
$result = '';

if($db === false) {
	echo mysqli_connect_error();
	exit;
}


$zip = str_pad((int)$_GET['zip'], 5, "0", STR_PAD_LEFT);

$query = "SELECT * FROM dioceses INNER JOIN zipcodes ON zipcodes.did=dioceses.did WHERE zipcodes.zip={$zip}";


$result = mysqli_query($db,$query);
if(mysqli_num_rows($result) == 0)
{
	exit("Invalid zip code.");
}

$printer = mysqli_fetch_array($result, MYSQLI_ASSOC);

// Get the latin name of the bishop
$bishWords = explode(" ", $printer['bishop']);
// no diocesan administrators though

switch (substr($printer['title'],0,3)) {
	case 'Dio':
		$title = "Diocesan Administrator";
		break;
	case 'Apo':
		$title = "Apostolic Administrator";
		break;
	case 'Bis':
		$title = "Bishop Emeritus";
		break;
	default:
		$title = "Diocesan Bishop";
		break;
}

if(strpos($printer['bishop'], "Diocesan Administrator") === false)
{
	$da = false;
	$bishName = $bishWords[1];

	if($bishName == "J." && $bishWords[2] == "Douglas")
		$bishName = "Douglas";

	$query2 = "SELECT * FROM englat WHERE eng='{$bishName}'";
	$result2 = mysqli_query($db,$query2);
	if(mysqli_num_rows($result2) > 0)
	{
		$latinName = mysqli_fetch_array($result2, MYSQLI_ASSOC);
		$latinName = "<br /><em>... et Antistite nostro {$latinName['lat']}...</em>";
	} else {
		$latinName = "";
	}
} else {
	$da = true;
	$latinName = "<br /><span style=\"font-style: italic;font-size:15px\">The name of diocesan administrators are not mentioned in the Canon.</span>";
}


if(!file_exists("./coa/{$printer['basezip']}.png"))
	echo '<img src="./coa/00000.png" align="right"><!-- no image for '.$printer["basezip"] .' -->';
else
	echo '<img src="./coa/'.$printer['basezip'].'.png" align="right">';
echo "You are in the...<br /><br /><strong>".$printer['name']."</strong><br />";
echo $printer['addr']."<br /><br />";

/*if(!$da)
	$term = "Diocesan Bishop";
else
	$term = "Diocesan Administrator";*/

echo "<strong><em>{$title}</strong></em><br />";
echo $printer['bishop']."{$latinName}<br /><br />";


if(trim($printer['auxbish']) != "")
{
	echo "<strong><em>Other Bishops</strong></em><br />";
	echo $printer['auxbish']."<br />";
}

echo "<div id=\"dataUpdated\">Diocesan data last updated ".$printer['lastupdated']."</div>";


$browser = get_browser(null,true);
if (strpos($browser['browser'], 'bot') === false && strpos($browser['browser'], 'Bot') === false && strpos($browser['browser'], 'Qwantify') === false && strpos($browser['browser'], 'pider') === false) {
	file_put_contents("/var/www/diolog/".date("Ymd").".log", date("g:i a")." - {$_SERVER['REMOTE_ADDR']} - {$zip} - {$printer['name']} : {$browser['platform']} {$browser['parent']} {$browser['browser']}\n", FILE_APPEND);
}
