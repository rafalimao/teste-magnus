<?php

require_once 'models/Vehicle.php';
require_once 'interfaces/VehicleDataProviderInterface.php';

class VehicleController {
    private $db;
    private $redis;
    private $vehicle;
    private $vehicleDataProvider;

    public function __construct(VehicleDataProviderInterface $vehicleDataProvider = null) {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->redis = new RedisConfig();
        $this->vehicle = new Vehicle($this->db);
        
        // Injeção de dependência - se não fornecido, usa FipeService como padrão
        if ($vehicleDataProvider === null) {
            require_once 'services/FipeService.php';
            $this->vehicleDataProvider = new FipeService();
        } else {
            $this->vehicleDataProvider = $vehicleDataProvider;
        }
    }

    public function loadInitialData() {
        try {
            // Buscar marcas usando a interface
            $brands = $this->vehicleDataProvider->getBrands();
            
            if (empty($brands)) {
                http_response_code(400);
                echo json_encode(['error' => 'No brands found']);
                return;
            }

            // Enviar marcas para a fila Redis
            foreach ($brands as $brand) {
                $this->redis->lpush('brands_queue', $brand);
            }

            echo json_encode([
                'message' => 'Initial data load started',
                'brands_count' => count($brands)
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getBrands() {
        try {
            // Verificar cache primeiro
            $cacheKey = 'brands_list';
            $cachedBrands = $this->redis->get($cacheKey);
            
            if ($cachedBrands) {
                echo json_encode($cachedBrands);
                return;
            }

            // Buscar no banco de dados
            $brands = $this->vehicle->getBrands();
            
            // Armazenar no cache por 1 hora
            $this->redis->set($cacheKey, $brands, 3600);
            
            echo json_encode($brands);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getModelsByBrand() {
        try {
            $brand = $_GET['brand'] ?? '';
            
            if (empty($brand)) {
                http_response_code(400);
                echo json_encode(['error' => 'Brand parameter is required']);
                return;
            }

            // Verificar cache primeiro
            $cacheKey = 'models_' . md5($brand);
            $cachedModels = $this->redis->get($cacheKey);
            
            if ($cachedModels) {
                echo json_encode($cachedModels);
                return;
            }

            // Buscar no banco de dados
            $models = $this->vehicle->getModelsByBrand($brand);
            
            // Armazenar no cache por 1 hora
            $this->redis->set($cacheKey, $models, 3600);
            
            echo json_encode($models);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updateVehicle() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!isset($data['id']) || !isset($data['model'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID and model are required']);
                return;
            }

            $this->vehicle->id = $data['id'];
            $this->vehicle->model = $data['model'];
            $this->vehicle->observations = $data['observations'] ?? '';

            if ($this->vehicle->update()) {
                // Limpar cache relacionado
                $this->clearVehicleCache();
                
                echo json_encode(['message' => 'Vehicle updated successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update vehicle']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function clearVehicleCache() {
        // Implementar lógica para limpar cache relacionado aos veículos
        // Por simplicidade, vamos limpar alguns caches conhecidos
        $this->redis->delete('brands_list');
    }

    /**
     * Método para definir o provedor de dados (útil para testes)
     */
    public function setVehicleDataProvider(VehicleDataProviderInterface $provider) {
        $this->vehicleDataProvider = $provider;
    }
}
