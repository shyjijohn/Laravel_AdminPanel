<?php
// Correct paths when link.php is inside the project root:
$target = __DIR__ . '/storage/app/public';
$shortcut = __DIR__ . '/public/storage';

// Remove broken symlink if it exists
if (is_link($shortcut) || file_exists($shortcut)) {
    if (is_dir($shortcut) && !is_link($shortcut)) {
        echo 'Error: "public/storage" exists as a regular folder. Delete the "storage" folder inside "public/" in cPanel File Manager first.';
        exit;
    }
    unlink($shortcut);
}

// Create correct symlink from /public/storage -> /storage/app/public
if (symlink($target, $shortcut)) {
    echo 'Correct symlink created successfully!';
} else {
    echo 'Failed to create symlink. Check permissions.';
}
?>