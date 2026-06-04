const fs = require('fs');
const path = 'c:/wamp64/www/checkout/resources/views/promoter/public-page.blade.php';
let text = fs.readFileSync(path, 'utf8');
let scripts = [];
let re = /<script[^>]*>([\s\S]*?)<\/script>/gi;
let m;
while ((m = re.exec(text)) !== null) {
  scripts.push(m[1]);
}
let combined = scripts.join('\n\n// ----- SCRIPT BOUNDARY -----\n\n');
// replace @json(...) with null
combined = combined.replace(/@json\((?:[^)(]+|\((?:[^)(]+|\([^)(]*\))*\))*\)/g, 'null');
// remove Blade echo patterns just in case
combined = combined.replace(/\{\{[^}]*\}\}/g, 'null');
fs.writeFileSync('c:/wamp64/www/checkout/tmp_all_scripts.js', combined, 'utf8');
console.log('Wrote tmp_all_scripts.js (' + combined.length + ' bytes)');
// Now attempt to compile using vm
try {
  const vm = require('vm');
  new vm.Script(combined, {filename: 'tmp_all_scripts.js'});
  console.log('OK: Parsed without syntax errors');
} catch (e) {
  console.error('SYNTAX ERROR:', e && e.stack || e);
  process.exitCode = 2;
}
