import pathlib
path = pathlib.Path(r'c:\wamp64\www\checkout\resources\views\affiliate\public-page.blade.php')
with path.open('r', encoding='utf-8', errors='ignore') as f:
    lines = f.readlines()
print(len(lines))
print('---')
for line in lines[-20:]:
    print(line.rstrip())
