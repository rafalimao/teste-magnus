<?php

require_once 'interfaces/VehicleDataProviderInterface.php';

class FipeService implements VehicleDataProviderInterface {
    private $baseUrl = 'https://parallelum.com.br/fipe/api/v1';

    public function getBrands($vehicleType = 'carros') {
        $url = $this->baseUrl . '/' . $vehicleType . '/marcas';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'FIPE-API-Consumer/1.0');
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Error fetching brands from FIPE API");
        }

        return json_decode($response, true);
    }

    public function getModels($vehicleType = 'carros', $brandCode) {
        $url = $this->baseUrl . '/' . $vehicleType . '/marcas/' . $brandCode . '/modelos';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'FIPE-API-Consumer/1.0');
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Error fetching models from FIPE API for brand: " . $brandCode);
        }

        $data = json_decode($response, true);
        return $data['modelos'] ?? [];
    }

    public function getYears($vehicleType = 'carros', $brandCode, $modelCode) {
        $url = $this->baseUrl . '/' . $vehicleType . '/marcas/' . $brandCode . '/modelos/' . $modelCode . '/anos';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'FIPE-API-Consumer/1.0');
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Error fetching years from FIPE API");
        }

        return json_decode($response, true);
    }
}