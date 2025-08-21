<?php

require_once '../api-1/interfaces/VehicleDataProviderInterface.php';

/**
 * Mock implementation para testes
 */
class MockVehicleDataProvider implements VehicleDataProviderInterface {
    private $mockBrands = [
        ['codigo' => '1', 'nome' => 'Toyota'],
        ['codigo' => '2', 'nome' => 'Honda'],
        ['codigo' => '3', 'nome' => 'Ford']
    ];

    private $mockModels = [
        ['codigo' => '001', 'nome' => 'Corolla'],
        ['codigo' => '002', 'nome' => 'Camry'],
        ['codigo' => '003', 'nome' => 'Prius']
    ];

    private $mockYears = [
        ['codigo' => '2020-1', 'nome' => '2020 Gasolina'],
        ['codigo' => '2021-1', 'nome' => '2021 Gasolina'],
        ['codigo' => '2022-1', 'nome' => '2022 Gasolina']
    ];

    public function getBrands($vehicleType = 'carros') {
        return $this->mockBrands;
    }

    public function getModels($vehicleType = 'carros', $brandCode) {
        return $this->mockModels;
    }

    public function getYears($vehicleType = 'carros', $brandCode, $modelCode) {
        return $this->mockYears;
    }
}
