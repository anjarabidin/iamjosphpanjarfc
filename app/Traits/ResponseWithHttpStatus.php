<?php

namespace App\Traits;

trait ResponseWithHttpStatus
{
    public function getSuccessResponse($data = [])
    {
        $data = [
            'code' => 200,
            'message' => 'Successfully Get Data',
            'data' => $data,
        ];
        return response()->json($data, 200);
    }

    public function postSuccessResponse($message = '', $data = [])
    {
        $data = [
            'success' => true,
            'code' => 200,
            'message' => $message ?? 'Successfully Post Data',
            'data' => $data,
        ];
        return response()->json($data, 200);
    }

    public function failedResponse($message = 'Failed Process Data', $data = null, $code = 422)
    {
        if ($data != null) {
            $data = [
                'code' => $code,
                'message' => $message,
                'data' => $data,
            ];
        } else {
            $data = [
                'code' => $code,
                'message' => $message,
            ];
        }
        return response()->json($data, $code);
    }

    public function createdResponse($message = '', $data = [])
    {
        $data = [
            'code' => 201,
            'message' => $message ?? 'Successfully Created Data',
            'data' => $data,
        ];
        return response()->json($data, 201);
    }

    public function alreadyExistResponse($message = '')
    {
        $data = [
            'code' => 409,
            'status' => false,
            'message' => $message ?? 'Data Already Exist',
        ];
        return response()->json($data, 409);
    }

    public function badRequestResponse($message = '')
    {
        $data = [
            'code' => 400,
            'status' => false,
            'message' => $message ?? 'Bad Request',
        ];
        return response()->json($data, 400);
    }

    public function notFoundResponse($message = '')
    {
        $data = [
            'code' => 404,
            'status' => false,
            'message' => $message ?? 'Data Not Found',
        ];
        return response()->json($data, 404);
    }

    public function unauthorizedResponse($message = '')
    {
        $data = [
            'code' => 401,
            'status' => false,
            'message' => $message ?? 'Unauthorized',
        ];
        return response()->json($data, 401);
    }

    protected function errorValidationResponse($validator)
    {
        return response()->json([
            'code'  => 422,
            'success' => false,
            'data' => null,
            'error' => $validator->errors()->first()
        ], 422);
    }
}
