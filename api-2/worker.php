<?php

require_once 'workers/BrandProcessor.php';

echo "Starting FIPE API-2 Worker...\n";

$processor = new BrandProcessor();
$processor->processBrands();

