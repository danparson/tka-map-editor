<?php
/*
[dimension-x][dimension-y]|[entrance-x][entrance-y]*|[gate-x][gate-y]*|tiles

 123456
1
2
3
4
5
6

Frame
a. Upper-left corner
b. Upper-right corner
c. Lower-left corner
d. Lower-right corner
e. Long grass
f. Short grass
g. Entrance
h. Castle gate
i. Castle wall

Frame Sides
1. Top
2. Right
3. Bottom
4. Left

Tiles
a. Blank 1
b. Blank 2
c. Blank 3
d. Road
e. Curve
f. Crossroads

Orientation
1. Top
2. Right
3. Bottom
4. Left

*/

error_reporting(0);

global $map, $map_frame;

function dbg()
{
	global $map, $map_frame;
	echo("<table border='1'>");
	for($i = 0; $i < count($map[0]); $i++)
	{
		echo("<tr>");
		for($j = 0; $j < count($map); $j++)
		{
			$tile = $map[$j][$i];
			//echo("<td>" . $map[$j][$i] . "</td>");
			echo("<td><img src='img/" . $tile . ".png' /></td>");
		}
		echo("</tr>");
	}
	echo("</table>");
	
	echo("<table border='1'>");
	for($i = 0; $i < count($map_frame[0]); $i++)
	{
		echo("<tr>");
		for($j = 0; $j < count($map_frame); $j++)
		{
			echo("<td>" . $map_frame[$j][$i] . "</td>");
		}
		echo("</tr>");
	}
	echo("</table>");
}

function create_map()
{
	global $map, $map_frame;

	$map_string = isset($_GET["m"]) ? $_GET["m"] : $_POST["m"];

	$map_chunks = explode(",", $map_string);

	if (count($map_chunks) != 3)
	{
		die("Invalid map string.");
	}

	$map_dims = $map_chunks[0];
	$map_frame_tiles = $map_chunks[1];
	$map_tiles = $map_chunks[2];

	$map_dim_x = $map_dims[0];
	$map_dim_y = $map_dims[1];

	$map = array_fill(0, $map_dim_x, array_fill(0, $map_dim_y, 0));
	$map_frame = array(array_fill(0, $map_dim_x - 1, 0), array_fill(0, $map_dim_y - 1, 0), array_fill(0, $map_dim_x - 1, 0), array_fill(0, $map_dim_y - 1, 0));

	// Place frame tiles
	$map_frame_tiles_top = substr($map_frame_tiles, 0, $map_dim_x - 1);
	$map_frame_tiles_right = substr($map_frame_tiles, $map_dim_x - 1, $map_dim_y - 1);
	$map_frame_tiles_bottom = substr($map_frame_tiles, $map_dim_x + $map_dim_y - 2, $map_dim_x - 1);
	$map_frame_tiles_left = substr($map_frame_tiles, (2 * $map_dim_x) + $map_dim_y - 3, $map_dim_y - 1);
	
	$map_frame = array(str_split($map_frame_tiles_top), str_split($map_frame_tiles_right), str_split($map_frame_tiles_bottom), str_split($map_frame_tiles_left));

	// Place tiles
	$map_tiles_chunks = str_split($map_tiles, 2);
	$t = 0;
	for ($i = 0; $i < $map_dim_y; $i++)
	{
		for ($j = 0; $j < $map_dim_x; $j++)
		{
			$map[$j][$i] = $map_tiles_chunks[$t];
			$t++;
		}
	}
}

