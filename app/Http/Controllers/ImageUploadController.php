<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

class ImageUploadController extends Controller
{
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
      return response()->json([
       'message'   => 'Successfully uploaded ' . $image->getClientOriginalName() . '.'
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
