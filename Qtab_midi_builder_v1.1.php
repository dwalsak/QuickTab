<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create midi/ dir if missing
$midi_dir = 'midi';
if (!is_dir($midi_dir)) {
    mkdir($midi_dir, 0777, true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>QTab MIDI Builder — Guitar Sketches → Instant Music</title>
	<meta name="description" content="Write guitar licks fast. Export to GarageBand. No setup. Open source.">
	<link rel="icon" href="image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎸</text></svg>">
    <script type="text/javascript" src="core.js"></script>
    <script type="text/javascript" src="player.js"></script>
	<style>
		:root {
			--bg: #fafafa; --bg-alt: #fff; --text: #222; 
			--accent: #0066cc; --accent-alt: #e3f2fd; --accent-green: #32cd32;
			--border: #e0e0e0; 
			--shadow: 0 2px 4px rgba(0,0,0,0.15), 0 8px 25px rgba(0,0,0,0.25);
			--grey: #666;
		}
		* { box-sizing: border-box; margin: 0; padding: 0; }
		body {
			font: 400 1.1rem/1.6 -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
			color: var(--text); background: var(--bg);
			max-width: 720px; margin: 20px auto; padding: 40px 20px 20px 20px;
		}
		header {
			position: sticky; top: 0; height: 50px;
			backdrop-filter: blur(10px); z-index: 100;
			background-color: black; color: white;
			box-shadow: var(--shadow);
		}
		header nav {
			display: flex; justify-content: space-between; align-items: center;
			padding: 0 .5rem; height: 50px; line-height: 50px;
		}
		.logo { font: 700 1.5rem; color: white !important; text-decoration: none; line-height: 1; display: flex; align-items: center; }
		.info { font-weight: normal; font-family: sans-serif; font-size: .8rem; }
		.btn-back { 
		  background: var(--accent-green); color: white; text-decoration: none;
		  height: 30px; line-height: 30px; padding: 0 16px; border-radius: 6px; 
		  font-size: .9rem; font-weight: 600; box-shadow: 0 2px 8px rgba(50,205,50,0.3);
		}
		.btn-back:hover { background: #2eb82c; transform: translateY(-1px); }
		button, input[type="button"], input[type="submit"], .btn, select {
			-webkit-appearance: none; -moz-appearance: none; appearance: none;
			margin: 0; padding: 0; border: none; border-radius: 0;
			background: none; font: inherit; cursor: pointer; display: inline-block; text-align: center; text-decoration: none;
		}
		.btn-wide {
			width: 100%; padding: 1.2rem; border-radius: 0.75rem;
			font-weight: 600; font-size: 1.1rem; transition: all 0.2s; box-shadow: var(--shadow);
			margin: 0.75rem 0; display: block; text-align: center;
		}
		.btn-wide.blue { background: var(--accent); color: white; }
		.btn-wide.green { background: var(--accent-green); color: white; }
		.btn-wide.grey { background: var(--grey); color: white; }
		.btn-wide:hover { transform: translateY(-1px); }
		#phraseName, #filenameInput {
			width: 100%; border: 3px solid var(--accent); border-radius: 8px;
			padding: 1rem 1.25rem; font-size: 1.1rem; font-weight: 500; resize: vertical;
			background: white; box-shadow: 0 2px 8px rgba(0,102,204,0.1); margin: 1rem 0;
			min-height: 60px; font-family: monospace;
		}
		label { display: block; margin: 1.5rem 0 0.5rem 0; font-family: sans-serif; font-weight: bold; font-size: 1.2rem; }
		h1, h2 { font-weight: bold; font-family: sans-serif; font-size: 1.5rem; margin: 12px 0; }
		h2 { font-size: 1.2rem; }
		.panel {
			background: var(--bg-alt); border-radius: 12px; padding: 2rem;
			border: 1px solid var(--border); box-shadow: var(--shadow); margin: 2rem 0;
		}
		pre {
			background: #111; color: #0f0; padding: 20px; border-radius: 8px; max-height: 400px;
			overflow: auto; font-family: monospace; font-size: 14px; line-height: 1.4em;
		}
		midi-player, midi-visualizer { width: 100%; margin: 1rem 0; border-radius: 8px; }
		
		.download-btn {
			background: var(--accent-green) !important; 
			color: white !important; 
			padding: 25px 40px !important;
			font-size: 24px !important; 
			text-decoration: none !important; 
			border-radius: 12px !important;
			display: inline-block !important; 
			box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important; 
			margin: 20px 0;
		}
		
		.download-btn:hover { 
			background: #2eb82c !important; 
			transform: translateY(-2px) !important; 
		}
		
		.file-drop {
			border: 3px dashed var(--accent); border-radius: 12px; padding: 2rem; text-align: center;
			background: var(--accent-alt); margin: 1.5rem 0; max-width: 100%; overflow: hidden;
		}
		@media (max-width: 480px) {
			body { padding: 20px 10px 10px; }
			.panel, .file-drop { padding: 1.5rem; }
			.btn-wide { padding: 1.5rem; font-size: 1.2rem; }
		}
		
		.menu-toggle {
			background: none; border: none; color: white; 
			font-size: 1.5rem; cursor: pointer; padding: 0 .5rem;
			line-height: 50px; height: 50px;
		}
		.menu-toggle:hover { background: rgba(255,255,255,0.1); }
		.menu-dropdown {
			position: absolute; top: 50px; right: 0.5rem;
			background: black; border: 1px solid #444; border-radius: 8px;
			box-shadow: var(--shadow-header); min-width: 280px;
			opacity: 0; visibility: hidden; transform: translateY(-10px);
			transition: all 0.2s ease; z-index: 100;
		}
		.menu-dropdown.show {
			opacity: 1; visibility: visible; transform: translateY(0);
		}
		.menu-item {
			padding: 0rem 1.25rem; border-bottom: 1px solid #333;
			font-family: sans-serif; color: white;
		}
		.menu-item:last-child { border-bottom: none; }
		.menu-item strong { font-size: 0.95rem; display: block; margin-bottom: 0.25rem; }
		.menu-item a { 
			color: #b3d9ff; font-family: monospace; font-size: 0.85rem; 
			text-decoration: none; word-break: break-all;
		}
		.menu-item a:hover { color: white; text-decoration: underline; }

	</style>
</head>
<body>
	
	<header>
		<nav>		
			<h1><a href="#home" class="logo">QTab MIDI Builder <span class="info" style="color: white;padding:8px 0 0 8px;">v 1.1</span></a></h1>
			<button class="menu-toggle" aria-label="Tools menu">☰</button>
			<div class="menu-dropdown">
				<div class="menu-item">
					<a href="Qtab-Composer_v2.8.html"><strong>QTab Composer</strong></a>
				</div>
				<div class="menu-item">
					<a href="Qtab_midi_builder_v1.1.php"><strong>QTab MIDI Builder</strong></a>
				</div>
				<div class="menu-item">
					<a href="qtab_to_tab_v1.1.php"><strong>QTab-To-Tab</strong></a>
				</div>
				<div class="menu-item">
					<a href="QTab_Chord_Editor.html"><strong>QTab-Chord Editor</strong></a>
				</div>
			</div>
		</nav>
	</header>
	

	<?php
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['qtab']['tmp_name'])) {
		ob_start();
		$qtab_content = file_get_contents($_FILES['qtab']['tmp_name']);
		$base_filename = pathinfo($_FILES['qtab']['name'], PATHINFO_FILENAME);
		$filename = $midi_dir . '/' . $base_filename . '_' . time() . '.mid';
		
		// ... (all your existing PHP parsing/MIDI building code unchanged - deltaTime function at bottom)
		$DURATION_MAP = ['w'=>4,'w.'=>6,'h'=>2,'h.'=>3,'q'=>1,'q.'=>1.5,'e'=>0.5,'e.'=>0.75,'s'=>0.25,'s.'=>0.375,'t'=>0.125,'t.'=>0.1875];
		$open_notes = [40,45,50,55,59,64];
		$mf_events = []; $current_time = 0;

		// === PARSING === (unchanged)
		$lines = explode("\n", $qtab_content);
		$note_lines = [];
		foreach ($lines as $line) {
			$line = trim($line);
			if (empty($line) || strpos($line, '#') === 0) continue;
			if (strpos($line, ':') !== false && preg_match('/[123456]/', $line)) {
				$note_lines[] = $line;
			}
		}
		$song = !empty($note_lines) ? implode(',', $note_lines) : $qtab_content;
		$cleaned = preg_replace('/\s+/', '', $song);
		echo "🧹 CLEANED: $cleaned<br>";

		$chords = []; $chord_full_texts = [];
		$i = 0; $len = strlen($cleaned);
		while ($i < $len) {
			if ($cleaned[$i] == '[') {
				$start = $i; $depth = 1; $i++;
					while ($i < $len && $depth > 0) {
					if ($cleaned[$i] == '[') $depth++;
					elseif ($cleaned[$i] == ']') $depth--;
					$i++;
				}
				$content = trim(substr($cleaned, $start+1, $i-$start-2));
				$end = $i;
				if ($i < $len && strpos('qwehts.', $cleaned[$i]) !== false) $end++;
				$full_chord = substr($cleaned, $start, $end-$start);
				$chords[] = $content;
				$chord_full_texts[] = $full_chord;
				echo "  CHORD " . count($chords) . ": '$content'<br>";
			}
			$i++;
		}

		$working_text = $cleaned;
		foreach($chord_full_texts as $j => $full_chord) {
			$pos = strpos($working_text, $full_chord);
			if ($pos !== false) {
				$working_text = substr_replace($working_text, "CHORD_$j", $pos, strlen($full_chord));
			}
		}

		// === PROCESS PARTS ===
		$raw_parts = array_filter(array_map('trim', explode(',', $working_text)));
		foreach ($raw_parts as $part_idx => $part) {
			$pitches   = [];
			$dur_str   = 'q';
			$dur_beats = 1.0;

			// ----- CHORDS (CHORD_0, CHORD_1, etc.) -----
			if (strpos($part, 'CHORD_') === 0) {
				$has_dot = substr($part, -1) === '.';
				$chord_part = explode('_', $part)[1];
				if ($has_dot) {
					$chord_part = rtrim($chord_part, '.');
				}
				$chord_idx = (int)$chord_part;

				if (isset($chords[$chord_idx])) {
					$chord_content = $chords[$chord_idx];
					$full_chord    = $chord_full_texts[$chord_idx];

					// Duration from FULL chord text, e.g. "[1:3,2:5]e"
					$base = strlen($full_chord) > 0 ? substr($full_chord, -1, 1) : 'q';
					$dur_str = ($has_dot && strpos('qwehts', $base) !== false) ? $base . '.' : $base;
					$dur_beats = $DURATION_MAP[$dur_str] ?? 1.0;

					foreach (explode(',', $chord_content) as $item) {
						$item = trim($item);
						if (strpos($item, ':') !== false) {
							[$string, $fret] = explode(':', $item, 2);
							$fret = rtrim($fret, 'qwehts.');
							// x = muted string → contributes no pitch, but chord still has duration
							if ($fret !== 'x' && is_numeric($fret)) {
								$pitch = $open_notes[6 - (int)$string] + (int)$fret;
								$pitches[] = $pitch;
							}
						}
					}
				}

			// ----- SINGLE NOTES / RESTS -----
			} else {
				$len_part = strlen($part);

				// Pull off duration suffix (w,h,q,e,s,t plus optional dot)
				if ($len_part >= 2 && isset($DURATION_MAP[substr($part, -2)])) {
					$dur_str = substr($part, -2);
					$core    = substr($part, 0, -2);
				} elseif ($len_part >= 1 && isset($DURATION_MAP[substr($part, -1)])) {
					$dur_str = substr($part, -1);
					$core    = substr($part, 0, -1);
				} else {
					$core = $part;
				}
				$dur_beats = $DURATION_MAP[$dur_str] ?? 1.0;

				if (strpos($core, ':') !== false) {
					[$string, $fret] = explode(':', $core, 2);
					$fret = rtrim($fret, 'qwehts.');
					if ($fret !== 'x' && is_numeric($fret)) {
						$pitch = $open_notes[6 - (int)$string] + (int)$fret;
						$pitches[] = $pitch;
					}
					// if $fret === 'x' → REST: no pitches, but we still advance time below
				}
			}

			// ----- COMMON: ALWAYS ADVANCE TIME, EVEN FOR RESTS -----
			if ($dur_beats > 0) {
				$mf_events[] = [$current_time, $pitches, $dur_beats];

				if (empty($pitches)) {
					echo "  🎵 REST ($dur_str=$dur_beats)<br>";
				} else {
					$label = (count($pitches) > 1) ? 'CHORD' : 'NOTE';
					echo "  🎵 $label ($dur_str=$dur_beats): [" . implode(', ', $pitches) . "]<br>";
				}

				$current_time += $dur_beats;
			}
		}

		// === BUILD MIDI EVENTS ===
		echo "<br>🎵 Building MIDI (" . count($mf_events) . " events, " . round($current_time,1) . " beats)<br>";
		
		$ticks_per_beat = 480;
		$events = [];
		foreach ($mf_events as [$start, $pitches, $dur]) {
			foreach ($pitches as $p) {
				$events[] = ['time' => $start, 'type' => 'on', 'pitch' => $p];
				$events[] = ['time' => $start + $dur, 'type' => 'off', 'pitch' => $p];
			}
		}
		usort($events, fn($a, $b) => $a['time'] <=> $b['time']);

		// === MIDI HEADER ===
		$header = "MThd\x00\x00\x00\x06\x00\x00\x00\x01\x01\xE0";
		$track_data = "\x00\xff\x51\x03\x07\xA1\x20\x00\xc0\x18";

		$running_time = 0; $total_notes = 0;
		foreach ($events as $ev) {
			$delta_ticks = (int)(($ev['time'] - $running_time) * $ticks_per_beat);
			$track_data .= deltaTime($delta_ticks);
			$running_time = $ev['time'];

			if ($ev['type'] === 'on') {
				$track_data .= "\x90" . chr($ev['pitch']) . "\x64";
				$total_notes++;
			} else {
				$track_data .= "\x80" . chr($ev['pitch']) . "\x00";
			}
		}
		$track_data .= "\x00\xff\x2f\x00";
		$track_chunk = "MTrk" . pack("N", strlen($track_data)) . $track_data;
		$midi_data = $header . $track_chunk;

		echo "✅ MIDI: " . strlen($midi_data) . " bytes, $total_notes notes (" . round($current_time,1) . " beats @90BPM)<br>";
		file_put_contents($filename, $midi_data);

		$log_content = ob_get_clean();

		// === OUTPUT ===
		echo "<div class='panel'>";
		
		echo "<h3>🧭 Processing Log</h3>";
		echo "<div style='background:#111;color:#0f0;padding:20px;border-radius:8px;max-height:300px;overflow:auto;font-size:14px;line-height:1.4em;'>";
		echo nl2br($log_content);
		echo "</div>";
		echo "<br>";
		echo "<h3>🧭 Global Event Flow (Timeline)</h3>";
		echo "<pre style='background:#111;color:#0f0;padding:20px;border-radius:8px;max-height:400px;overflow:auto;font-size:14px;line-height:1.4em;'>";
		foreach ($events as $idx => $ev) {
			$delta_ticks = (int)(($ev['time'] - ($events[$idx-1]['time'] ?? 0)) * $ticks_per_beat);
			printf("%5.2f beat | %5d ticks | %-3s | Pitch %3d\n", $ev['time'], $delta_ticks, strtoupper($ev['type']), $ev['pitch']);
		}
		echo "</pre>";
		echo "<br>";
		
		echo "<h2>Play Now:</h2>";
		// iOS Audio Unlock Button (only shows on mobile)
		echo "<button id='enableAudio' class='btn-wide blue' style='display: none;'>🔊 Tap to Enable Audio (iPhone/iPad)</button>";
		echo "<midi-player src='$filename' sound-font visualizer='#mainVisualizer' id='mainPlayer'></midi-player>";
		echo "<midi-visualizer type='piano-roll' id='mainVisualizer'></midi-visualizer>";
		
		echo "<a href='$filename' download class='download-btn'>🎵 DOWNLOAD " . strtoupper($base_filename) . '.MID</a>';
		echo "</div>";
	}

	// === Helper ===
	function deltaTime($ticks) {
		$bytes = ''; $buffer = $ticks & 0x7F;
		while (($ticks >>= 7) > 0) {
			$buffer <<= 8; $buffer |= (($ticks & 0x7F) | 0x80);
		}
		while (true) {
			$bytes .= chr($buffer & 0xFF);
			if ($buffer & 0x80) $buffer >>= 8;
			else break;
		}
		return $bytes;
	}
	?>

	<div class="panel file-drop">
		<form method="POST" enctype="multipart/form-data" id="qtabForm">
			<label for="qtab">Load QTab file:</label>
			<input type="file" id="qtab" name="qtab" accept=".qtab,text/plain" required 
				   class="btn-wide blue" style="padding:1.2rem; font-size:1.1rem;">
			<input type="text" id="filenameInput" placeholder="Filename will appear here after selection (multi-line OK)" 
				   readonly style="display:none;">
			<button class="btn-wide green" type="submit">Convert qtab → MIDI</button>
			<button type="reset" class="btn-wide grey">Clear Current QTab Selection</button>
		</form>
	</div>

