import re, pathlib
print('RUNNING JAVASCRIPT-LIKE CHECK')
path = pathlib.Path(r'c:\wamp64\www\checkout\resources\views\affiliate\public-page.blade.php')
text = path.read_text(encoding='utf-8', errors='ignore')
scripts = re.findall(r'<script[^>]*>(.*?)</script>', text, re.S|re.I)
print('Found', len(scripts), 'script blocks')
for si, script in enumerate(scripts,1):
    print('\n--- Script block', si, '---')
    stack=[]
    single=double=backtick=False
    escaped=False
    for lineno, line in enumerate(script.splitlines(),1):
        for idx,ch in enumerate(line,1):
            if escaped:
                escaped=False
                continue
            if ch=='\\':
                escaped=True
                continue
            if ch=="'" and not double and not backtick:
                single= not single
                continue
            if ch=='"' and not single and not backtick:
                double= not double
                continue
            if ch=='`' and not single and not double:
                backtick= not backtick
                continue
            if single or double or backtick:
                continue
            if ch in '([{':
                stack.append((ch, lineno, idx, line.strip()))
            elif ch in ')]}':
                if not stack:
                    print('Unmatched closing', ch, 'at line', lineno, 'col', idx)
                    raise SystemExit(0)
                opench, ol, oi, olinestr = stack.pop()
                pairs={')':'(',']':'[','}':'{'}
                if pairs.get(ch)!=opench:
                    print('Mismatched', opench, 'vs', ch, 'opened at', ol, 'col', oi)
                    raise SystemExit(0)
    if single or double or backtick:
        print('Unclosed quote in script', si, 'type', 'single' if single else ('double' if double else 'backtick'))
    if stack:
        last = stack[-1]
        print('Unclosed bracket', last[0], 'opened at line', last[1], 'col', last[2])
print('\nCheck complete')
