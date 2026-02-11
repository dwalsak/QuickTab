# QuickTab

QTab Composer is a modular, web-based music notation app, evolved from decades of pencil‑and‑paper design. It adapts the immediacy of hand-drawn shorthand into a digital system for composing and archiving musical phrases. Built with HTML5, JavaScript, and the Web Audio API, QTab supports intuitive phrase editing and playback, while Python\* tools convert `.qtab` files to MIDI, seamlessly connecting notation to DAW workflows and other creative processes.  

**Try It Now**

Double-click `QTab_Composer_v2.6.html` — works offline in any browser.  

Create phrases, save your library, export ready-to-parse\*\* files.


**How It Works**

1. Write → Browser Tool (.html)

2. Export → Music Files (.qtab and .json) 

3. Import → Python Parser → MIDI → DAW


**What .qtab looks like:**

'G-riff': [
  ['2:0']e,
  ['2:1']e,
  ['2:2']e,
  ['3:0', '2:3', '1:3']e,
  ['3:0', '2:3', '1:3']q,
  ['3:0', '2:2', '1:3']q,
  ['1:x']e,
  ['3:0', '2:2', '1:3']q,
  ['3:0', '2:1', '1:3']e,
  ['3:0', '2:1', '1:3']q,
  ['3:0', '2:0', '1:3']q
]

OR:

'G-riff': 2:0e,2:1e,2:2e,[3:0,2:3,1:3]e,[3:0,2:3,1:3]q,[3:0,2:2,1:3]q,1:xe,[3:0,2:2,1:3]q,
[3:0,2:1,1:3]e,[3:0,2:1,1:3]q,[3:0,2:0,1:3]q

Both are valid.

**Why QTab Rocks**

•	Write guitar ideas faster than TAB paper  
•	No music reading — just string numbers + frets  
•	Rhythm built‑in — quarters, eighths, dotted everything  
•	GarageBand ready — one drag, instant playback  

\* Exporting to .mid requires Python. (See the section on Python Installation -- web link.)  
\*\* .qtab files are ready-to-parse into .mid (MIDI) or standard TAB using Python.


Copyright & License

**QTab Composer v2.6**  
Copyright © 2026 Dennis M. Walsak  
[modularmedia.com/qtab] (https://modularmedia.com/qtab) 
Released under the [MIT License](LICENSE). 
See LICENSE.md file for details.

---

**Dependency Notice**  
• `midiutil` (Python MIDI library) — MIT License  
• HTML/JS/CSS — Public domain (no external libraries)

