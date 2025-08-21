<?php

interface VehicleDataProviderInterface {
    /**
     * Busca todas as marcas de veículos
     * @param string $vehicleType Tipo de veículo (carros, motos, caminhoes)
     * @return array Lista de marcas
     */
    public function getBrands($vehicleType = 'carros');

    /**
     * Busca modelos de uma marca específica
     * @param string $vehicleType Tipo de veículo
     * @param string $brandCode Código da marca
     * @return array Lista de modelos
     */
    public function getModels($vehicleType = 'carros', $brandCode);

    /**
     * Busca anos disponíveis para um modelo
     * @param string $vehicleType Tipo de veículo
     * @param string $brandCode Código da marca
     * @param string $modelCode Código do modelo
     * @return array Lista de anos
     */
    public function getYears($vehicleType = 'carros', $brandCode, $modelCode);
}