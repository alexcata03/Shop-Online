<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\UserController;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Controllers/HomeController.php';
require __DIR__ . '/../src/Controllers/ProductController.php';
require __DIR__ . '/../src/Controllers/UserController.php'; // Include UserController
$dbConfig = require __DIR__ . '/../config/db_config.php';

$hostname = $dbConfig['host'];
$username = $dbConfig['username'];
$password = $dbConfig['password'];
$dbname = $dbConfig['dbname'];

try {
    $dsn = "mysql:host={$hostname};dbname={$dbname}";
    $db = new PDO($dsn, $username, $password);

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $app = AppFactory::create();
    $twig = Twig::create('../templates', ['cache' => false]);

    $app->get('/', [HomeController::class, 'index']);

    $productController = new ProductController($db, $twig);
    $userController = new UserController($db, $twig); // Instantiate UserController

    $app->get('/products', [$productController, 'getAll']);
    $app->get('/filtered-products', [$productController, 'getByCategory']);
    $app->post('/products', [$productController, 'create']);
    $app->put('/products/{productId}', [$productController, 'update']);
    $app->delete('/products/{productId}', [$productController, 'delete']);

    // User routes
    $app->post('/login', [$userController, 'login']); // Route for user login
    $app->post('/logout', [$userController, 'logout']); // Route for user logout
    $app->get('/users', [$userController, 'getAll']); // Route to get all users
    $app->post('/users', [$userController, 'createUser']); // Route to create a new user
    $app->put('/users/{username}', [$userController, 'updateUser']); // Route to update an existing user
    $app->delete('/users/{username}', [$userController, 'deleteUser']); // Route to delete an existing user

    $app->add(TwigMiddleware::create($app, $twig));

    $app->run();
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>