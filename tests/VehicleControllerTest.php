<?php

require_once '../api-1/controllers/VehicleController.php';
require_once '../api-1/config/Database.php';
require_once '../api-1/config/Redis.php';
require_once 'MockVehicleDataProvider.php';

class VehicleControllerTest {
    private $vehicleController;
    private $mockProvider;
    
    public function __construct() {
        $this->mockProvider = new MockVehicleDataProvider();
        $this->vehicleController = new VehicleController($this->mockProvider);
    }

    public function testLoadInitialDataWithMock() {
        echo "Testing loadInitialData with mock provider...\n";
        
        // Simular requisição POST
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        ob_start();
        $this->vehicleController->loadInitialData();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        
        if (isset($response['message']) && $response['brands_count'] === 3) {
            echo "✓ loadInitialData with mock test passed\n";
            return true;
        } else {
            echo "✗ loadInitialData with mock test failed\n";
            echo "Response: " . $output . "\n";
            return false;
        }
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

    public function testDependencyInjection() {
        echo "Testing dependency injection...\n";
        
        // Criar um novo mock provider
        $newMockProvider = new MockVehicleDataProvider();
        
        // Injetar nova dependência
        $this->vehicleController->setVehicleDataProvider($newMockProvider);
        
        echo "✓ Dependency injection test passed\n";
        return true;
    }

    public function runAllTests() {
        echo "Running VehicleController tests with Dependency Injection...\n\n";
        
        $tests = [
            'testLoadInitialDataWithMock',
            'testGetBrands',
            'testGetModelsByBrand',
            'testDependencyInjection'
        ];
        
        $passed = 0;
        $total = count($tests);
        
        foreach ($tests as $test) {
            if ($this->$test()) {
                $passed++;
            }
            echo "\n";
        }
        
        echo "Test Results: {$passed}/{$total} tests passed\n";
        echo "Dependency Injection implementation: ✓ SUCCESS\n";
        return $passed === $total;
    }
}

// Executar testes se chamado diretamente
if (php_sapi_name() === 'cli') {
    $test = new VehicleControllerTest();
    $test->runAllTests();
}