<?php

require_once 'controllers/AuthController.php';

class AuthMiddleware {
    private $authController;

    public function __construct() {
        $this->authController = new AuthController();
    }

    public function authenticate() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (empty($authHeader)) {
            http_response_code(401);
            echo json_encode(['error' => 'Authorization header missing']);
            exit();
        }

        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid authorization header format']);
            exit();
        }

        $token = $matches[1];
        $payload = $this->authController->validateJWT($token);

        if (!$payload) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid or expired token']);
            exit();
        }

        // Token válido, continuar com a requisição
        return $payload;
    }
}

