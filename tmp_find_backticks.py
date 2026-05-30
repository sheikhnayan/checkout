import re, pathlib
p=pathlib.Path(r'c:\wamp64\www\checkout\resources\views\affiliate\public-page.blade.php')
s=p.read_text(encoding='utf-8', errors='ignore')
scripts=re.findall(r'<script[^>]*>(.*?)</script>', s, re.S|re.I)
s= scripts[9]
for i,line in enumerate(s.splitlines(),1):
    if '`' in line:
        print(i, line)
