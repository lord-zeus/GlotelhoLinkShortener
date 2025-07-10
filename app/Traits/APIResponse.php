<?php
namespace App\Traits;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

trait APIResponse {
    /**
     * @param $data
     * @param int $code
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     *
     */
    public function successResponse($data, $code = Response::HTTP_OK){
        return response(['data' => $data, 'code' => $code])->header('Content-Type', 'application/json');
    }

    /**
     * @param $message
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function errorResponse($message, $code){
        DB::rollBack();

        return response()->json(['message' => $message, 'code' => $code], $code);
    }

    /**
     * @param $message
     * @param $code
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     *
     */
    public function errorMessage($message, $code){
        return response($message, $code)->header('Content-Type', 'application/json');
    }


}
