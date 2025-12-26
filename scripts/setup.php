<?php

/**
 * Laravel Starter Setup Script
 * Full Web/API setup for src/ structure with vendor inside src/
 */

$root = __DIR__ . '/../';
$laravel = $root . 'src/';

// Paths
$controllersPath = $laravel . 'app/Http/Controllers';
$apiControllersPath = $controllersPath . '/API';
$webControllersPath = $controllersPath . '/Web';
$viewsPath = $laravel . 'resources/views';
$apiRoutes = $laravel . 'routes/api.php';
$webRoutes = $laravel . 'routes/web.php';
$middlewaresPath = $laravel . 'app/Http/Middleware';
$databasePath = $laravel . 'database/database.sqlite';
$envPath = $laravel . '.env';
$envExample = $laravel . '.env.example';
$providerPath = $laravel . 'app/Providers/AppServiceProvider.php';
$bootstrapFile = $laravel . 'bootstrap/app.php';
$templatesPath = $root . 'templates/';

// Helper to create .keep files
function createKeepFile($path) {
    if (!file_exists($path . '/.keep')) file_put_contents($path . '/.keep', '');
}

// Step 1: Ask for project type
$handle = fopen("php://stdin", "r");
echo "Which type of project do you want? (web/api) [web]: ";
$type = trim(fgets($handle));
if ($type === '') $type = 'web';
fclose($handle);

echo "\nYou chose: $type\n";

// Step 2: Ensure .env exists
if (!file_exists($envPath)) {
    if (file_exists($envExample)) {
        copy($envExample, $envPath);
        echo ".env created from .env.example\n";
    } else {
        file_put_contents($envPath, "APP_NAME=Laravel\nAPP_ENV=local\nAPP_KEY=\nAPP_DEBUG=true\nAPP_URL=http://localhost\n");
        echo ".env created with defaults\n";
    }
}

// Step 3: Create SQLite file if needed
if (!file_exists($databasePath)) {
    if (!is_dir(dirname($databasePath))) mkdir(dirname($databasePath), 0755, true);
    touch($databasePath);
    echo "SQLite database created: $databasePath\n";
}

// Step 4: Copy bootstrap template
if ($type === 'api') {
    copy($templatesPath . 'bootstrap-app-api.php', $bootstrapFile);
    echo "Bootstrap file set for API project.\n";
} else {
    copy($templatesPath . 'bootstrap-app-web.php', $bootstrapFile);
    echo "Bootstrap file set for Web project.\n";
}

// Step 5: Setup Web or API
if ($type === 'api') {
    echo "Setting up API project...\n";

    // Remove Web-specific
    if (is_dir($webControllersPath)) system("rm -rf " . escapeshellarg($webControllersPath));
    if (is_dir($viewsPath)) system("rm -rf " . escapeshellarg($viewsPath));

    // Create API Controllers
    if (!is_dir($apiControllersPath)) mkdir($apiControllersPath, 0755, true);
    createKeepFile($apiControllersPath);

    // AuthController
    $authController = $apiControllersPath . '/AuthController.php';
    if (!file_exists($authController)) {
        file_put_contents($authController, <<<PHP
<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request \$request) {
        \$user = User::create([
            'name' => \$request->name,
            'email' => \$request->email,
            'password' => Hash::make(\$request->password),
        ]);
        \$token = \$user->createToken('api-token')->plainTextToken;
        return response()->json(['token' => \$token], 201);
    }

    public function login(Request \$request) {
        \$user = User::where('email', \$request->email)->first();
        if (!\$user || !Hash::check(\$request->password, \$user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        \$token = \$user->createToken('api-token')->plainTextToken;
        return response()->json(['token' => \$token]);
    }
}
PHP
        );
    }

    // Routes
    file_put_contents($apiRoutes, <<<PHP
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/ping', fn() => response()->json(['pong' => true]));
PHP
    );
    file_put_contents($webRoutes, "<?php\n// Web routes disabled for API project.\n");

    // Middleware
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

    // AppServiceProvider modification
    if (file_exists($providerPath)) {
        $providerContent = file_get_contents($providerPath);
        if (!str_contains($providerContent, 'pushMiddlewareToGroup')) {
            $insert = <<<PHP

        // Add API middleware dynamically
        \$router = \$this->app->make(\Illuminate\Routing\Router::class);
        \$router->pushMiddlewareToGroup('api', \App\Http\Middleware\ApiMiddleware::class);

PHP;
            $providerContent = preg_replace('/public function boot\(\)\s*\{/', "public function boot()\n    { $insert", $providerContent, 1);
            file_put_contents($providerPath, $providerContent);
            echo "AppServiceProvider updated to register ApiMiddleware\n";
        }
    }

    // Install Sanctum
    echo "Installing Laravel Sanctum...\n";
    shell_exec("composer require laravel/sanctum");
    shell_exec("php " . escapeshellarg($laravel . "artisan") . " vendor:publish --provider=\"Laravel\\Sanctum\\SanctumServiceProvider\"");
} else {
    echo "Setting up Web project...\n";

    // Remove API-specific
    if (is_dir($apiControllersPath)) system("rm -rf " . escapeshellarg($apiControllersPath));

    // Web Controllers
    if (!is_dir($webControllersPath)) mkdir($webControllersPath, 0755, true);
    createKeepFile($webControllersPath);

    // Views
    if (!is_dir($viewsPath)) mkdir($viewsPath, 0755, true);
    file_put_contents($viewsPath . '/welcome.blade.php', "<h1>Welcome to Web Project</h1>");

    // Routes
    file_put_contents($webRoutes, "<?php\nuse Illuminate\Support\Facades\Route;\nRoute::get('/', fn() => view('welcome'));\n");
    file_put_contents($apiRoutes, "<?php\n// API routes disabled for Web project.\n");
}

// Step 6: Artisan commands
echo "Generating key and migrating database...\n";
$artisan = escapeshellarg($laravel . 'artisan');
shell_exec("php $artisan key:generate");
shell_exec("php $artisan migrate --force");

// Step 7: Frontend (npm/Vite)
if (file_exists($laravel . 'package.json')) {
    echo "Installing frontend dependencies...\n";
    shell_exec("cd " . escapeshellarg($laravel) . " && npm install && npm run build");
}

echo "\n Setup complete! Your Laravel project is ready in src (M.F)/\n";
