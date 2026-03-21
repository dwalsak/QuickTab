<?php
session_start();
ob_start();

/*
QTab to Standard Tab PDF Generator
Standalone tester - upload QTab → see printable tab notation

Copyright © 2026 Dennis M. Walsak
*/

$isPost = ($_SERVER['REQUEST_METHOD'] === 'POST');
$step = $_POST['step'] ?? 'select';

// ========================================
// UPLOAD FORM (default/first load)
// ========================================
if (!$isPost || $step === 'select') {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QTab → Standard Tab PDF Generator</title>
    <style>
        :root {
            --bg: #fafafa; --bg-alt: #fff; --text: #222; 
            --accent: #0066cc; --accent-alt: #e3f2fd; --accent-green: #32cd32;
            --border: #e0e0e0; 
            --shadow: 0 4px 12px rgba(0,0,0,0.1);
            --shadow-header: 0 2px 4px rgba(0,0,0,0.15), 0 8px 25px rgba(0,0,0,0.25);
            --grey: #666;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, sans-serif; 
            color: var(--text); background: var(--bg);
            max-width: 720px; margin: 20px auto; padding: 40px 20px 20px 20px;
            line-height: 1.6;
        }
        header {
            position: sticky; top: 0; height: 50px;
            backdrop-filter: blur(10px); z-index: 100;
            background-color: black; color: white;
            box-shadow: var(--shadow-header);
        }
        header nav {
            display: flex; justify-content: space-between; align-items: center;
            padding: 0 .5rem; height: 50px; line-height: 50px;
        }
        .logo { font: 700 1.5rem; color: white !important; text-decoration: none; line-height: 1; display: flex; align-items: center; }
        .info { font-weight: normal; font-family: sans-serif; font-size: .8rem; }
        button, input[type="button"], input[type="submit"], .btn {
            -webkit-appearance: none; -moz-appearance: none; appearance: none;
            margin: 0; padding: 0; border: none; border-radius: 0;
            background: none; font: inherit; cursor: pointer; display: inline-block; text-align: center; text-decoration: none;
        }
        .btn-wide {
            width: 100%; padding: 1.2rem; border-radius: 8px;
            font-weight: 600; font-size: 1.1rem; transition: all 0.2s; box-shadow: var(--shadow);
            margin: 1rem 0; display: block; text-align: center;
        }
        .btn-wide.green { background: var(--accent-green); color: white; }
        .btn-wide.grey { background: var(--grey); color: white; }
        .btn-wide:hover { transform: translateY(-1px); }
        .panel {
            background: var(--bg-alt); border-radius: 12px; padding: 2rem;
            border: 1px solid var(--border); box-shadow: var(--shadow); margin: 2rem 0;
        }
        #qtabInput {
            width: 100%; border: 3px solid var(--accent); border-radius: 8px;
            padding: 1rem 1.25rem; font-size: 1.1rem; font-weight: 500; resize: vertical;
            background: white; box-shadow: 0 2px 8px rgba(0,102,204,0.1); margin: 1rem 0;
            min-height: 200px; font-family: monospace;
        }
        .file-drop {
            border: 3px dashed var(--accent); border-radius: 12px; padding: 2rem; text-align: center;
            background: var(--accent-alt); margin: 1.5rem 0; max-width: 100%; overflow: hidden;
        }
        h1, h2 { font-weight: bold; font-family: sans-serif; font-size: 1.5rem; margin: 12px 0; }
        h2 { font-size: 1.2rem; }
        .box-info { 
            background: var(--accent-alt); padding: 1rem; border-radius: 8px; 
            font-size: 14px; margin-bottom: 2rem;
        }
        pre { 
            background: #111; color: #0f0; padding: 20px; border-radius: 8px; max-height: 400px;
            overflow: auto; font-family: monospace; font-size: 14px; line-height: 1.4em;
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
			<h1><a href="#home" class="logo">QTab-To-Tab <span class="info" style="color: white;padding:8px 0 0 8px;">v 1.1</span></a></h1>
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

    <div class="panel">
        <div class="box-info">
            <strong>Test your QTab parsing before PDF:</strong><br>
            Paste QTab text or upload .qtab file → Generate → Print to PDF (Cmd/Ctrl+P → Save as PDF).
        </div>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="step" value="generate_tab">
            
            <div class="file-drop">
                <label><strong>Paste QTab or upload .qtab:</strong></label><br><br>
                <textarea name="qtab_text" id="qtabInput" 
                    placeholder="T[std]: 1:e,2:B,3:G,4:D,5:A,6:E
#Acute-Phrase
[1:9,2:10,3:9]q,[1:7,2:9,3:9]q,[1:5,2:7,3:6]q,[1:5,2:5,3:4]q,[1:2,2:3,3:2]q,[2:2,3:2,4:2]q,[1:2,2:3,3:2,4:0]q,[1:4,2:5,3:4]q"></textarea>
                <br><small>OR</small><br>
                <input type="file" name="qtab_file" accept=".qtab,text/plain" 
                       style="margin-top: 1rem; padding: 0.75rem;">
            </div>

            <button class="btn-wide green" type="submit">Generate Standard Tab → Print to PDF</button>
            <button class="btn-wide grey" type="reset">Clear</button>
        </form>
    </div>

<footer style="margin-top:24px;">
  <p><strong>QTab-To-Tab v1.1</strong><br>
  Copyright © 2026 Dennis M. Walsak</p>
</footer>


	<script>
	document.querySelector('input[type="file"]').addEventListener('change', function() {
		if (this.files[0]) {
			const reader = new FileReader();
			reader.onload = function(e) {
				document.getElementById('qtabInput').value = e.target.result;
			};
			reader.readAsText(this.files[0]);
		}
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
<?php
exit;
}

// ========================================
// PARSE QTAB + GENERATE TAB HTML (SINGLE CSS)
// ========================================

$qtab_content = $_POST['qtab_text'] ?? '';
if (isset($_FILES['qtab_file']) && $_FILES['qtab_file']['tmp_name']) {
    $qtab_content = file_get_contents($_FILES['qtab_file']['tmp_name']);
}

if (empty(trim($qtab_content))) {
    echo "<div class='panel'><h3>❌ No QTab content found</h3></div>";
    exit;
}

// ========================================
// === CORE PARSING FUNCTIONS ===
// ========================================
// parse_full_qtab, parse_chord_notes, write_group, split_long_riff]

function parse_full_qtab($quicktab_text) {
    $lines = explode("\n", $quicktab_text);
    $note_lines = [];
    $tuning_line = null;
    $song_title = null;
    $ad_hoc_riffs = [];

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || $line === '') continue;
        if (strpos($line, 'T[') === 0) {
            $tuning_line = $line;
            continue;
        }
        if (strpos($line, '#') === 0 && !$song_title) {
            $song_title = trim(substr($line, 1));
            continue;
        }
        if (strpos($line, ':') !== false && preg_match('/[123456]/', $line)) {
            $note_lines[] = $line;
        }
    }

    // Clean durations
    $duration_chars = 'qwehts.~.';
    $cleaned = '';
    foreach (str_split(implode($note_lines)) as $char) {
        if (strpos($duration_chars, $char) === false) {
            $cleaned .= $char;
        }
    }

    // Extract chords
    $chords_parsed = [];
    $i = 0;
    $len = strlen($cleaned);
    while ($i < $len) {
        if ($cleaned[$i] === '[') {
            $start = $i;
            $depth = 1;
            $i++;
            while ($i < $len && $depth > 0) {
                if ($cleaned[$i] === '[') $depth++;
                elseif ($cleaned[$i] === ']') $depth--;
                $i++;
            }
            $chords_parsed[] = substr($cleaned, $start, $i - $start);
        } else {
            $i++;
        }
    }

    // Replace chords
    $working = $cleaned;
    foreach ($chords_parsed as $j => $chord) {
        $working = str_replace($chord, "CHORD_$j", $working);
    }

    // Split into groups
    $groups = preg_split('~(?<!:)\|~', $working);
    $groups = array_filter(array_map('trim', $groups));
    if (empty($groups) && !empty(trim($working))) {
        $groups = ['MAIN'];
        $ad_hoc_riffs['MAIN'] = $working;
    }

    // Riff detection
    $final_groups = [];
    $riff_id = 0;
    foreach ($groups as $group) {
        $note_count = preg_match_all('/\d+:\d+|CHORD_\d+/', $group);
        if ($note_count > 5) {
            $riff_id++;
            $riff_name = "R$riff_id";
            $ad_hoc_riffs[$riff_name] = $group;
            $final_groups[] = $riff_name;
        } else {
            $final_groups[] = $group;
        }
    }

    return [$final_groups, $ad_hoc_riffs, $chords_parsed, 
            $tuning_line ?: 'T[std]: 1:e,2:B,3:G,4:D,5:A,6:E', 
            $song_title ?: 'Untitled'];
}

