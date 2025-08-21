<?php

class AuthController {
    private $secretKey = 'your-secret-key-here';

    public function login() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';

            // Validação simples (em produção, usar hash de senha e banco de dados)
            if ($username === 'admin' && $password === 'password') {
                $token = $this->generateJWT([
                    'user_id' => 1,
                    'username' => $username,
                    'exp' => time() + (24 * 60 * 60) // 24 horas
                ]);

                echo json_encode([
                    'message' => 'Login successful',
                    'token' => $token
                ]);
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid credentials']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function generateJWT($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode($payload);
        
        $headerEncoded = $this->base64UrlEncode($header);
        $payloadEncoded = $this->base64UrlEncode($payload);
        
        $signature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, $this->secretKey, true);
        $signatureEncoded = $this->base64UrlEncode($signature);
        
        return $headerEncoded . "." . $payloadEncoded . "." . $signatureEncoded;
    }

    public function validateJWT($token) {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return false;
        }

        $header = $this->base64UrlDecode($parts[0]);
        $payload = $this->base64UrlDecode($parts[1]);
        $signature = $this->base64UrlDecode($parts[2]);

        $expectedSignature = hash_hmac('sha256', $parts[0] . "." . $parts[1], $this->secretKey, true);

        if (!hash_equals($signature, $expectedSignature)) {
            return false;
        }

        $payloadData = json_decode($payload, true);
        
        if ($payloadData['exp'] < time()) {
            return false;
        }

        return $payloadData;
    }

    private function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}

