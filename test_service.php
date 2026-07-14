<?php

if ($argc < 2) {
    echo "Usage: php test_service.php <path/to/image>\n";
    exit(1);
}

$filename = $argv[1];
$fullPath = realpath($filename);

if ($fullPath === false || !file_exists($fullPath)) {
    echo "The file $filename does not exist\n";
    exit(1);
}

echo "Barcode file: $fullPath \n";

$serviceUrl = "http://127.0.0.1:8080";
$url = $serviceUrl . "/decode?file=" . urlencode($fullPath);

$context = stream_context_create([
    "http" => [
        "timeout" => 60,
    ],
]);

$time = microtime(true);
$response = @file_get_contents($url, false, $context);
echo "Time: " . (microtime(true) - $time) . "s\n";

if ($response === false) {
    echo "Failed to call barcode service. Is the service running on $serviceUrl?\n";
    exit(1);
}

$resultArray = json_decode($response, true);
if (!is_array($resultArray)) {
    echo "Invalid response from service: $response\n";
    exit(1);
}

if (isset($resultArray["error"])) {
    echo "Service error: " . $resultArray["error"] . "\n";
    exit(1);
}

$resultCount = count($resultArray);
echo "Total count: $resultCount\n";
for ($i = 0; $i < $resultCount; $i++) {
    $result = $resultArray[$i];
    echo "Barcode format: $result[0], ";
    echo "value: $result[1], ";
    echo "raw: $result[2]\n";
    echo "Localization : $result[3]\n";
}
