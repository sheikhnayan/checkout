<?php
$content = file_get_contents(__DIR__ . '/resources/views/index_two_product.blade.php');
$lines = explode("\n", $content);
$start = 0;
foreach ($lines as $i => $line) {
    if (strpos($line, '<main') !== false) {
        $start = $i;
        break;
    }
}
echo "Main starts at line " . ($start + 1) . "\n";

$void_tags = ['input', 'img', 'br', 'hr', 'meta', 'link', 'source', 'area', 'base', 'col', 'embed', 'param', 'track', 'wbr'];
$stack = [];

for ($idx = $start; $idx < count($lines); $idx++) {
    $line = $lines[$idx];
    $line = preg_replace('/\{\{--.*?--\}\}/', '', $line);
    
    // match tags
    if (preg_match_all('/<\/?([a-zA-Z0-9\-]+)[^>]*>/', $line, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $m) {
            $full = $m[0];
            $tag = strtolower($m[1]);
            // check if self closing
            if (in_array($tag, $void_tags) || preg_match('/\/>$/', trim($full))) {
                continue;
            }
            if (strpos($full, '</') === 0) {
                if (!empty($stack) && end($stack)['tag'] === $tag) {
                    array_pop($stack);
                } else {
                    $top = !empty($stack) ? end($stack)['tag'] . ' (line ' . end($stack)['line'] . ')' : 'EMPTY';
                    echo "Mismatch at line " . ($idx + 1) . ": closing </$tag>, but stack top is $top\n";
                }
            } else {
                $stack[] = ['tag' => $tag, 'line' => $idx + 1, 'text' => substr(trim($line), 0, 70)];
            }
        }
    }
    
    if (strpos($line, 'id="cv-order-sidebar"') !== false) {
        echo "cv-order-sidebar reached at line " . ($idx + 1) . "! Stack count: " . count($stack) . "\n";
        foreach ($stack as $s) {
            echo "  {$s['tag']} from line {$s['line']}: {$s['text']}\n";
        }
        break;
    }
}