<hr style="margin: 24px 0">
<footer>
  <p><strong>QTab MIDI Builder v 1.1 </strong><br>
  Copyright © 2026 Dennis M. Walsak<br>
  <a href="QTab_Primer.pdf" target="_blank">QuickTab Primer</a> • <a href="LICENSE.md" target="_blank">MIT License</a></p>
  <p><a href="https://modularmedia.com/qtab" target="_blank">modularmedia.com/qtab</a></p>
</footer>
		
<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Status display for debugging
		const statusDiv = document.createElement('div');
		statusDiv.id = 'audioStatus';
		statusDiv.style.cssText = 'font-size:.9rem;color:#555;margin:1rem 0;padding:8px;background:#f0f0f0;border-radius:6px;';
		document.querySelector('.panel')?.appendChild(statusDiv) || document.body.appendChild(statusDiv);
	
		// 1. FIXED iOS Audio Unlock - shows on ALL Apple touch devices
	function initAudioUnlock() {
		const statusDiv = document.getElementById('audioStatus') || 
						 (document.querySelector('.panel:last-child') || document.body).appendChild(
							 Object.assign(document.createElement('div'), {
								 id: 'audioStatus',
								 style: 'font-size:.9rem;color:#555;margin:1rem 0;padding:8px;background:#f0f0f0;border-radius:6px;',
								 textContent: 'Initializing...'
							 })
						 );
	
		function checkAndShowUnlock() {
			const enableBtn = document.getElementById('enableAudio');
			const player = document.getElementById('mainPlayer');
			
			if (!enableBtn || !player) {
				statusDiv.textContent = '⏳ Waiting for player...';
				return setTimeout(checkAndShowUnlock, 500);
			}
	
			const ua = navigator.userAgent || '';
			const isTouchApple = /iPhone|iPad|iPod/i.test(ua) || 
							   (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
	
			if (isTouchApple) {
				enableBtn.style.display = 'block';
				statusDiv.textContent = '✅ Player found - tap 🔊 button';
			}
		}
	
		checkAndShowUnlock();
	
		const enableBtn = document.getElementById('enableAudio');
		if (enableBtn) {
		
			enableBtn.addEventListener('click', async function(e) {
				e.preventDefault();
				statusDiv.textContent = '🔓 Unlocking + waiting for controls...';
				
				try {
					// 1. Unlock Tone.js FIRST
					if (window.Tone && Tone.start) {
						await Tone.start();
						statusDiv.textContent = '✅ Tone unlocked';
					}
			
					const player = document.getElementById('mainPlayer');
					if (!player) {
						statusDiv.textContent = '❌ Player missing';
						return;
					}
			
					// 2. FORCE player to fully render controls (iPhone needs this)
					player.style.visibility = 'hidden';
					player.style.visibility = 'visible';
					
					// 3. Wait 800ms for buttons to appear, THEN click
					setTimeout(() => {
						const playBtn = player.querySelector('button, [role="button"], *');
						if (playBtn && playBtn !== player) {
							playBtn.click();
							statusDiv.textContent = '✅ Play button clicked!';
						} else {
							// NUCLEAR OPTION: Double-click entire player
							player.click();
							setTimeout(() => player.click(), 100);
							statusDiv.textContent = '✅ Player double-clicked';
						}
					}, 800);
			
					enableBtn.textContent = '✅ Audio Unlocked! 🎸';
					enableBtn.style.background = '#28a745';
					
				} catch(err) {
					statusDiv.textContent = '❌ ' + err.message;
				}
			});
			
		}
	}

    // 2. FIXED Reset - clears EVERYTHING properly
    const form = document.getElementById('qtabForm');
    const filenameInput = document.getElementById('filenameInput');
    
    if (form) {
        form.addEventListener('reset', function() {
            // Clear filename
            if (filenameInput) {
                filenameInput.value = '';
                filenameInput.style.display = 'none';
            }
            
            // Remove ALL result content (logs, player, timeline, download)
            const allPanels = document.querySelectorAll('.panel');
            allPanels.forEach(panel => {
                if (!panel.classList.contains('file-drop')) {
                    panel.remove();
                }
            });
            
            // Clear status
            statusDiv.textContent = '🧹 Cleared';
            setTimeout(() => statusDiv.textContent = '', 2000);
        });
    }

    // 3. Filename display
    const fileInput = document.getElementById('qtab');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files.length) {
                const filenameInput = document.getElementById('filenameInput');
                if (filenameInput) {
                    filenameInput.value = this.files[0].name;
                    filenameInput.style.display = 'block';
                }
            }
        });
    }

    // Initialize everything
    initAudioUnlock();
});

	// Menu dropdown toggle
	document.querySelector('.menu-toggle').addEventListener('click', function() {
		document.querySelector('.menu-dropdown').classList.toggle('show');
	});
	
	// Close menu on outside click
	document.addEventListener('click', function(e) {
		if (!e.target.closest('nav')) {
			document.querySelector('.menu-dropdown').classList.remove('show');
		}
	});


</script>

</body>
</html>
