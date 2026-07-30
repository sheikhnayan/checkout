import re,pathlib
p=pathlib.Path(r'c:\wamp64\www\checkout\resources\views\affiliate\public-page.blade.php')
s=p.read_text(encoding='utf-8', errors='ignore')
scripts=re.findall(r'<script[^>]*>(.*?)</script>', s, re.S|re.I)
s= scripts[9]
start=920; end=930
single=double=backtick=False
escaped=False
for lineno,line in enumerate(s.splitlines(),1):
    if lineno<start or lineno> end: continue
    print('Line', lineno, repr(line))
    for idx,ch in enumerate(line,1):
        state = ('S' if single else '.')+('D' if double else '.')+('B' if backtick else '.')+('E' if escaped else '.')
        print(idx, ch, state)
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
    print('STATE after line', lineno, 'S D B E ->', single,double,backtick,escaped)    
