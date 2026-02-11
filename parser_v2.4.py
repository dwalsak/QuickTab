from midiutil import MIDIFile
import sys
import os
import json

DURATION_MAP = {'w':4,'w.':6,'h':2,'h.':3,'q':1,'q.':1.5,'e':0.5,'e.':0.75,'s':0.25,'s.':0.375,'t':0.125,'t.':0.1875}
open_notes = [40,45,50,55,59,64]  # strings 6–1

def quicktab_to_midi(song_input, filename="song.mid"):
    """v2.4 - Auto-detects JSON or string .qtab - ONE COMMAND WORKS FOR ALL!"""

    # --- FILE HANDLER (unchanged) ---
    if isinstance(song_input, str) and song_input.endswith('.qtab'):
        qtab_file = song_input
        try:
            with open(qtab_file, 'r') as f:
                song = f.read()
            filename = qtab_file.replace('.qtab', '.mid')
            print(f"📁 Loaded .qtab: {qtab_file} → {filename}")
        except FileNotFoundError:
            print(f"❌ File not found: '{qtab_file}'")
            return
    else:
        song = song_input

    # --- AUTO-DETECT JSON vs STRING (NEW v2.4 MAGIC) ---
    mf = MIDIFile(1)
    mf.addTempo(0, 0, 90)
    mf.addProgramChange(0, 0, 0, 24)
    time = 0
    
    try:
        song_data = json.loads(song)
        if 'steps' in song_data:
            print("📊 JSON format detected - perfect durations!")
            # JSON PATH - bulletproof
            for i, step in enumerate(song_data['steps']):
                notes = step['notes']
                dur_str = step['duration']
                dur = DURATION_MAP.get(dur_str, 1.0)
                print(f"  JSON Step {i+1}: {len(notes)} notes → {dur_str} ({dur})")
                
                for note in notes:
                    if ':' in note:
                        string, fret = note.split(':', 1)
                        if fret != 'x' and fret.isdigit():
                            pitch = open_notes[6-int(string)] + int(fret)
                            mf.addNote(0, 0, pitch, time, dur, 100)
                time += dur
            with open(filename, 'wb') as f:
                mf.writeFile(f)
            print(f"✅ JSON saved {filename} ({time:.2f}s)")
            return
    except json.JSONDecodeError:
        pass

    print("📝 String format detected - original v2.3 logic")
    
    # --- YOUR ORIGINAL STRING PARSING (proven perfect) ---
    lines = song.splitlines()
    note_lines = []
    for line in lines:
        line = line.strip()
        if not line or line.startswith('#'):
            continue
        if ':' in line and any(c in line for c in '123456'):
            note_lines.append(line)
    
    if note_lines:
        song = ','.join(note_lines)
    cleaned = ''.join(song.split())
    print(f"🧹 CLEANED INPUT: {cleaned}")

    # Chord parsing (your original)
    chords = []
    chord_full_texts = []
    i = 0
    while i < len(cleaned):
        if cleaned[i] == '[':
            start = i
            depth = 1
            i += 1
            while i < len(cleaned) and depth > 0:
                if cleaned[i] == '[': depth += 1
                elif cleaned[i] == ']': depth -= 1
                i += 1
            content = cleaned[start+1:i-1].strip()
            end = i
            if i < len(cleaned) and cleaned[i] in 'qwehts.':
                end += 1
            full_chord = cleaned[start:end]
            chords.append(content)
            chord_full_texts.append(full_chord)
            print(f"  CHORD {len(chords)}: '{content}' (dur={full_chord[-1]})")
        i += 1

    working_text = cleaned
    for j, full_chord in enumerate(chord_full_texts):
        placeholder = f"CHORD_{j}"
        working_text = working_text.replace(full_chord, placeholder, 1)

    simple_parts = [p.strip() for p in working_text.split(',') if p.strip()]

    # Process parts
    for part in simple_parts:
        if part.startswith('CHORD_'):
            chord_part = part.replace('.', '').split('_')[1]
            try:
                chord_idx = int(chord_part)
                if chord_idx < len(chords):
                    chord_content = chords[chord_idx]
                    full_chord = chord_full_texts[chord_idx]
                    
                    # –> durations                   
                    dur_str = full_chord[-1] if full_chord[-1] in DURATION_MAP else 'q'
                    
                    dur = DURATION_MAP[dur_str]
                    print(f"  🎸 PLAYING CHORD {chord_idx} (dur={dur}): {chord_content}")
                    
                    chord_notes = []
                    for item in chord_content.split(','):
                        item = item.strip()
                        if ':' in item:
                            string, fret = item.split(':', 1)
                            fret = fret.rstrip('qwehts.]')
                            if fret != 'x' and fret.isdigit():
                                pitch = open_notes[6-int(string)] + int(fret)
                                chord_notes.append(pitch)
                    for pitch in chord_notes:
                        mf.addNote(0, 0, pitch, time, dur, 100)
                    time += dur
            except:
                continue
        elif ':' in part:
            # FIXED SINGLE NOTES (your proven version)
            dur_str = 'q'
            core = part
            if len(part) >= 2 and part[-2:] in DURATION_MAP:
                dur_str = part[-2:]
                core = part[:-2]
            elif len(part) >= 1 and part[-1] in DURATION_MAP:
                dur_str = part[-1]
                core = part[:-1]
                
            dur = DURATION_MAP[dur_str]
            print(f"  🎵 SINGLE NOTE: {part} → '{core}' dur={dur}")
            
            for item in core.split(','):
                item = item.strip()
                if ':' in item:
                    string, fret = item.split(':', 1)
                    fret = fret.rstrip('qwehts.')
                    if fret == 'x':
                        print(f"    💀 Dead note: {item}")
                        continue
                    if fret.isdigit():
                        pitch = open_notes[6-int(string)] + int(fret)
                        mf.addNote(0, 0, pitch, time, dur, 100)
                        print(f"    → {string}:{fret} = {pitch}")
            time += dur

    print(f"\n✅ Saved {filename} ({time:.1f}s @90BPM) - GarageBand ready!")
    with open(filename, 'wb') as f:
        mf.writeFile(f)

if __name__ == "__main__":
    if len(sys.argv) > 1:
        qtab_path = sys.argv[1]
        if not os.path.exists(qtab_path):
            print(f"⚠️  '{qtab_path}' not found — using Demo_Song.qtab")
            qtab_path = "Demo_Song.qtab"
        quicktab_to_midi(qtab_path)
    else:
        print("ℹ️  Usage: python3 parser_v2.4.py song.qtab")
        quicktab_to_midi("Demo_Song.qtab")


# Example Terminal Command (assumes the .qtab is in the current directory)
# --> python3 parser_v2.4.py G-Blues-Boogie.qtab