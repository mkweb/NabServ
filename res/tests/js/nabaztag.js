function Ear(nabaztag, site) {

	this.nabaztag 	= nabaztag;
	this.site	= site;
	this.width 	= 12;
	this.points	= null;
	this.angle	= 0;

	this.init	= function() {

		this.points = {
			line1 : {
				start : {
					x : null,
					y : null,
				},
				end : {
					x : null,
					y : null,
				}
			},
			curve : {
				p1 : {
					x : null,
					y : null
				},
				p2 : {
					x : null,
					y : null
				},
				end : {
					x : null,
					y : null
				}
			},
			line2 : {
				end : {
					x : null,
					y : null,
				}
			},
		}
	}

	this.setAngle 	= function(a) {

		this.angle = a;
	}

	this.create	= function() {

		this.init();

		var c = this.nabaztag.c;

		if(this.site == 'left') {

			var left = this.nabaztag.x + (this.nabaztag.width / 5) + 4;
		} else {

			var left = (this.nabaztag.x + this.nabaztag.width) - (this.nabaztag.width / 5) - 4;
		}

		c.save();
		c.translate(left, this.nabaztag.y + (this.nabaztag.height / 2.3));

		if(this.site == 'right') {

			c.rotate(this.angle * Math.PI / 180);
		}

		if(this.site == 'left') {

			c.rotate(-this.angle * Math.PI / 180);
		}

		this.points.line1.start.x = - (this.width / 2);
		this.points.line1.start.y = 4;
		
		this.points.line1.end.x = this.points.line1.start.x;
		this.points.line1.end.y = this.points.line1.start.y - (this.nabaztag.height / 3);
		
		// curve
		this.points.curve.p1.x = this.points.line1.end.x;
		this.points.curve.p1.y = this.points.line1.end.y - this.width;
		
		this.points.curve.p2.x = this.points.line1.end.x + this.width;
		this.points.curve.p2.y = this.points.line1.end.y - this.width;
		
		this.points.curve.end.x = this.points.line1.end.x + this.width;
		this.points.curve.end.y = this.points.line1.end.y;
		
		// line2
		this.points.line2.end.x = this.points.line1.start.x + this.width;
		this.points.line2.end.y = this.points.line1.start.y;

		c.beginPath();
		c.moveTo(this.points.line1.start.x, this.points.line1.start.y);
		c.lineTo(this.points.line1.end.x, this.points.line1.end.y);
		c.bezierCurveTo(
			this.points.curve.p1.x, this.points.curve.p1.y,
			this.points.curve.p2.x, this.points.curve.p2.y,
			this.points.curve.end.x, this.points.curve.end.y
		);
		c.lineTo(this.points.line2.end.x, this.points.line2.end.y);
		c.restore();
		c.closePath();

		c.strokeStyle = '#666';
		c.fillStyle = this.nabaztag.gradient;
		c.fill();
		c.stroke();
	}
}