function create_image()
{
	global $map, $map_frame;
	$tile_names = array("_a1", "_a2", "_a3", "_a4", "_b1", "_b2", "_b3", "_b4", "_c1", "_c2", "_c3", "_c4", "_d1", "_d2", "_d3", "_d4", "_e1", "_e2", "_e3", "_e4", "_f1", "_f2", "_f3", "_f4", "_g1", "_g2", "_g3", "_g4", "_h1", "_h2", "_h3", "_h4", "_i1", "_i2", "_i3", "_i4", "a1", "a2", "a3", "a4", "b1", "b2", "b3", "b4", "c1", "c2", "c3", "c4", "d1", "d2", "d3", "d4", "e1", "e2", "e3", "e4", "f1", "f2", "f3", "f4");
	
	$out_width = (count($map[0]) * 180) + 220;
	$out_height = (count($map) * 180) + 220;
	$out = imagecreatetruecolor($out_width, $out_height);
	
	// Allocate images
	foreach ($tile_names as $tn)
	{
		$$tn = imagecreatefrompng("img/{$tn}.png");
	}
	
	// Place corner tiles
	imagecopy($out, $_a1, 0, 0, 0, 0, 200, 200);
	imagecopy($out, $_b1, $out_width - 200, 0, 0, 0, 200, 200);
	imagecopy($out, $_c1, 0, $out_height - 200, 0, 0, 200, 200);
	imagecopy($out, $_d1, $out_width - 200, $out_height - 200, 0, 0, 200, 200);
	
	// Place border tiles: top, right, bottom, left
	for($i = 0; $i < count($map[0]) - 1; $i++)
	{
		if ($map_frame[0][$i] != "0")
		{
			$tile_name = "_" . $map_frame[0][$i] . "1";
			if (!in_array($tile_name, $tile_names))
				die("Invalid border tile.");
			imagecopy($out, ${$tile_name}, ($i * 180) + 200, 0, 0, 0, imagesx(${$tile_name}), imagesy(${$tile_name}));
		}
	}

	for($i = 0; $i < count($map[1]) - 1; $i++)
	{
		if ($map_frame[1][$i] != "0")
		{
			$tile_name = "_" . $map_frame[1][$i] . "2";
			if (!in_array($tile_name, $tile_names))
				die("Invalid border tile.");
			imagecopy($out, ${$tile_name}, $out_width - 110, ($i * 180) + 200, 0, 0, imagesx(${$tile_name}), imagesy(${$tile_name}));
		}
	}

	for($i = 0; $i < count($map[0]) - 1; $i++)
	{
		if ($map_frame[2][$i] != "0")
		{
			$tile_name = "_" . $map_frame[2][$i] . "3";
			if (!in_array($tile_name, $tile_names))
				die("Invalid border tile.");
			imagecopy($out, ${$tile_name}, ($i * 180) + 200, $out_height - 110, 0, 0, imagesx(${$tile_name}), imagesy(${$tile_name}));
		}
	}

	for($i = 0; $i < count($map[1]) - 1; $i++)
	{
		if ($map_frame[3][$i] != "0")
		{
			$tile_name = "_" . $map_frame[3][$i] . "4";
			if (!in_array($tile_name, $tile_names))
				die("Invalid border tile.");
			imagecopy($out, ${$tile_name}, 0, ($i * 180) + 200, 0, 0, imagesx(${$tile_name}), imagesy(${$tile_name}));
		}
	}
	
	// Place main tiles
	for($i = 0; $i < count($map[0]); $i++)
	{
		for($j = 0; $j < count($map); $j++)
		{
			if (!in_array($map[$j][$i], $tile_names))
				die("Invalid map tile.");
			imagecopy($out, ${$map[$j][$i]}, ($j * 180) + 110, ($i * 180) + 110, 0, 0, 180, 180);
			//$tile = $map[$j][$i];
			//echo("<td>" . $map[$j][$i] . "</td>");
			//echo("<td><img src='img/" . $tile . ".png' /></td>");
		}
	}
	
	header('Content-type: image/png');
	//header('Content-Disposition: attachment; filename="tka-map.png"');
	imagepng($out);
	imagedestroy($out);
	die();
}

if (isset($_GET["m"]))
{
	create_map();
	create_image();
}

?>

