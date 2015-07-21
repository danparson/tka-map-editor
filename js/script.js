var dims = new Array(4, 4);
var btiles = new Array("g", "0", "f", "e", "0", "f", "i", "h", "0", "e", "0", "f");
var mtiles = new Array("a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1");

var mtile_array = ["a1", "a2", "a3", "a4", "b1", "b2", "b3", "b4", "c1", "c2", "c3", "c4", "d1", "d2", "d3", "d4", "e1", "e2", "e3", "e4", "f1", "f2", "f3", "f4", "g1", "g2", "g3", "g4", "h1", "h2", "h3", "h4", "i1", "i2", "i3", "i4", "j1", "j2", "j3", "j4"];
var btile_array = ["_a1", "_a2", "_a3", "_a4", "_b1", "_b2", "_b3", "_b4", "_c1", "_c2", "_c3", "_c4", "_d1", "_d2", "_d3", "_d4", "_e2", "_e4", "_g2", "_g4", "_h2", "_h4", "_f2", "_f4", "_i2", "_i4", "_e1", "_f1", "_e3", "_f3", "_g1", "_i1", "_g3", "_i3", "_h1", "_h3"];
var tile_images = {};
var canvas;
var imagesLoaded = 0;

$(function() {

  function updateMapImage() {
    var border_tiles = [];
    var map_tiles = [];

    // Unflatten tile arrays
    border_tiles[0] = btiles.slice(0, dims[0] - 1);
    border_tiles[1] = btiles.slice(dims[0] - 1, (dims[0] - 1) + (dims[1] - 1));
    border_tiles[2] = btiles.slice((dims[0] - 1) + (dims[1] - 1), 2 * (dims[0] - 1) + (dims[1] - 1));
    border_tiles[3] = btiles.slice(2 * (dims[0] - 1) + (dims[1] - 1), 2 * (dims[0] - 1) + 2 * (dims[1] - 1));

    for (var i = 0; i < dims[1]; i++) {
      map_tiles[i] = [];
      for (var j = 0; j < dims[0]; j++) {
        map_tiles[i][j] = mtiles[4 * i + j];
      }
    }

    // Draw corner tiles
    ctx.drawImage(tile_images._a1, 0, 0);
    ctx.drawImage(tile_images._b1, 740, 0);
    ctx.drawImage(tile_images._c1, 0, 740);
    ctx.drawImage(tile_images._d1, 740, 740);

    // Draw border tiles
    for (i = 0; i < border_tiles[0].length; i++) {
      if (border_tiles[0][i] != 0) {
        ctx.drawImage(tile_images["_" + border_tiles[0][i] + "1"], (180 * i) + 200, 0);
      }
      if (border_tiles[2][i] != 0) {
        ctx.drawImage(tile_images["_" + border_tiles[2][i] + "3"], (180 * i) + 200, 180 * (dims[1] - 1) + 290);
      }
    }

    for (i = 0; i < border_tiles[1].length; i++) {
      if (border_tiles[1][i] != 0) {
        ctx.drawImage(tile_images["_" + border_tiles[1][i] + "4"], 180 * (dims[1] - 1) + 290, (180 * i) + 200);
      }
      if (border_tiles[3][i] != 0) {
        ctx.drawImage(tile_images["_" + border_tiles[3][i] + "2"], 0, (180 * i) + 200);
      }
    }

    for (i = 0; i < map_tiles.length; i++) {
      for (var j = 0; j < map_tiles[0].length; j++) {
        ctx.drawImage(tile_images[map_tiles[i][j]], (j * 180) + 110, (i * 180) + 110);
      }
    }

    // Clear download link URL to avoid browser lag
    if (navigator.userAgent.indexOf("MSIE ") > -1 || navigator.userAgent.indexOf("Trident/") > -1) {
      document.getElementById("map-image").src = canvas.toDataURL();
    } else {
      document.getElementById("dl").href = canvas.toDataURL();
    }
  }

  function downloadImage(link, filename) {
    link.href = canvas.toDataURL();
    link.download = filename;
  }

  var imageLoaded = function() {
    if (++imagesLoaded == 76) {
      if (navigator.userAgent.indexOf("MSIE ") > -1 || navigator.userAgent.indexOf("Trident/") > -1) {
        $("#dl-heading").after("<p>To download, right-click the image below, and click \"Save picture as...\" in the menu.</p><img id=\"map-image\" />");
      } else {
        $("#dl-heading").after("<p><a id=\"dl\" href=\"\">Download Map Image</a></p>");
        document.getElementById('dl').addEventListener('click', function() {
          downloadImage(this, 'TKA Map.png');
        }, false);
      }
      updateMapImage();
    }
  };
  $('img').each(function() {
    var tmpImg = new Image() ;
    tmpImg.onload = imageLoaded ;
    tmpImg.src = $(this).attr('src') ;
  }) ;

  canvas = document.createElement("canvas");
  canvas.width = 940;
  canvas.height = 940;
  var ctx = canvas.getContext("2d");

  for (var i = 0; i < mtile_array.length; i++) {
    tile_images[mtile_array[i]] = document.getElementById('mti' + mtile_array[i]);
  }
  for (i = 0; i < btile_array.length; i++) {
    tile_images[btile_array[i]] = document.getElementById('mti' + btile_array[i]);
  }

  $( ".mtile" ).draggable({ opacity: 1.0, helper: "clone", scroll: true, snap: ".mcell", snapMode: "inner", cursor: "move", cursorAt: { top: 50, left: 50 } });
  $( ".htile" ).draggable({ opacity: 1.0, helper: "clone", scroll: true, snap: ".hcell", snapMode: "inner", cursor: "move", cursorAt: { top: 50, left: 50 } });
  $( ".vtile" ).draggable({ opacity: 1.0, helper: "clone", scroll: true, snap: ".vcell", snapMode: "inner", cursor: "move", cursorAt: { top: 50, left: 50 } });


  $( ".mcell" ).droppable({
    activeClass: "ui-state-default",
    hoverClass: "ui-state-hover",
    accept: ".mtile",
    drop: function( event, ui ) {
      var mtile_coords = [];
      var mtiles_index = 0;
      mtile_coords = $( this ).attr('id').substring(1).split("-");
      mtiles_index = ((4 * (parseInt(mtile_coords[1]) - 1)) + parseInt(mtile_coords[0])) - 1;
      mtiles[mtiles_index] = ui.draggable.attr('id').substring(2);
      $( this ).css('background-image', 'url(img/' + ui.draggable.attr('id').substring(2) + '.png)');
      updateMapImage();
    }
  });

  $( ".hcell.cell1" ).droppable({
    activeClass: "ui-state-default",
    hoverClass: "ui-state-hover",
    accept: ".htile.tile1",
    drop: function( event, ui ) {
      var btile_coords = [];
      var btiles_index = 0;
      btile_coords = $( this ).attr('id').substring(1).split("-");
      btiles_index = ((3 * (parseInt(btile_coords[0]) - 1)) + (parseInt(btile_coords[1]) - 1));
      btiles[btiles_index] = ui.draggable.attr('id').substring(3,4);
      $( this ).next().css("display", "");
      $( this ).attr('colspan', '1');
      $( this ).css('background-image', 'url(img/' + ui.draggable.attr('id').substring(2) + '.png)');
      updateMapImage();
    }
  });

  $( ".hcell.cell2" ).droppable({
    activeClass: "ui-state-default",
    hoverClass: "ui-state-hover",
    accept: ".htile",
    drop: function( event, ui ) {
      var btile_coords = [];
      var btiles_index = 0;
      btile_coords = $( this ).attr('id').substring(1).split("-");
      btiles_index = ((3 * (parseInt(btile_coords[0]) - 1)) + (parseInt(btile_coords[1]) - 1));
      btiles[btiles_index] = ui.draggable.attr('id').substring(3,4);
      if (ui.draggable.hasClass('tile2'))
      {
        btiles[btiles_index + 1] = 0;
        $( this ).next().css("display", "none");
        $( this ).attr('rowspan', '2');
      }
      else if (btiles[btiles_index + 1] == 0)
      {
        btiles[btiles_index + 1] = 'f';
        $( this ).next().css('background-image', 'url(img/' + ui.draggable.attr('id').substring(2,3) + 'f' + ui.draggable.attr('id').substring(4,5) + '.png)');
        $( this ).next().css("display", "");
        $( this ).attr('rowspan', '1');
      }
      $( this ).css('background-image', 'url(img/' + ui.draggable.attr('id').substring(2) + '.png)');
      updateMapImage();
    }
  });

  $( ".vcell.cell1" ).droppable({
    activeClass: "ui-state-default",
    hoverClass: "ui-state-hover",
    accept: ".vtile.tile1",
    drop: function( event, ui ) {
      var btile_coords = [];
      var btiles_index = 0;
      btile_coords = $( this ).attr('id').substring(1).split("-");
      btiles_index = ((3 * (parseInt(btile_coords[0]) - 1)) + (parseInt(btile_coords[1]) - 1));
      btiles[btiles_index] = ui.draggable.attr('id').substring(3,4);
      $( this ).css('background-image', 'url(img/' + ui.draggable.attr('id').substring(2) + '.png)');
      updateMapImage();
    }
  });

  $( ".vcell.cell2" ).droppable({
    activeClass: "ui-state-default",
    hoverClass: "ui-state-hover",
    accept: ".vtile",
    drop: function( event, ui ) {
      var btile_coords = [];
      var btiles_index = 0;
      btile_coords = $( this ).attr('id').substring(1).split("-");
      btiles_index = ((3 * (parseInt(btile_coords[0]) - 1)) + (parseInt(btile_coords[1]) - 1));
      btiles[btiles_index] = ui.draggable.attr('id').substring(3,4);
      if (ui.draggable.hasClass('tile2'))
      {
        btiles[btiles_index + 1] = 0;
        $( this ).parent().next().children(":first-child").css("display", "none");
        $( this ).attr('rowspan', '2');
      }
      else if (btiles[btiles_index + 1] == 0)
      {
        btiles[btiles_index + 1] = 'f';
        $( this ).parent().next().children(":first-child").css('background-image', 'url(img/' + ui.draggable.attr('id').substring(2,3) + 'f' + ui.draggable.attr('id').substring(4,5) + '.png)');
        $( this ).parent().next().children(":first-child").css("display", "");
        $( this ).attr('rowspan', '1');
      }
      $( this ).css('background-image', 'url(img/' + ui.draggable.attr('id').substring(2) + '.png)');
      updateMapImage();
    }
  });
});