function parse_chord_notes($chord_content) {
    $chord_dict = [];
    if (preg_match_all('/(\d+):([^,\]\s]+)/', $chord_content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $s_num = (int)$match[1];
            $fret_str = trim(rtrim($match[2], 'qwehts.~.'));
            $fret = ($fret_str === 'X' || strtoupper($fret_str) === 'X') ? 'X' : (int)$fret_str;
            $chord_dict[$s_num] = $fret;
        }
    }
    return $chord_dict;
}

function write_group($group_name, $beat_pos, &$tab_lines, $chords, $chords_parsed) {
    // Helper: Write 3-char fret slot: "-X-" or "-11"
    $write_fret_slot = function($line_idx, $slot_pos, $fret) use (&$tab_lines) {
        $fret_str = $fret === 'X' ? 'X' : (string)$fret;
        $len = strlen($fret_str);

        if ($len === 1) {
            // "-7-"
            $tab_lines[$line_idx][$slot_pos + 0] = '-';
            $tab_lines[$line_idx][$slot_pos + 1] = $fret_str[0];
            $tab_lines[$line_idx][$slot_pos + 2] = '-';
        } else {
            // "-11"
            $tab_lines[$line_idx][$slot_pos + 0] = '-';
            $tab_lines[$line_idx][$slot_pos + 1] = $fret_str[0];
            $tab_lines[$line_idx][$slot_pos + 2] = $fret_str[1];
        }
    };

    // === CHORD_N handling (1 slot = 3 chars) ===
    if (preg_match('/^CHORD_(\d+)$/', $group_name, $matches)) {
        $idx = (int)$matches[1];
        if (isset($chords_parsed[$idx])) {
            $content = substr($chords_parsed[$idx], 1, -1);
            $chord_dict = parse_chord_notes($content);
            foreach ($chord_dict as $s_num => $fret) {
                if ($s_num >= 1 && $s_num <= 6) {
                    $write_fret_slot($s_num - 1, $beat_pos, $fret);
                }
            }
        }
        return 3;  // FIXED: 1 slot = 3 chars (not 24!)
    }

    // === RIFF/GROUP handling ===
    if (!isset($chords[$group_name])) {
        return 3;  // FIXED: 1 slot minimum
    }

    $content = $chords[$group_name];
    $items = preg_split('/[,;\/]/', $content);
    $local_pos = 0;

    foreach (array_filter(array_map('trim', $items)) as $item) {
        // Sub-chord (1 slot)
        if (preg_match('/^CHORD_(\d+)$/', $item, $matches)) {
            $idx = (int)$matches[1];
            if (isset($chords_parsed[$idx])) {
                $chord_content = substr($chords_parsed[$idx], 1, -1);
                $chord_dict = parse_chord_notes($chord_content);
                foreach ($chord_dict as $s_num => $fret) {
                    if ($s_num >= 1 && $s_num <= 6) {
                        $write_fret_slot($s_num - 1, $beat_pos + $local_pos, $fret);
                    }
                }
            }
            $local_pos += 3;  // FIXED: 1 slot = 3 chars (not 24!)
        } 
        // Single note(s)
        elseif (strpos($item, ':') !== false) {
            [$s_str, $fret_part] = explode(':', $item, 2);
            $s_num = (int)trim($s_str);
            
            foreach (preg_split('/,/', $fret_part) as $fret_str) {
                $fret_str = trim(rtrim($fret_str, 'qwehts.~.'));
                if ($fret_str === 'X' || is_numeric($fret_str)) {
                    $fret = ($fret_str === 'X') ? 'X' : (int)$fret_str;
                    if ($s_num >= 1 && $s_num <= 6) {
                        $write_fret_slot($s_num - 1, $beat_pos + $local_pos, $fret);
                    }
                    $local_pos += 3;  // FIXED: 1 slot = 3 chars (not 4!)
                }
            }
        }
    }
    return max($local_pos, 3);
}

