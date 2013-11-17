window.Mekanize = {
	config: {
		SPRITE: 
		{
			SIZE: 128
		},
		MAP: 
		{
			TILESIZE: 128,
			WIDTH: 25,
			HEIGHT: 25
		}
	},
};

window.MAP = {
	2: {
		3: true,
		4: true,
		5: true,
	},
	3: {
		1: true,
		2: true,
		3: true,
		4: true,
		5: true,
		6: true,
		7: true
	},
	4: {
		0: true,
		1: true,
		2: true,
		3: true,
		4: true,
		5: true,
		6: true,
		7: true,
		8: true
	},
	5: {
		2: true,
		3: true,
		4: true,
		5: true,
		6: true
	},
	6: {
		4: true
	}
};

$(document).ready(function() {
	Crafty.init();

	Crafty.sprite(Mekanize.config.SPRITE.SIZE, "assets/test.png", {
		grass: [0,0,1,1],
		stone: [1,0,1,1]
	});

	var iso = Crafty.isometric.size(Mekanize.config.MAP.TILESIZE);
	var mapFlags = window.MAP;
	var z = 0;
	for(var x = Mekanize.config.MAP.WIDTH -1; x >= 0; x--) {
		for(var y = 0; y < Mekanize.config.MAP.HEIGHT; y++) {
			if(!mapFlags[x] || !mapFlags[x][y])
				continue;
		
			var tile = Crafty.e("2D, Canvas, grass, Mouse")
			.attr({
				'tilex':x,
				'tiley':y,
				'z':x+1 * y+1
			})
			.bind("MouseUp", function(e) {
				//console.log('X:'+this.tilex+' / Y:'+this.tiley);
				//destroy on right click
				if(e.button === 2) this.destroy();
			})
			.bind("MouseOver", function() {
				this.sprite(0,1,1,1);
			})
			.bind("MouseOut", function() {
				this.sprite(0,0,1,1);
			});
			
			
			iso.place(x,y,0, tile);
		}
	}
	
	Crafty.addEvent(this, Crafty.stage.elem, "mousedown", function(e) {
		if(e.button > 1) return;
		var base = {x: e.clientX, y: e.clientY};

		function scroll(e) {
			var dx = base.x - e.clientX,
				dy = base.y - e.clientY;
				base = {x: e.clientX, y: e.clientY};
			Crafty.viewport.x -= dx;
			Crafty.viewport.y -= dy;
		};

		Crafty.addEvent(this, Crafty.stage.elem, "mousemove", scroll);
		Crafty.addEvent(this, Crafty.stage.elem, "mouseup", function() {
			Crafty.removeEvent(this, Crafty.stage.elem, "mousemove", scroll);
		});
	});
});