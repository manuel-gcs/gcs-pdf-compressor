<?php

require __DIR__ . '/vendor/autoload.php';

use Mostafaznv\PdfOptimizer\Optimizer;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    readfile(__DIR__ . '/index.html');
    exit;
}

// Apenas aceitar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header("Content-Type: application/json");
    echo json_encode(["error" => "Use POST and send a file named 'pdf'"]);
    exit;
}

if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    header("Content-Type: application/json");
    echo json_encode(["error" => "You must upload a PDF in the 'pdf' field"]);
    exit;
}

$uploadedFile = $_FILES['pdf']['tmp_name'];

$outputDir = __DIR__ . '/output';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

$outputFile = $outputDir . '/' . uniqid('optimized_') . '.pdf';

try {
    $optimizer = new Optimizer();
    $optimizer->setOutputDirectory($outputDir)
              ->setOptimizationLevel(2)
              ->optimize($uploadedFile);

    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=\"optimized.pdf\"");
    readfile($outputFile);

    unlink($outputFile);

} catch (Exception $e) {
    http_response_code(500);
    header("Content-Type: application/json");
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}