function split_long_riff($group_name, &$chords, $max_chords = 8) {  // INCREASED from 6
    if (!isset($chords[$group_name])) {
        return [$group_name];
    }

    $content = $chords[$group_name];
    $items = array_filter(array_map('trim', explode(',', $content)));
    
    //echo "🔨 SPLIT DEBUG: $group_name has " . count($items) . " items<br>";
    
    if (count($items) <= $max_chords) {
        return [$group_name];  // Don't split short riffs
    }
    
    $chunks = [];
    $current_chunk = [];
    
    foreach ($items as $item) {
        $current_chunk[] = $item;
        if (count($current_chunk) >= $max_chords) {
            $chunks[] = implode(',', $current_chunk);
            $current_chunk = [];
        }
    }
    if (!empty($current_chunk)) {
        $chunks[] = implode(',', $current_chunk);
    }
    
    $split_groups = [];
    for ($i = 0; $i < count($chunks); $i++) {
        $new_name = "R" . (100 + $i * 10);
        $chords[$new_name] = $chunks[$i];
        $split_groups[] = $new_name;
        
        //echo "✂️ Created $new_name (" . substr($chunks[$i], 0, 30) . "...)";
    }
    
    return $split_groups;
}

// END CORE FUNCTIONS HERE

// ========================================
// EXECUTE PARSING
// ========================================