function Nabaztag(id) {

	this.id =	 id;
	this.canvas 	= null;
	this.c 		= null;
	this.height	= 110;
	this.width	= 76;
	this.x		= 40;
	this.y		= 10;
	this.gradient 	= null;
	this.blink	= true;
	this.lastBlink  = 0;

	this.angle	= 0;
	this.gotoSleep  = false;
	this.wakeUp  	= false;

	this.leds = {
		led1 : '',
		led2 : '',
		led3 : '',
		led4 : '',
	}

	this.init = function() {

		this.canvas = document.getElementById(this.id);
		this.canvas.style.border = '1px solid #000';
		this.c = this.canvas.getContext('2d');

		this.gradient = this.c.createLinearGradient(this.x - 220, this.y, (this.x + this.width), this.y + 20);
		this.gradient.addColorStop(0, '#666');
		this.gradient.addColorStop(1, '#FFF');

		this.draw();

		var t = this;
//		window.setInterval(function() { t.draw() }, 100);
	}

	this.led = function(num, col) {

		switch(num) {

			case 1:
				console.log("setting led1 to " + col);

				this.leds.led2 = '';
				this.leds.led3 = '';
				this.leds.led4 = '';

				this.leds.led1 = (this.leds.led1 == '' ? col : '');
				break;
			
			case 2:
				console.log("setting led2 to " + col);

				this.leds.led1 = '';
				this.leds.led3 = '';
				this.leds.led4 = '';

				this.leds.led2 = (this.leds.led2 == '' ? col : '');
				break;
			
			case 3:
				console.log("setting led3 to " + col);

				this.leds.led1 = '';
				this.leds.led2 = '';
				this.leds.led4 = '';

				this.leds.led3 = (this.leds.led3 == '' ? col : '');
				break;
			
			case 4:
				console.log("setting led4 to " + col);

				this.leds.led1 = '';
				this.leds.led2 = '';
				this.leds.led3 = '';

				this.leds.led4 = (this.leds.led4 == '' ? col : '');
				break;
		}

		this.draw();
	}

	this.sleep = function() {

		this.gotoSleep = true;
	}

	this.wake = function() {

		this.wakeUp = true;
	}

	this.draw = function() {

		this.c.clearRect(0, 0, this.canvas.width, this.canvas.height);

		this.drawEars();
		this.drawBody();
		this.drawLeds();
		this.drawFace();
	}

	this.drawEars = function() {

		if(this.gotoSleep) {
			this.wakeUp = false;

			if(this.angle < 90) {

				this.angle += 4;
			} else {

				this.gotoSleep = false;	
			}
		}

		if(this.wakeUp) {
			this.gotoSleep = false;

			if(this.angle > 0) {

				this.angle -= 4;
			} else {

				this.wakeUp = false;
			}
		}

		var ear = new Ear(this, 'left');
		ear.setAngle(this.angle);
		ear.create();
		
		var ear = new Ear(this, 'right');
		ear.setAngle(this.angle);
		ear.create();
	}

	this.drawLeds = function() {

		var d = new Date();
		var seconds = d.getSeconds();

		/*
		if(this.lastBlink == seconds) {

			return false;
		}

		this.lastBlink = seconds;

		if(this.blink == false) {

			this.blink = true;
			return false;
		}

		this.blink = false;
*/			

		var leds = {
			led1 : {
				x : (this.x + (this.width / 2)) + (this.width / 4),
				y : (this.y + (this.height / 2)) + (this.height / 3) + 4
			},
			led2 : {
				x : (this.x + (this.width / 2)),
				y : (this.y + (this.height / 2)) + (this.height / 3) + 6
			},
			led3 : {
				x : (this.x + (this.width / 2) - (this.width / 4)),
				y : (this.y + (this.height / 2)) + (this.height / 3) + 4
			},
			led4 : {
				x : (this.x + (this.width / 2)),
				y : (this.y + (this.height / 2)) + 9
			}
		}

		this.c.globalAlpha = 0.5;


		// led1
		if(this.leds.led1 != '') {
		
			this.c.fillStyle = this.leds.led1;
			this.c.moveTo(leds.led1.x, leds.led1.y);
			this.c.beginPath();
			this.c.arc(leds.led1.x, leds.led1.y, 7, 0, 2*Math.PI, false);
			this.c.closePath();
			this.c.fill();
		}

		// led2
		if(this.leds.led2 != '') {
		
			this.c.fillStyle = this.leds.led2;
			this.c.moveTo(leds.led2.x, leds.led2.y);
			this.c.beginPath();
			this.c.arc(leds.led2.x, leds.led2.y, 7, 0, 2*Math.PI, false);
			this.c.closePath();
			this.c.fill();
		}

		// led3
		if(this.leds.led3 != '') {
		
			this.c.fillStyle = this.leds.led3;
			this.c.moveTo(leds.led3.x, leds.led3.y);
			this.c.beginPath();
			this.c.arc(leds.led3.x, leds.led3.y, 7, 0, 2*Math.PI, false);
			this.c.closePath();
			this.c.fill();
		}

		// led4
		if(this.leds.led4 != '') {
		
			this.c.fillStyle = this.leds.led4;
			this.c.moveTo(leds.led4.x, leds.led4.y);
			this.c.beginPath();
			this.c.arc(leds.led4.x, leds.led4.y, 7, 0, 2*Math.PI, false);
			this.c.closePath();
			this.c.fill();
		}

		this.c.globalAlpha = 1;
	}

	this.drawBody = function() {

		var start = {
			x : this.x,
			y : (this.y + this.height)
		}

		// body
		this.c.beginPath();
		this.c.moveTo(start.x, start.y);
		this.c.lineTo((start.x + 10), (this.y + 60));
		this.c.bezierCurveTo(this.x + 24, this.y + 20, (this.x + this.width) - 24, this.y + 20, ((start.x + this.width) - 10), (this.y + 60));
		this.c.lineTo((start.x + this.width), start.y);
		this.c.quadraticCurveTo((start.x + (this.width / 2)), (this.y + this.height + 10), start.x, start.y);
	
		this.c.fillStyle = this.gradient;	
		this.c.strokeStyle = '#666';
		this.c.closePath();

		this.c.fill();
		this.c.stroke();
	}

	this.drawFace = function() {

		var eyes = {
			left : {
				x : (this.x + (this.width / 2)) - 6,
				y : (this.y + (this.height / 2))
			},
			right : {
				x : (this.x + (this.width / 2)) + 6,
				y : (this.y + (this.height / 2))
			}
		}

		var nose = {
			x : (this.x + (this.width / 2)),
			y : (this.y + (this.height / 2)) + 8
		}

		// eyes
		this.c.fillStyle = '#666';

		this.c.moveTo(eyes.left.x, eyes.left.y);
		this.c.beginPath();
		this.c.arc(eyes.left.x, eyes.left.y, 2, 0, 2*Math.PI, false);
		this.c.arc(eyes.left.x, eyes.left.y + 1, 2, 0, 2*Math.PI, false);
		this.c.closePath();
		this.c.fill();
		
		this.c.moveTo(eyes.right.x, eyes.right.y);		
		this.c.beginPath();
		this.c.arc(eyes.right.x, eyes.right.y, 2, 0, 2*Math.PI, false);
		this.c.arc(eyes.right.x, eyes.right.y + 1, 2, 0, 2*Math.PI, false);
		this.c.closePath();
		this.c.fill();

		// nose
		this.c.moveTo(eyes.right.x, eyes.right.y);		
		this.c.beginPath();
		this.c.arc(nose.x, nose.y, 2, 0, 2*Math.PI, false);
		this.c.arc(nose.x, nose.y + 1, 2, 0, 2*Math.PI, false);
		this.c.arc(nose.x, nose.y + 2, 2, 0, 2*Math.PI, false);
		this.c.arc(nose.x, nose.y + 3, 2, 0, 2*Math.PI, false);
		this.c.arc(nose.x - 2, nose.y, 2, 0, 2*Math.PI, false);
		this.c.arc(nose.x - 1, nose.y, 2, 0, 2*Math.PI, false);
		this.c.arc(nose.x + 1, nose.y, 2, 0, 2*Math.PI, false);
		this.c.arc(nose.x + 2, nose.y, 2, 0, 2*Math.PI, false);
		this.c.closePath();
		this.c.fill();
	}
}

var nabaztag;

window.onload = function() {

	nabaztag = new Nabaztag('nabaztag');
	nabaztag.init();
}

function sleep() {

	nabaztag.sleep();
}

function wake() {

	nabaztag.wake();
}
