<?php

//DB info

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

$fullpage = file_get_contents("http://www.usccb.org/about/bishops-and-dioceses/all-dioceses.cfm");

$query = "SELECT * FROM dioceses";
$result = mysqli_query($db,$query);


while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
echo "\nchecking ".$row['name'];
	$page = $fullpage;
	// find where in the page the diocese is
	$position = strpos($page, $row['name']);

	//if it's found (it better be)
    if($position)
    {
    	//echo $position."\n";
	//get the entire page starting at where the diocese is
    	$substring = substr($page,$position);
	//what we're gonna delete from the beginning to get diocesan info like address
    	$baleet = $row['name']." </td>
		</tr>
		<tr valign=\"top\">
			<td style=\" margin-top:0px!important;font-size:16px!important;color:#555!important;\">";
    	//echo $substring;
	// separate the info into address and people
    	$infoarr = explode("<td class=\"personnel\">", $substring);
	//echo sizeof($infoarr)."\n";
	//DIOCESAN INFORMATION like address and stuff
    	$truinfo = $infoarr[0];
	//echo $truinfo;
	// get rid of junk trailing
    	$peoplearr = explode("</td>
		</tr>
		<tr>", $infoarr[1]);
        // get rid of junk trailing if there's a new state
        $peoplearr2 = explode("</tbody>
</table>", $peoplearr[0]);
    	$people = $peoplearr2[0];
	//what the hell does this do
    	$info = substr($truinfo,strlen($baleet)+1);
	//get rid of certain formatting..... doesnt work anymore
    	$info = str_replace(' class="subtle" style="font-size:11px;"', '', $info);
	// i have no clue what this does
        $info = preg_replace('/([a-z])([A-Z])/', '$1 $2',$info);
        $info = str_replace("  ", " ", $info);
    	$info = mysqli_real_escape_string($db,str_replace("</td>\n", "", $info));
    	$people = mysqli_real_escape_string($db,/*substr(*/$people/*,1)*/);
    	$query2 = "UPDATE dioceses SET addr='{$info}', bishop='{$people}' WHERE did='{$row['did']}'";
    	//echo $query2."\n\n";
		$result2 = mysqli_query($db,$query2);
		echo mysqli_error($db);

    } else {
		echo "Error for {$row['name']}\n";
    }
}

echo "Done";
