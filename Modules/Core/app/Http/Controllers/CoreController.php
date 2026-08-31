<?php

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;


class CoreController extends Controller
{
    /**
     * Return success response
     *
     * @param string $message
     * @param null $data
     * @return JsonResponse
     */
    public function returnSuccess(string $message = 'Success', $data = null): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], 200);
    }


    /**
     * Return error response
     *
     * @param string $message
     * @param null $data
     * @return JsonResponse
     */
    public function returnError(string $message = 'Error', $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data
        ], 400);
    }
}
