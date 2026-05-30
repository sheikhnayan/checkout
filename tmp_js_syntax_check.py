import re, pathlib
p=pathlib.Path(r'c:\wamp64\www\checkout\resources\views\affiliate\public-page.blade.php')
s=p.read_text(encoding='utf-8', errors='ignore')
scripts=re.findall(r'<script[^>]*>(.*?)</script>', s, re.S|re.I)
problems=[]
for si, script in enumerate(scripts,1):
    stack=[]
    single=False
    double=False
    backtick=False
    escaped=False
    for lineno, line in enumerate(script.splitlines(),1):
        for i,ch in enumerate(line,1):
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
                stack.append((ch, lineno, i))
            elif ch in ')]}':
                if not stack:
                    problems.append((si, lineno, i, 'unmatched_closing', ch))
                    raise SystemExit
                opench, ol, oi = stack.pop()
                pairs={')':'(',']':'[','}':'{'}
                if pairs.get(ch)!=opench:
                    problems.append((si, lineno, i, 'mismatched', opench+ ' vs ' + ch))
                    raise SystemExit
    if single or double or backtick:
        problems.append((si, None, None, 'unclosed_quote', 'single' if single else ('double' if double else 'backtick')))
    if stack:
        problems.append((si, stack[-1][1], stack[-1][2], 'unclosed_bracket', stack[-1][0]))

if not problems:
    print('OK: no issues found in', len(scripts), 'script blocks')
else:
    for p in problems:
        print('PROBLEM in script', p)
