<?php
/**
 * Laravel Starter Setup Script
 * Supports Web or API project
 */

$root = __DIR__ . '/../';
$src = $root . 'src/';
$templates = $root . 'templates/';

// Paths
$bootstrapFile = $src . 'bootstrap/app.php';
$webRoutes = $src . 'routes/web.php';
$apiRoutes = $src . 'routes/api.php';
$viewsPath = $src . 'resources/views';

// Helper to delete folder recursively
function deleteFolder($folder) {
    if (!is_dir($folder)) return;
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($items as $item) {
        $item->isDir() ? rmdir($item) : unlink($item);
    }
    rmdir($folder);
}

// Step 1: Ask for project type
$handle = fopen("php://stdin", "r");
echo "Which type of project do you want? (web/api) [web]: ";
$type = trim(fgets($handle));
if ($type === '') $type = 'web';
fclose($handle);

echo "\nYou chose: $type\n";

// Step 2: Copy bootstrap file
if ($type === 'api') {
    copy($templates . 'bootstrap-app-api.php', $bootstrapFile);
    echo "Bootstrap file set for API project.\n";

    // Remove web-specific files
    if (file_exists($webRoutes)) unlink($webRoutes);
    if (is_dir($viewsPath)) deleteFolder($viewsPath);

    // Ensure API route exists with ping
    $apiContent = <<<PHP
<?php

use Illuminate\Support\Facades\Route;

Route::get('/ping', function() {
    return response()->json(['pong' => true]);
});

// Add other API routes below
PHP;
    file_put_contents($apiRoutes, $apiContent);

} else { // web
    copy($templates . 'bootstrap-app-web.php', $bootstrapFile);
    echo "Bootstrap file set for Web project.\n";

    // Remove API-specific files
    if (file_exists($apiRoutes)) unlink($apiRoutes);

    // Ensure Web route exists
    if (!file_exists($webRoutes)) {
        file_put_contents($webRoutes, "<?php\nuse Illuminate\Support\Facades\Route;\nRoute::get('/', fn() => view('welcome'));\n");
    }

    // Ensure views exist
    if (!is_dir($viewsPath)) mkdir($viewsPath, 0755, true);
    if (!file_exists($viewsPath . '/welcome.blade.php')) {
        file_put_contents($viewsPath . '/welcome.blade.php', "<h1>Welcome to Web Project</h1>");
    }
}

echo "\nSetup complete! Your Laravel project is now configured as a '$type' project.\n";
