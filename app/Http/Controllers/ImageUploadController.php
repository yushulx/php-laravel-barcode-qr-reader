<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

class ImageUploadController extends Controller
{
    function __construct() {
        DBRInitLicense("DLS2eyJoYW5kc2hha2VDb2RlIjoiMjAwMDAxLTE2NDk4Mjk3OTI2MzUiLCJvcmdhbml6YXRpb25JRCI6IjIwMDAwMSIsInNlc3Npb25QYXNzd29yZCI6IndTcGR6Vm05WDJrcEQ5YUoifQ==");
        DBRInitRuntimeSettingsWithString("{\"ImageParameter\":{\"Name\":\"BestCoverage\",\"DeblurLevel\":9,\"ExpectedBarcodesCount\":512,\"ScaleDownThreshold\":100000,\"LocalizationModes\":[{\"Mode\":\"LM_CONNECTED_BLOCKS\"},{\"Mode\":\"LM_SCAN_DIRECTLY\"},{\"Mode\":\"LM_STATISTICS\"},{\"Mode\":\"LM_LINES\"},{\"Mode\":\"LM_STATISTICS_MARKS\"}],\"GrayscaleTransformationModes\":[{\"Mode\":\"GTM_ORIGINAL\"},{\"Mode\":\"GTM_INVERTED\"}]}}");		
    }

    function page()
    {
     return view('barcode_qr_reader');
    }

    function upload(Request $request)
    {
     $validation = Validator::make($request->all(), [
      'BarcodeQrImage' => 'required'
     ]);
     if($validation->passes())
     {
      $image = $request->file('BarcodeQrImage');
      $image->move(public_path('images'), $image->getClientOriginalName());

      $resultArray = DecodeBarcodeFile(public_path('images/' . $image->getClientOriginalName()), 0x3FF | 0x2000000 | 0x4000000 | 0x8000000 | 0x10000000); // 1D, PDF417, QRCODE, DataMatrix, Aztec Code

      if (is_array($resultArray)) {
        $resultCount = count($resultArray);
        echo "Total count: $resultCount", "\n";
        if ($resultCount > 0) {
            for ($i = 0; $i < $resultCount; $i++) {
                $result = $resultArray[$i];
                echo "Barcode format: $result[0], ";
                echo "value: $result[1], ";
                echo "raw: ", bin2hex($result[2]), "\n";
                echo "Localization : ", $result[3], "\n";
            }
        }
        else {
            echo 'No barcode found.', "\n";
        }
      } 

      return response()->json([
       'message'   => 'Successfully uploaded the image.'
      ]);
     }
     else
     {
      return response()->json([
       'message'   => $validation->errors()->all()
      ]);
     }
    }
}
