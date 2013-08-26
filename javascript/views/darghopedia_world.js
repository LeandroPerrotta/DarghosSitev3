var map_path = "files/map/";
var offset_x = 570 / 2;
var offset_y = 510 / 2; 
var base_position = {x: (1966 - offset_x), y: (1846 - offset_y)};
var current_position = base_position;
var current_floor = 0;
var dragging = false;
var drag_startpos = {}

var DIR_NORTH = 0
var DIR_EAST = 1
var DIR_SOUTH = 2
var DIR_WEST = 3

function setFocus(x, y){
	
	if(current_position.x != x)
		current_position.x = x;
	
	if(current_position.y != y)
		current_position.y = y;
	
	$("#map-viewer").css("background-position", "-" + current_position.x + "px -" + current_position.y + "px");
	onChangeCords();
}

function setFloor(floor){
	current_floor = floor;
	$("#map-viewer").css("background", "url(" + map_path + current_floor + ".png)");
	
	setFocus(current_position.x, current_position.y);	
	
	onChangeCords();
}

function onChangeCords(){
	$("#control-cords").text("X: " + current_position.x + ", Y: " + current_position.y + ", Z: " + current_floor);
	$("#control-url").val("http://darghos.com.br/?ref=darghopedia.world&posx=" + current_position.x + "&posy=" + current_position.y + "&posz=" + current_floor);
}

function movePov(direction){
	
	move_x = 0
	move_y = 0
	
	if(direction == DIR_NORTH)
		move_y = -10;
	else if(direction == DIR_EAST)
		move_x = +10;
	else if(direction == DIR_SOUTH)
		move_y = +10;	
	else if(direction == DIR_WEST)
		move_x = -10;		
	
	setFocus(current_position.x + move_x, current_position.y + move_y);
}

$(document).ready(function() {	
	
	if($("#base_x").val() != "" && $("#base_y").val() != "" && $("#base_z").val() != ""){
		current_position = {x: (Number($("#base_x").val())), y: (Number($("#base_y").val()))};
		setFloor(Number($("#base_z").val()));
	}
	else
		setFloor(7);	
	
	$("#map-viewer").mousedown(function(e){
		dragging = true;
		
		drag_startpos.x = e.pageX;
		drag_startpos.y = e.pageY;
	});
	$("#map-viewer").mousemove(function(e){
		if(dragging){
			
			if(e.pageX > drag_startpos.x + 10)
				movePov(DIR_WEST);
			else if(e.pageX < drag_startpos.x - 10)	
				movePov(DIR_EAST);
			
			if(e.pageY > drag_startpos.y + 10)
				movePov(DIR_NORTH);
			else if(e.pageY < drag_startpos.y - 10)	
				movePov(DIR_SOUTH);			
		}
	});
	$("#map-viewer").mouseup(function(e){
		dragging = false;
	});
	$("#map-viewer").mouseleave(function(e){
		dragging = false;
	});	
	
	$(".control-button").click(function(){
		
		dir = null;
		
		if($(this).attr("id") == "control-east")
			dir = DIR_EAST;
		else if($(this).attr("id") == "control-south")
			dir = DIR_SOUTH;
		else if($(this).attr("id") == "control-west")
			dir = DIR_WEST;
		else if($(this).attr("id") == "control-north")
			dir = DIR_NORTH;
		
		if(dir != null)
			movePov(dir);
		
		floor = null;
		
		if($(this).attr("id") == "control-floorup")
			floor = current_floor - 1;
		else if($(this).attr("id") == "control-floordown")
			floor = current_floor + 1;
		
		if(floor != null)
			setFloor(floor)
	});
});