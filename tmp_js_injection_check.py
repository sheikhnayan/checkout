import pathlib, re
path = pathlib.Path(r'c:\wamp64\www\checkout\resources\views\affiliate\public-page.blade.php')
text = path.read_text(encoding='utf-8', errors='ignore')
scripts = re.findall(r'<script[^>]*>(.*?)</script>', text, re.S | re.I)
for i, script in enumerate(scripts, 1):
    if '{{' in script and '@json' not in script:
        print('SCRIPT', i)
        for j, line in enumerate(script.splitlines(), 1):
            if '{{' in line and '@json' not in line:
                print(j, line.strip())
        print('---')
