<?php
$root = __DIR__ . '/../';
$laravel = $root . 'src/';

$controllersPath = $laravel . 'app/Http/Controllers';
$apiControllersPath = $controllersPath . '/API';
$webControllersPath = $controllersPath . '/Web';
$viewsPath = $laravel . 'resources/views';
$apiRoutes = $laravel . 'routes/api.php';
$webRoutes = $laravel . 'routes/web.php';
$middlewaresPath = $laravel . 'app/Http/Middleware';

// Ask the user
$handle = fopen("php://stdin", "r");
echo "Which type of project do you want? (web/api) [web]: ";
$type = trim(fgets($handle));
if ($type === '') $type = 'web';
fclose($handle);

echo "\nYou chose: $type\n";

// Helper to create .keep files
function createKeepFile($path) {
    if (!file_exists($path . '/.keep')) file_put_contents($path . '/.keep', '');
}

// Setup API or Web
if ($type === 'api') {
    echo "Setting up API project...\n";

    // Remove Web-specific folders
    if (is_dir($webControllersPath)) system("rm -rf " . escapeshellarg($webControllersPath));
    if (is_dir($viewsPath)) system("rm -rf " . escapeshellarg($viewsPath));

    // Create API controllers
    if (!is_dir($apiControllersPath)) mkdir($apiControllersPath, 0755, true);
    createKeepFile($apiControllersPath);

    // Routes
    file_put_contents($webRoutes, "<?php\n// Web routes disabled for API project.\n");
    file_put_contents($apiRoutes, "<?php\nuse Illuminate\Support\Facades\Route;\nRoute::get('/ping', function() {\n    return response()->json(['pong'=>true]);\n});\n");

    // Middleware example
    if (!is_dir($middlewaresPath)) mkdir($middlewaresPath, 0755, true);
    $apiMiddleware = $middlewaresPath . '/ApiMiddleware.php';
    if (!file_exists($apiMiddleware)) {
        file_put_contents($apiMiddleware, <<<PHP
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiMiddleware {
    public function handle(Request \$request, Closure \$next) {
        return \$next(\$request);
    }
}
PHP
        );
    }

} else {
    echo "Setting up Web project...\n";

    // Remove API-specific
    if (is_dir($apiControllersPath)) system("rm -rf " . escapeshellarg($apiControllersPath));

    // Create Web controllers
    if (!is_dir($webControllersPath)) mkdir($webControllersPath, 0755, true);
    createKeepFile($webControllersPath);

    // Views
    if (!is_dir($viewsPath)) mkdir($viewsPath, 0755, true);
    file_put_contents($viewsPath . '/welcome.blade.php', "<h1>Welcome to Web Project</h1>");

    // Routes
    file_put_contents($webRoutes, "<?php\nuse Illuminate\Support\Facades\Route;\nRoute::get('/', function() {\n    return view('welcome');\n});\n");
    file_put_contents($apiRoutes, "<?php\n// API routes disabled for Web project.\n");
}

// Artisan commands
echo "Generating key and migrating database...\n";
shell_exec("php " . escapeshellarg($laravel . "artisan key:generate"));
shell_exec("php " . escapeshellarg($laravel . "artisan migrate --force"));

// Front-end (npm)
if (file_exists($laravel . 'package.json')) {
    echo "Installing frontend dependencies...\n";
    shell_exec("cd " . escapeshellarg($laravel) . " && npm install && npm run build");
}

echo "Setup complete! 🚀\n";