list($groups, $chords, $chords_parsed, $tuning_line, $song_title) = parse_full_qtab($qtab_content);
$string_labels = ['e','B','G','D','A','E'];

// Build all groups (split long riffs)
$all_groups = [];
foreach ($groups as $g) {
    $split_groups = split_long_riff($g, $chords);
    $all_groups = array_merge($all_groups, $split_groups);
}

// Build staves (1 group per stave)
$staves = [];
foreach ($all_groups as $g) {
    $staves[] = [$g];
}
$stave_count = count($staves);


// ========================================
// GENERATE PRINTABLE HTML
// ========================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=794, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($song_title); ?> – Standard Tab</title>
    <style>
        :root {
            --bg-pdf: #fff; --text-pdf: #000; --accent-pdf: #0066cc;
        }
        @page { 
            size: A4; margin: 15mm 20mm 15mm 20mm; 
            @bottom-center { 
                content: "<?php echo htmlspecialchars($song_title); ?> | Page " counter(page) " | <?php echo htmlspecialchars($tuning_line); ?>"; 
                font-size: 9pt; font-family: monospace;
            }
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: "Courier New", Courier, monospace; 
            font-size: 10pt; line-height: 1.25; color: var(--text-pdf); 
            max-width: 100%; background: var(--bg-pdf);
        }
        .title { 
            text-align: center; font-size: 18pt; font-weight: bold; 
            margin: 20px 0 30px 0; page-break-after: avoid;
        }
        .stave-block { margin-bottom: 24px; page-break-inside: avoid; }
        .stave-label {
            font-size: 9pt; color: #333; margin-bottom: 6px; font-weight: bold;
        }
        pre.tab {
            font-family: "Courier New", Courier, monospace !important;
            font-size: 10pt !important; line-height: 1.15 !important;
            white-space: pre !important; margin: 0 !important; tab-size: 1;
        }
		pre.tab .num {
			font-family: "Courier New Bold", Courier-Bold, Courier, monospace !important;
		}
        .debug {
            background: #f8f9fa; padding: 1rem; border-radius: 8px; 
            font-size: 11pt; margin: 2rem 0;
        }
        @media print {
            body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .debug, .print-hint { display: none !important; }
        }
        @media screen {
            body { margin: 20px; background: #fafafa; }
            .print-hint {
                position: fixed; top: 10px; right: 10px; background: var(--accent-pdf); 
                color: white; padding: 10px; border-radius: 6px; font-size: 12pt; 
                z-index: 1000;
            }
        }
        @supports (-webkit-touch-callout: none) {
            @page { margin: 0.5in !important; }
            body { width: 8.27in !important; max-width: 8.27in !important; padding: 0.25in !important; }
        }
        @media (max-width: 480px) {
            .print-hint { position: static; margin: 1rem 0; text-align: center; }
        }
    </style>
</head>
<body>
    <div class="print-hint">📱 Cmd/Ctrl+P → Save as PDF</div>
    <div class="title"><?php echo htmlspecialchars($song_title); ?></div>

    <?php
    foreach ($staves as $stave_idx => $stave_groups):
        $tab_lines = array_fill(0, 6, array_fill(0, 400, '-'));
        $beat_pos = 2;

        foreach ($stave_groups as $group_name) {
            $span = write_group($group_name, $beat_pos, $tab_lines, $chords, $chords_parsed);
            $beat_pos += $span;
        }

        $used_chars = $beat_pos;
        $max_chars = 120;
        $content_end = min($used_chars, $max_chars);
        $trailing_chars = 12;
        $safe_end = $content_end + $trailing_chars;

        for ($i = 0; $i < 6; $i++) {
            $tab_lines[$i][0] = '|';
            $tab_lines[$i][1] = '|';
            for ($pos = $content_end; $pos < $safe_end; $pos++) {
                if ($pos < 400) $tab_lines[$i][$pos] = '-';
            }
            $end_bar_pos = min($safe_end - 1, 399);
            $tab_lines[$i][$end_bar_pos] = '|';
        }

		$stave_lines = [];
		for ($i = 0; $i < 6; $i++) {
			$line_str = '';
			for ($j = 0; $j <= $end_bar_pos; $j++) {
				$line_str .= $tab_lines[$i][$j];
			}
		
			// Original monospace line (letters + pipes + dashes + digits)
			$full_line = $string_labels[$i] . $line_str;
		
			// Wrap numeric runs (fret numbers) in a span
			// This will turn e.g. "--7-" into "--<span class=\"num\">7</span>-"
			// and "--11" into "--<span class=\"num\">11</span>"
			$full_line = preg_replace(
				'/(\d+)/',
				'<span class="num">$1</span>',
				$full_line
			);
		
			$stave_lines[] = $full_line;
		}
		?>
		<div class="stave-block">
			<div class="stave-label">Stave <?php echo ($stave_idx + 1); ?>/<?php echo $stave_count; ?></div>
			<pre class="tab"><?php echo implode("\n", $stave_lines); ?></pre>
		</div>
		<?php endforeach; ?>

	<?php //if (isset($_GET['debug'])): ?>
	<div class="debug">
		<h3>✅ <?php echo $stave_count; ?> staves generated</h3>
		<p><strong>Groups parsed:</strong> <?php echo htmlspecialchars(implode(', ', $groups)); ?></p>
		<p><strong>Tuning:</strong> <?php echo htmlspecialchars($tuning_line); ?></p>
		<a href="?debug=1" style="color: var(--accent-pdf); font-weight: bold;">View Raw Debug</a> | 
		<a href="" onclick="window.print(); return false;" style="color: var(--accent-green); font-weight: bold;">🖨️ Print Now</a>
	</div>
	<?php // endif; ?>

    <script>
        if (navigator.userAgent.includes('iPhone') || navigator.userAgent.includes('iPad')) {
            const hint = document.createElement('div');
            hint.innerHTML = '📱 <strong>Share → Print → Save as PDF</strong>';
            hint.style.cssText = 'position:fixed;top:10px;left:10px;background:#ff6b35;color:white;padding:12px;border-radius:8px;z-index:9999;font-weight:bold;';
            document.body.appendChild(hint);
        }
    </script>
</body>
</html>
<?php
$html = ob_get_clean();
header('Content-Type: ' . 'text/html; charset=utf-8');
echo $html;
?>
