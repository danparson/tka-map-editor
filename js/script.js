var dims = new Array(4, 4);
var btiles = new Array("g", "0", "f", "e", "0", "f", "i", "h", "0", "e", "0", "f");
var mtiles = new Array("a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1", "a1");

  $(function() {
    /*$( ".tile-list-item" ).draggable({ opacity: 1.0, helper: "clone", scroll: true, snap: ".mcell", snapMode: "inner", cursor: "move", cursorAt: { top: 50, left: 50 } });*/
	$( ".mtile" ).draggable({ opacity: 1.0, helper: "clone", scroll: true, snap: ".mcell", snapMode: "inner", cursor: "move", cursorAt: { top: 50, left: 50 } });
	$( ".htile" ).draggable({ opacity: 1.0, helper: "clone", scroll: true, snap: ".hcell", snapMode: "inner", cursor: "move", cursorAt: { top: 50, left: 50 } });
	$( ".vtile" ).draggable({ opacity: 1.0, helper: "clone", scroll: true, snap: ".vcell", snapMode: "inner", cursor: "move", cursorAt: { top: 50, left: 50 } });

	
	$( ".mcell" ).droppable({
      activeClass: "ui-state-default",
      hoverClass: "ui-state-hover",
      accept: ".mtile",
      drop: function( event, ui ) {
	    var mtile_coords = new Array();
		var mtiles_index = 0;
		mtile_coords = $( this ).attr('id').substring(1).split("-");
		mtiles_index = ((4 * (parseInt(mtile_coords[1]) - 1)) + parseInt(mtile_coords[0])) - 1;
		mtiles[mtiles_index] = ui.draggable.attr('id').substring(2);
		$( this ).css('background-image', 'url(img/' + ui.draggable.attr('id').substring(2) + '.png)');
		$( "#mapurl" ).attr('href', location.href + '?m=' + dims.join('') + ',' + btiles.join('') + ',' + mtiles.join(''));
		$( "#mapurl" ).text(location.href + '?m=' + dims.join('') + ',' + btiles.join('') + ',' + mtiles.join(''));
      }
    });
	
	$( ".hcell.cell1" ).droppable({
      activeClass: "ui-state-default",
      hoverClass: "ui-state-hover",
      accept: ".htile.tile1",
      drop: function( event, ui ) {
	    var btile_coords = new Array();
		var btiles_index = 0;
		btile_coords = $( this ).attr('id').substring(1).split("-");
		btiles_index = ((3 * (parseInt(btile_coords[0]) - 1)) + (parseInt(btile_coords[1]) - 1));
		btiles[btiles_index] = ui.draggable.attr('id').substring(3,4);
		$( this ).next().css("display", "");
		$( this ).attr('colspan', '1');
		$( this ).css('background-image', 'url(img/' + ui.draggable.attr('id').substring(2) + '.png)');
		$( "#mapurl" ).attr('href', location.href + '?m=' + dims.join('') + ',' + btiles.join('') + ',' + mtiles.join(''));
		$( "#mapurl" ).text(location.href + '?m=' + dims.join('') + ',' + btiles.join('') + ',' + mtiles.join(''));
      }
    });

	$( ".hcell.cell2" ).droppable({
      activeClass: "ui-state-default",
      hoverClass: "ui-state-hover",
      accept: ".htile",
      drop: function( event, ui ) {
	    var btile_coords = new Array();
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
		$( "#mapurl" ).attr('href', location.href + '?m=' + dims.join('') + ',' + btiles.join('') + ',' + mtiles.join(''));
		$( "#mapurl" ).text(location.href + '?m=' + dims.join('') + ',' + btiles.join('') + ',' + mtiles.join(''));
      }
    });
	
	$( ".vcell.cell1" ).droppable({
      activeClass: "ui-state-default",
      hoverClass: "ui-state-hover",
      accept: ".vtile.tile1",
      drop: function( event, ui ) {
	    var btile_coords = new Array();
		var btiles_index = 0;
		btile_coords = $( this ).attr('id').substring(1).split("-");
		btiles_index = ((3 * (parseInt(btile_coords[0]) - 1)) + (parseInt(btile_coords[1]) - 1));
		btiles[btiles_index] = ui.draggable.attr('id').substring(3,4);
		$( this ).css('background-image', 'url(img/' + ui.draggable.attr('id').substring(2) + '.png)');
		$( "#mapurl" ).attr('href', location.href + '?m=' + dims.join('') + ',' + btiles.join('') + ',' + mtiles.join(''));
		$( "#mapurl" ).text(location.href + '?m=' + dims.join('') + ',' + btiles.join('') + ',' + mtiles.join(''));
      }
    });

	$( ".vcell.cell2" ).droppable({
      activeClass: "ui-state-default",
      hoverClass: "ui-state-hover",
      accept: ".vtile",
      drop: function( event, ui ) {
	    var btile_coords = new Array();
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
		$( "#mapurl" ).attr('href', location.href + '?m=' + dims.join('') + ',' + btiles.join('') + ',' + mtiles.join(''));
		$( "#mapurl" ).text(location.href + '?m=' + dims.join('') + ',' + btiles.join('') + ',' + mtiles.join(''));
      }
    });

	$( "#mapurl" ).attr('href', location.href + '?m=' + dims.join('') + ',' + btiles.join('') + ',' + mtiles.join(''));
	$( "#mapurl" ).text(location.href + '?m=' + dims.join('') + ',' + btiles.join('') + ',' + mtiles.join(''));
  });