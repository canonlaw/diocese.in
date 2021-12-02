<?php

if(php_sapi_name() != "cli")
    exit("CLI access only");

include("../config.php");
$db = mysqli_connect('localhost',$dbuser,$dbpass,'diocese');

mysqli_set_charset($db,'utf8');

$result = '';

if($db === false) {
	echo mysqli_connect_error();
	exit;
}


include("simple_html_dom.php");

$html = file_get_html("http://www.usccb.org/about/bishops-and-dioceses/all-dioceses.cfm");

foreach($html->find('div[class=node__content]') as $diocese)
{
	$dioName = mysqli_real_escape_string($db,$diocese->find('span[class*=field]', 0)->plaintext);
	$bishops = $diocese->find('div[class=staff]', 0);
	$bishopArray = array();

	// Finf the bishops
	foreach($bishops->find('div[class=item]') as $bp)
	{
		$bishopArray[] = array(
			'name' => mysqli_real_escape_string($db,trim($bp->find('div[class=name]', 0)->plaintext)),
			'position' => mysqli_real_escape_string($db,$bp->find('div[class=position]', 0)->plaintext)
		);
	}

	$bishop = $bishopArray[0]['name'];
	$title = $bishopArray[0]['position'];

	// Sort addtl bishops
	$auxbishes = '';
	for ($i=1; $i < sizeof($bishopArray); $i++) {
		$bishopArray[$i]['name'] = mysqli_real_escape_string($db,trim($bishopArray[$i]['name']));
		$bishopArray[$i]['position'] = mysqli_real_escape_string($db,trim($bishopArray[$i]['position']));
		$position = explode(" of ", $bishopArray[$i]['position']);
		$auxbishes .= "{$bishopArray[$i]['name']}\n<br /><em>{$position[0]}</em>\n<br />\n<br />";
	}

	// website and mailing address
	$addrLine2 = '';
	if(is_object($diocese->find('div[class*=field--name-field-da-address-2]', 0)))
		$addrLine2 = $diocese->find('div[class*=field--name-field-da-address-2]', 0)->plaintext."\n<br />";

	$fullAddr =
		$diocese->find('div[class*=field--name-field-da-address-1]', 0)->plaintext."\n<br />".
			$addrLine2.
			$diocese->find('div[class*=field--name-field-da-city]', 0)->plaintext.", ".
			$diocese->find('div[class*=field--name-field-da-state-abbreviation]', 0)->plaintext." ".
			$diocese->find('div[class*=field--name-field-da-zip-code]', 0)->plaintext."\n<br />".
			strip_tags($diocese->find('div[class*=field--name-field-da-site]', 0), "<a>")
		;
	$fullAddr = mysqli_real_escape_string($db,$fullAddr);

	//echo $fullAddr;exit;

	// Update shit
	$query = "UPDATE dioceses SET addr='{$fullAddr}', bishop='{$bishop}', title='{$title}', auxbish='{$auxbishes}' WHERE name='{$dioName}'";

	echo $query."<br /><br /><br />";
	$result = mysqli_query($db,$query);
	//echo mysqli_error($db);
}

 ?>
