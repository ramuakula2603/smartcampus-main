<?php
/**
 * Check if subjects directory exists
 */

echo "=== CHECKING FOR SUBJECTS DIRECTORY ===\n\n";

$api_dir = __DIR__;
echo "API Directory: $api_dir\n\n";

// List all items in the api directory
echo "Contents of api/ directory:\n";
echo "============================\n";

$items = scandir($api_dir);
foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;
    
    $path = $api_dir . DIRECTORY_SEPARATOR . $item;
    $type = is_dir($path) ? '[DIR]' : '[FILE]';
    
    echo "$type $item\n";
    
    if (strtolower($item) === 'subjects') {
        echo "   ⚠️  FOUND SUBJECTS ITEM!\n";
        echo "   Is directory: " . (is_dir($path) ? "YES" : "NO") . "\n";
        echo "   Is file: " . (is_file($path) ? "YES" : "NO") . "\n";
        echo "   Permissions: " . substr(sprintf('%o', fileperms($path)), -4) . "\n";
    }
}

echo "\n=== END CHECK ===\n";
?>

