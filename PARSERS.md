# QTab Parsers Overview

## Python Parser (parser_v2.4.py)
- **Input**: Auto-detects .qtab strings or JSON; falls back to heuristic string parsing
- **Key Behaviors**:
  - Cleans whitespace, skips `#` comments
  - Inline chords `[1:0,2:2]q` → placeholder substitution during playback
  - Default duration: `q` (quarter); detects dots (e.g., `q.`)
  - Tuning: Hardcoded EADGBE (open_notes = [40,45,50,55,59,64])
  - Output: MIDI (.mid) at 90BPM, program 24 (acoustic guitar)
- **Quirks**: String order 6→1 auto-reverses; omitted strings = muted

## PHP Parser (with MIDI Player)
- **Input**: .qtab files via web interface
- **Key Behaviors**:
  - [Fill from your PHP implementation—real-time playback? Browser MIDI?]
  - Supports sequence playback (`|` delimited)
  - Chord/phrase references (`C[]`, `P[]`)
- **Quirks**: [Browser limitations? Duration handling?]

## Comparison Table
| Feature | Python v2.4 | PHP MIDI Player |
|---------|-------------|-----------------|
| JSON Support | ✅ Auto-detect | ❓ |
| Real-time Playback | ❌ MIDI export | ✅ Browser |
| Tuning Config | ❌ Hardcoded | ❓ |
| Chord Nesting | ✅ Deep brackets | ❓ |

