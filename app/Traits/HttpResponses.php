<?php

namespace App\Traits ;

use Illuminate\Http\JsonResponse;

trait HttpResponses {
    protected function success($data , $message = null , $code = 200): JsonResponse
    {
        return response()->json([
            'status'  => 1 ,
            'data' => $data ,
            'message' => $message ,
        ] , $code) ;
    }

    protected function error($data , $message = null , $code): JsonResponse
    {
        return response()->json([
            'status' => 0 ,
            'data' => $data ,
            'message' => $message ,
        ] , $code);
    }
}
