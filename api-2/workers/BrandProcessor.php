<?php

require_once 'config/Database.php';
require_once 'config/Redis.php';
require_once 'models/Vehicle.php';
require_once 'services/FipeService.php';

class BrandProcessor {
    private $db;
    private $redis;
    private $vehicle;
    private $fipeService;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->redis = new RedisConfig();
        $this->vehicle = new Vehicle($this->db);
        $this->fipeService = new FipeService();
    }

    public function processBrands() {
        echo "Starting brand processing...\n";
        
        while (true) {
            try {
                // Buscar marca da fila
                $brand = $this->redis->rpop('brands_queue');
                
                if (!$brand) {
                    echo "No brands in queue, waiting...\n";
                    sleep(5);
                    continue;
                }

                echo "Processing brand: " . $brand['nome'] . " (Code: " . $brand['codigo'] . ")\n";
                
                // Buscar modelos da marca na API FIPE
                $models = $this->fipeService->getModels('carros', $brand['codigo']);
                
                if (empty($models)) {
                    echo "No models found for brand: " . $brand['nome'] . "\n";
                    continue;
                }

                $savedCount = 0;
                
                // Salvar cada modelo no banco de dados
                foreach ($models as $model) {
                    // Verificar se já existe
                    if (!$this->vehicle->exists($model['codigo'], $brand['nome'], $model['nome'])) {
                        $this->vehicle->code = $model['codigo'];
                        $this->vehicle->brand = $brand['nome'];
                        $this->vehicle->model = $model['nome'];
                        $this->vehicle->observations = '';

                        if ($this->vehicle->create()) {
                            $savedCount++;
                        } else {
                            echo "Failed to save model: " . $model['nome'] . "\n";
                        }
                    }
                }

                echo "Saved {$savedCount} models for brand: " . $brand['nome'] . "\n";
                
                // Pequena pausa para não sobrecarregar a API
                sleep(1);

            } catch (Exception $e) {
                echo "Error processing brand: " . $e->getMessage() . "\n";
                sleep(5);
            }
        }
    }
}

// Executar o processador se chamado diretamente
if (php_sapi_name() === 'cli') {
    $processor = new BrandProcessor();
    $processor->processBrands();
}

