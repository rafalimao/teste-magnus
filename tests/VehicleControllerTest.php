<?php

require_once '../api-1/controllers/VehicleController.php';
require_once '../api-1/config/Database.php';
require_once '../api-1/config/Redis.php';

class VehicleControllerTest {
    private $vehicleController;
    
    public function __construct() {
        $this->vehicleController = new VehicleController();
    }

    public function testGetBrands() {
        echo "Testing getBrands method...\n";
        
        // Simular requisição GET
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        ob_start();
        $this->vehicleController->getBrands();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        
        if (is_array($response)) {
            echo "✓ getBrands test passed\n";
            return true;
        } else {
            echo "✗ getBrands test failed\n";
            return false;
        }
    }

    public function testGetModelsByBrand() {
        echo "Testing getModelsByBrand method...\n";
        
        // Simular requisição GET com parâmetro
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['brand'] = 'Toyota';
        
        ob_start();
        $this->vehicleController->getModelsByBrand();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        
        if (is_array($response)) {
            echo "✓ getModelsByBrand test passed\n";
            return true;
        } else {
            echo "✗ getModelsByBrand test failed\n";
            return false;
        }
    }

    public function runAllTests() {
        echo "Running VehicleController tests...\n\n";
        
        $tests = [
            'testGetBrands',
            'testGetModelsByBrand'
        ];
        
        $passed = 0;
        $total = count($tests);
        
        foreach ($tests as $test) {
            if ($this->$test()) {
                $passed++;
            }
        }
        
        echo "\nTest Results: {$passed}/{$total} tests passed\n";
        return $passed === $total;
    }
}

// Executar testes se chamado diretamente
if (php_sapi_name() === 'cli') {
    $test = new VehicleControllerTest();
    $test->runAllTests();
}

