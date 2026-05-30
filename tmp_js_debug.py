import re, pathlib
p=pathlib.Path(r'c:\wamp64\www\checkout\resources\views\affiliate\public-page.blade.php')
s=p.read_text(encoding='utf-8', errors='ignore')
scripts=re.findall(r'<script[^>]*>(.*?)</script>', s, re.S|re.I)
script = scripts[9]
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
            stack.append((ch, lineno, idx, line))
        elif ch in ')]}':
            if not stack:
                print('UNMATCHED CLOSING', ch, 'at', lineno, idx)
                print('Line:', line)
                raise SystemExit
            opench, ol, oi, olinestr = stack.pop()
            pairs={')':'(',']':'[','}':'{'}
            if pairs.get(ch)!=opench:
                print('MISMATCH at line', lineno, 'col', idx)
                print('Found closing', ch)
                print('Top of stack:', opench, 'opened at', ol, 'col', oi)
                print('Stack snapshot (last 10):')
                for item in stack[-10:]:
                    print(' ', item[0], 'opened at', item[1], 'col', item[2], 'line:', item[3].strip())
                print('Current line:', line)
                raise SystemExit
print('Done, stack length', len(stack))
if stack:
    print('Remaining top', stack[-1])
