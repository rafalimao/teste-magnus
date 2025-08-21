<?php

require_once 'config/Database.php';
require_once 'config/Redis.php';
require_once 'controllers/VehicleController.php';
require_once 'controllers/AuthController.php';
require_once 'middleware/AuthMiddleware.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$route = $_GET['route'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

$authController = new AuthController();
$vehicleController = new VehicleController();
$authMiddleware = new AuthMiddleware();

try {
    switch ($route) {
        case 'auth/login':
            if ($method === 'POST') {
                $authController->login();
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        case 'vehicles/load-initial':
            if ($method === 'POST') {
                $authMiddleware->authenticate();
                $vehicleController->loadInitialData();
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        case 'vehicles/brands':
            if ($method === 'GET') {
                $authMiddleware->authenticate();
                $vehicleController->getBrands();
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        case 'vehicles/models':
            if ($method === 'GET') {
                $authMiddleware->authenticate();
                $vehicleController->getModelsByBrand();
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        case 'vehicles/update':
            if ($method === 'PUT') {
                $authMiddleware->authenticate();
                $vehicleController->updateVehicle();
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Route not found']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

