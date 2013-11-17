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
	$tile_names = array("_a1", "_a2", "_a3", "_a4", "_b1", "_b2", "_b3", "_b4", "_c1", "_c2", "_c3", "_c4", "_d1", "_d2", "_d3", "_d4", "_e1", "_e2", "_e3", "_e4", "_f1", "_f2", "_f3", "_f4", "_g1", "_g2", "_g3", "_g4", "_h1", "_h2", "_h3", "_h4", "_i1", "_i2", "_i3", "_i4", "a1", "a2", "a3", "a4", "b1", "b2", "b3", "b4", "c1", "c2", "c3", "c4", "d1", "d2", "d3", "d4", "e1", "e2", "e3", "e4", "f1", "f2", "f3", "f4", "g1", "g2", "g3", "g4", "h1", "h2", "h3", "h4", "i1", "i2", "i3", "i4", "j1", "j2", "j3", "j4");
	
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
<link rel="stylesheet" type="text/css" href="css/style.css"></style>
<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.3.custom.js"></script>
<script type="text/javascript" src="js/jquery.ui.touch-punch.js"></script>

<script>
var mtiles = new Array("a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1");

  $(function() {
    $( ".tile-list-item" ).draggable({ opacity: 1.0, helper: "clone", scroll: true, snap: ".mcell", snapMode: "inner", cursor: "move", cursorAt: { top: 90, left: 90 } });
	
	$( ".mcell" ).droppable({
      activeClass: "ui-state-default",
      hoverClass: "ui-state-hover",
      accept: ".tile-list-item",
      drop: function( event, ui ) {
	    var tile_coords = new Array();
		var mtiles_index = 0;
        $( this ).css('background-image', 'url(img/' + ui.draggable.attr('id').substring(1) + '.png)');
		tile_coords = $( this ).attr('id').substring(1).split("-");
		mtiles_index = ((4 * (parseInt(tile_coords[1]) - 1)) + parseInt(tile_coords[0])) - 1;
		mtiles[mtiles_index] = ui.draggable.attr('id').substring(1);
		$( "#mapurl" ).attr('href', 'http://www.danielparson.com/tka-map/?m=44,g0fe0fih0e0f,' + mtiles.join(''));
		$( "#mapurl" ).text('http://www.danielparson.com/tka-map/?m=44,g0fe0fih0e0f,' + mtiles.join(''));
      }
    })
  });
  
  </script>
</head>
<body>
<h1><i>The King's Armory</i> Map Creator <small>(beta)</small></h1>
<p>Drag tiles from the right onto the map grid to create your map. A link to the completed image is below the map grid.</p>
<div id="tile-list">
<?php

$tile_array = array("a1", "a2", "a3", "a4", "b1", "b2", "b3", "b4", "c1", "c2", "c3", "c4", "d1", "d2", "d3", "d4", "e1", "e2", "e3", "e4", "f1", "f2", "f3", "f4", "g1", "g2", "g3", "g4", "h1", "h2", "h3", "h4", "i1", "i2", "i3", "i4", "j1", "j2", "j3", "j4");

foreach($tile_array as $tile)
{
	echo("<div id='t" . $tile . "' class='tile-list-item'><img src='img/" . $tile . ".png' /></div>");
}

?>
</div>
<div id="canvas">
	<div class="row">
		<div id="ul" class="corner"></div>
		<div id="top">
			<table class="grid-table htable">
				<tr>
					<td id="b1-1" class="hcell-1" colspan="2"></td>
					<!-- td id="b1-2" class="hcell"></td -->
					<td id="b1-3" class="hcell"></td>
				</tr>
			</table>
		</div>
		<div id="ur" class="corner"></div>
	</div>
	<div class="row">
		<div id="left">
			<table class="grid-table vtable">
				<tr>
					<td id="b4-1" class="vcell"></td>
				</tr>
				<tr>
					<td id="b4-2" class="vcell"></td>
				</tr>
				<tr>
					<td id="b4-3" class="vcell"></td>
				</tr>
			</table>
		</div>
		<div id="middle">
			<table class="grid-table mtable">
				<tr>
					<td id="m1-1" class="mcell"></td>
					<td id="m2-1" class="mcell"></td>
					<td id="m3-1" class="mcell"></td>
					<td id="m4-1" class="mcell"></td>
				</tr>
				<tr>
					<td id="m1-2" class="mcell"></td>
					<td id="m2-2" class="mcell"></td>
					<td id="m3-2" class="mcell"></td>
					<td id="m4-2" class="mcell"></td>
				</tr>
				<tr>
					<td id="m1-3" class="mcell"></td>
					<td id="m2-3" class="mcell"></td>
					<td id="m3-3" class="mcell"></td>
					<td id="m4-3" class="mcell"></td>
				</tr>
				<tr>
					<td id="m1-4" class="mcell"></td>
					<td id="m2-4" class="mcell"></td>
					<td id="m3-4" class="mcell"></td>
					<td id="m4-4" class="mcell"></td>
				</tr>
			</table>
		</div>
		<div id="right">
			<table class="grid-table vtable">
				<tr>
					<td id="b2-1" class="vcell"></td>
				</tr>
				<tr>
					<td id="b2-2" class="vcell"></td>
				</tr>
				<tr>
					<td id="b2-3" class="vcell"></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="row">
		<div id="ll" class="corner"></div>
		<div id="bottom">
		<table class="grid-table htable">
				<tr>
					<td id="b3-1" class="hcell-2"></td>
					<td id="b3-2" class="hcell-3" colspan="2"></td>
					<!-- td id="b3-3" class="hcell"></td -->
				</tr>
			</table>
		</div>
		<div id="lr" class="corner"></div>
	</div>
</div>
<br id="clearcanvas" />
<p>Your map URL is <a id="mapurl" href="http://www.danielparson.com/tka-map/?m=44,g0fe0fih0e0f,a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1">http://www.danielparson.com/tka-map/?m=44,g0fe0fih0e0f,a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1</a></p>
<small>Last Update: Nov. 14, 2013 12:01 PM<br />-
<br />
To-do List
<ul>
<li>Fix incorrect filetype when saving image</li>
<li>Allow border tile editing</li>
<li>Allow custom-sized maps (likely up to 6x6 to keep the server from blowing up)</li>
<li>Make the interface prettier</li>
<li>Better mobile compatibility (I personally have had mixed results)</li>
</ul>
<p>Issues with this map creator may be emailed to <a href="dan@danielparson.com">dan@danielparson.com</a>. Any contact not related to this map creator should be made to <a href="http://www.gatekeepergaming.com/contact-us">Gate Keeper Games</a>.</p>
&copy; 2013 Dan Parson. All <i>The King's Armory</i> images are &copy; Gate Keeper Games. Please visit the <a href="http://www.kickstarter.com/projects/johnwrot/the-kings-armory-the-tower-defense-board-game-0">Kickstarter for TKA.</a></small>
</body>
</html>