<html>
<head>
<title>The King's Armory Map Builder</title>
<script type="text/javascript">
function generateURL()
{
	var map_url = "http://www.danielparson.com/tka-map/?m=44,g0fe0fih0e0f," + document.getElementById("m1").value + document.getElementById("m2").value + document.getElementById("m3").value + document.getElementById("m4").value + document.getElementById("m5").value + document.getElementById("m6").value + document.getElementById("m7").value + document.getElementById("m8").value + document.getElementById("m9").value + document.getElementById("m10").value + document.getElementById("m11").value + document.getElementById("m12").value + document.getElementById("m13").value + document.getElementById("m14").value + document.getElementById("m15").value + document.getElementById("m16").value;
	var genurl_element = document.getElementById("genurl");
	var url_node = document.createTextNode(map_url);
	
	genurl_element.href = map_url;
	genurl_element.removeChild(genurl_element.firstChild);
	genurl_element.appendChild(url_node);
	
	return false;
}
</script>
</head>
<body>
<!-- img src="img/base.png" usemap="#tka-map" />
<map id="tka-map" name="tka-map">
<area shape="rect" coords="200,0,379,109" title="Top 1" href="1">
<area shape="rect" coords="380,0,559,109" title="Top 2" href="2">
<area shape="rect" coords="560,0,739,109" title="Top 3" href="3">
<area shape="rect" coords="830,200,940,379" title="Right 1" href="4">
<area shape="rect" coords="830,380,940,559" title="Right 2" href="5">
<area shape="rect" coords="830,560,940,839" title="Right 3" href="6">
<area shape="rect" coords="200,830,379,940" title="Bottom 1" href="7">
<area shape="rect" coords="380,830,559,940" title="Bottom 2" href="8">
<area shape="rect" coords="560,830,739,940" title="Bottom 3" href="9">
<area shape="rect" coords="0,200,109,379" title="Left 1" href="10">
<area shape="rect" coords="0,380,109,559" title="Left 2" href="11">
<area shape="rect" coords="0,560,109,839" title="Left 3" href="12">
</map -->
<h1><i>The King's Armory</i> Map Generator</h1>
<img src="img/key.png" height="300" width="300" />
<form id="map-form" onsubmit="return generateURL();">
<p>Use the key below to input the desired tile for each space on the board. Custom border tiles will be working soon.</p>
<!-- T1: <input type="text" id="t1"><br />
T2: <input type="text" id="t2"><br />
T3: <input type="text" id="t3"><br />
R1: <input type="text" id="r1"><br />
R2: <input type="text" id="r2"><br />
R3: <input type="text" id="r3"><br />
B1: <input type="text" id="b1"><br />
B2: <input type="text" id="b2"><br />
B3: <input type="text" id="b3"><br />
L1: <input type="text" id="l1"><br />
L2: <input type="text" id="l2"><br />
L3: <input type="text" id="l3"><br / -->
1: <input type="text" id="m1"><br />
2: <input type="text" id="m2"><br />
3: <input type="text" id="m3"><br />
4: <input type="text" id="m4"><br />
5: <input type="text" id="m5"><br />
6: <input type="text" id="m6"><br />
7: <input type="text" id="m7"><br />
8: <input type="text" id="m8"><br />
9: <input type="text" id="m9"><br />
10: <input type="text" id="m10"><br />
11: <input type="text" id="m11"><br />
12: <input type="text" id="m12"><br />
13: <input type="text" id="m13"><br />
14: <input type="text" id="m14"><br />
15: <input type="text" id="m15"><br />
16: <input type="text" id="m16"><br />
<input type="submit" value="Generate URL" />
</form>
<div style="background-color: tan;">Your map URL: <a id="genurl" href="">[none]</a></div>
<h1>Key</h1>
<div style="width: 75%; overflow: scroll;">
<table>
<tr>
<?php

$tile_array = array("a1", "a2", "a3", "a4", "b1", "b2", "b3", "b4", "c1", "c2", "c3", "c4", "d1", "d2", "d3", "d4", "e1", "e2", "e3", "e4", "f1", "f2", "f3", "f4");

foreach($tile_array as $tile)
{
	echo("<td><h3 style='text-align: center;'>" . $tile . "</h3><img src='img/" . $tile . ".png' /></td>");
}

?>
</tr>
</table>
</div>
<small>&copy; 2013 Dan Parson. All <i>The King's Armory</i> images are Copyright <a href="http://www.gatekeepergaming.com/">Gate Keeper Games</a>. Please visit the <a href="http://www.kickstarter.com/projects/johnwrot/the-kings-armory-the-tower-defense-board-game-0">Kickstarter for TKA.</a></small>
</body>
</html>