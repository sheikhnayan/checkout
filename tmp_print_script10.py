import re, pathlib
p=pathlib.Path(r'c:\wamp64\www\checkout\resources\views\affiliate\public-page.blade.php')
s=p.read_text(encoding='utf-8', errors='ignore')
scripts=re.findall(r'<script[^>]*>(.*?)</script>', s, re.S|re.I)
script = scripts[9]  # zero-based
lines = script.splitlines()
start = max(0, 860)
end = min(len(lines), 900)
print('Script 10 lines', start+1, 'to', end)
for i in range(start,end):
    print(str(i+1).rjust(4), lines[i])
