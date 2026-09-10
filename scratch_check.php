<?php
$content = file_get_contents(__DIR__ . '/resources/views/index_two_product.blade.php');

// Let's search for "date" in the HTML part where package selection or calendar is
preg_match_all('/.*(?:date|calendar|datepicker|pick-a-date|select_date).*/i', $content, $matches, PREG_OFFSET_CAPTURE);

echo "Total matches: " . count($matches[0]) . "\n";
foreach ($matches[0] as $m) {
    $line = substr_count(substr($content, 0, $m[1]), "\n") + 1;
    $text = trim($m[0]);
    if (strlen($text) > 100) $text = substr($text, 0, 100) . '...';
    // Let's filter to interesting lines like UI fields, buttons, js events
    if (stripos($text, 'select') !== false || stripos($text, 'hide') !== false || stripos($text, 'cart') !== false || stripos($text, 'label') !== false || stripos($text, 'input') !== false || stripos($text, 'picker') !== false) {
        echo "L$line: $text\n";
    }
}
