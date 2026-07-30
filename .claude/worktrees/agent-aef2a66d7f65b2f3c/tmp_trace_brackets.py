import re, pathlib
p=pathlib.Path(r'c:\wamp64\www\checkout\resources\views\affiliate\public-page.blade.php')
s=p.read_text(encoding='utf-8', errors='ignore')
scripts=re.findall(r'<script[^>]*>(.*?)</script>', s, re.S|re.I)
script=scripts[9]
stack=[]
single=double=backtick=False
escaped=False
for lineno,line in enumerate(script.splitlines(),1):
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
            print('BACKTICK toggle at', lineno, 'now', backtick)
            continue
        if single or double or backtick:
            continue
        if ch in '([{':
            stack.append((ch, lineno, idx))
            if 780<=lineno<=940:
                print('PUSH', ch, 'at', lineno, idx, 'stacklen', len(stack))
        elif ch in ')]}':
            if not stack:
                print('UNMATCHED CLOSING', ch, 'at', lineno, idx)
                raise SystemExit
            opench, ol, oi = stack.pop()
            if 780<=lineno<=940:
                print('POP', ch, 'at', lineno, idx, 'matched', opench, 'opened at', ol, oi, 'stacklen', len(stack))
            pairs={')':'(',']':'[','}':'{'}
            if pairs.get(ch)!=opench:
                print('MISMATCH at', lineno, idx, 'found', ch, 'top', opench)
                raise SystemExit
print('done')
