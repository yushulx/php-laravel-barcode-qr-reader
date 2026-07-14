<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

class ImageUploadController extends Controller
{
    /**
     * Decode barcodes from an image file by calling the external
     * Dynamsoft Barcode Reader service.
     *
     * @param string $filePath Absolute path to the image file.
     * @return array|null List of barcode results or null on failure.
     */
    private function decodeBarcodeFile($filePath)
    {
        $serviceUrl = env('BARCODE_SERVICE_URL', 'http://127.0.0.1:8080');
        $url = rtrim($serviceUrl, '/') . '/decode?file=' . urlencode($filePath);

        $context = stream_context_create([
            'http' => [
                'timeout' => 60,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            return null;
        }

        $result = json_decode($response, true);
        if (!is_array($result)) {
            return null;
        }

        return $result;
    }

    function page()
    {
        return view('barcode_qr_reader');
    }

    function upload(Request $request)
    {
        $maxSizeKilobytes = env('BARCODE_IMAGE_MAX_SIZE_KB', 20480);

        $validation = Validator::make($request->all(), [
            'BarcodeQrImage' => 'required|file|max:' . $maxSizeKilobytes
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()->all()
            ], 422);
        }

        $image = $request->file('BarcodeQrImage');

        if (!$image->isValid()) {
            return response()->json([
                'success' => false,
                'message' => ['Upload failed: ' . $image->getErrorMessage()]
            ], 422);
        }

        $filename = $image->getClientOriginalName();
        $destinationPath = public_path('images');
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }
        $image->move($destinationPath, $filename);
        $filePath = $destinationPath . DIRECTORY_SEPARATOR . $filename;

        $resultArray = $this->decodeBarcodeFile($filePath);

        if ($resultArray === null) {
            return response()->json([
                'success' => false,
                'message' => ['Failed to connect to barcode service. Is it running?']
            ], 503);
        }

        $results = [];
        if (is_array($resultArray)) {
            foreach ($resultArray as $result) {
                $results[] = [
                    'format' => $result[0] ?? '',
                    'text' => $result[1] ?? '',
                    'raw' => $result[2] ?? '',
                    'localization' => $result[3] ?? ''
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully decoded the image.',
            'results' => $results
        ]);
    }
}
