#!/usr/bin/env python3
"""Voice check for Environmentle copy. Flags em-dashes, emoji and American
spellings in user-facing files and content data. Run: python3 check-voice.py
Exit code 1 when something needs a look."""
import re, sys, glob, json, csv, pathlib

SOURCE = ['index.html', 'home.js', 'engine.js', 'tour.js', 'app.js', 'sw.js', 'manifest.json',
          'environmentle-quiz-game.html', 'environmentle-sort-it-out.html', 'environmentle-water-challenge.html']
DATA = ['actions.json', 'partners.json', 'challenges.json', 'water-cards.json'] + sorted(glob.glob('courses/*.json'))
CSV = ['cards.csv']

EMOJI = re.compile('[\U0001F300-\U0001FAFF\u2600-\u26FF\u2700-\u2712\u2719-\u27BF\U0001F1E6-\U0001F1FF]')
AMERICAN = re.compile(r'\b(colou?r(?<!colour)s?|favorite|organiz\w*|realiz\w*|recogniz\w*|liters?|meters?|catalog|neighbors?|behaviors?|labor|honor|travel(ed|ing)|analyz\w*|defense|gray|aluminum|fiber|fertilizers?|tires?|programs?(?! ?me))\b', re.I)
NOT_COPY_KEYS = {'id', 'align', 'icon', 'emoji', 'theme', 'type', 'image', 'accent', 'category_key', 'tone', 'level',
                 'scope', 'kind', 'date', 'source_url', 'impact_unit', 'basis', 'pool', 'confidence', 'keywords',
                 'unit_label', 'arithmetic', 'relates_to', 'generated_from', 'version', 'compiled', 'units_note',
                 'research_caveat', 'primary_sources', 'citation', 'url', 'voice', 'note', 'cls', 'class', 'in_play'}

problems = 0
def flag(where, kind, text):
    global problems
    problems += 1
    print(f'{where}: {kind}: {text.strip()[:110]}')

def check_text(where, text, allow_emoji=False):
    if '—' in text: flag(where, 'em-dash', text)
    if not allow_emoji and EMOJI.search(text): flag(where, 'emoji', text)
    for m in AMERICAN.finditer(text):
        if m.group(0).lower().startswith('organization') and 'World Health' in text: continue  # proper name
        flag(where, f'American spelling "{m.group(0)}"', text)

def is_code_line(line):
    s = line.strip()
    return (s.startswith(('//', '/*', '*', '<!--', '.', '#', '@', '}', '{', 'const ', 'let ', 'var ', 'function ', 'if ', 'return ', 'import ', 'export '))
            or re.match(r'^[\w\-]+\s*:\s*[^;]*;\s*$', s) is not None)

for f in SOURCE:
    p = pathlib.Path(f)
    if not p.exists(): continue
    in_style = in_comment = False
    for n, line in enumerate(p.read_text(encoding='utf-8').splitlines(), 1):
        low = line.lower()
        if '<style' in low: in_style = True
        if '</style' in low: in_style = False; continue
        if '/*' in line and '*/' not in line: in_comment = True; continue
        if in_comment:
            if '*/' in line: in_comment = False
            continue
        if in_style or is_code_line(line): continue
        if re.search(r"\b(icon|lucide|cls|tone)\s*:\s*['\"]", line): continue
        if re.search(r"\bbehavior\s*:", line): continue  # scrollIntoView option, not copy
        # strip attribute values that are code (class, style, onclick, data-*)
        copy = re.sub(r'\b(class|style|onclick|data-[\w-]+|id|href|src|aria-hidden|type|name|content)="[^"]*"', '', line)
        copy = re.sub(r'/\*.*?\*/', '', copy)
        check_text(f'{f}:{n}', copy)

def walk(where, obj, key=None):
    if isinstance(obj, dict):
        for k, v in obj.items():
            if k in NOT_COPY_KEYS: continue
            walk(f'{where}.{k}', v, k)
    elif isinstance(obj, list):
        for i, v in enumerate(obj): walk(f'{where}[{i}]', v, key)
    elif isinstance(obj, str):
        check_text(where, obj)

for f in DATA:
    p = pathlib.Path(f)
    if not p.exists(): continue
    walk(f, json.load(open(p, encoding='utf-8')))

for f in CSV:
    p = pathlib.Path(f)
    if not p.exists(): continue
    for i, row in enumerate(csv.DictReader(open(p, encoding='utf-8')), 2):
        for k, v in row.items():
            if k in ('emoji', 'category', 'impact') or not v: continue
            check_text(f'{f}:{i}.{k}', v)

print(f'\n{problems} thing(s) to look at' if problems else '\nVoice check clean')
sys.exit(1 if problems else 0)
