/**
*
* This file is part of the 'Online Choose your own Adventure' MultiOil (working title).
*
* University of the Arts Zurich (Switzerland)
* Game Design 2016
* Task: Writing and producing an Online CYOA, including own illustrations.
*
* @copyright (c) University of the Arts Zurich
*
* Author(s):
* Arno Justus
* Julian Schoenbaechler
* Marcel Arioli
* Michael von Ah
*
*/

// Globals
var ambientSound = {
	channel1: new buzz.sound('sound/helicopter.mp3', {
				loop: true,
				volume: 0
			}),
	channel2: new buzz.sound('sound/helicopter.mp3', {
				loop: true,
				volume: 0
			}),
	playing: 0,
	last: "none"
};
var overlaySounds = [];


// Stop all overlay sounds
function stopOverlaySounds() {
	
	// Fade each overlay sound out
	for(var i = 0;i < overlaySounds.length;i++) {
		
		overlaySounds[i].unloop().fadeOut(1000, function() {
		
			overlaySounds[i].stop();
		
		});
		
	}
	
}

// Set and start a new overlay sound
function setOverlaySound(file, id) {
	
	// Check types
	if((typeof file === 'string') && (typeof id === 'number')) {
		
		// New sound
		overlaySounds[id] = new buzz.sound('sound/' + file, {
								loop: true,
								volume: 0
							});
		
		// Fade in
		overlaySounds[id].play().fadeIn(1000);
		
	}
	
}

// Set and start ambient sound
function setAmbientSound(ambient) {
	
	// Check types
	if(typeof ambient === 'string') {
		
		// Check if ambient sound should be 'badday'
		if((ambient == 'radio') && (datasets > 5)) {
			
			ambient = 'badday';
			
		}
		
		// Ambient is not currently playing?
		if (ambientSound.last != ambient) {
			
			// Which channel is active?
			if(!ambientSound.playing) {
				// 0
				
				// Choose: no sound?
				if(ambient != 'none') {
					
					ambientSound.channel2 = new buzz.sound('sound/' + ambient + '.mp3', {
						loop: true,
						volume: 0
					});
					ambientSound.channel2.play().fadeIn(2000);
				}
				
				// Fade current ambient out
				ambientSound.channel1.unloop().fadeOut(2000, function() {
					
					ambientSound.channel1.stop();
					
				});
				
			} else {
				// 1
				
				// Choose: no sound?
				if(ambient != 'none') {
					
					ambientSound.channel1 = new buzz.sound('sound/' + ambient + '.mp3', {
						loop: true,
						volume: 0
					});
					ambientSound.channel1.play().fadeIn(2000);
				}
				
				// Fade current ambient out
				ambientSound.channel2.unloop().fadeOut(2000, function() {
					
					ambientSound.channel2.stop();
					
				});
			}
			
			ambientSound.last = ambient;
			ambientSound.playing ^= 1;
		
		}
		
	}
	
}
