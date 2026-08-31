<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class BusinessRuleException extends Exception
{
    public function render($request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'error' => 'BUSINESS_RULE_VIOLATION',
        ], 422);
    }
}
