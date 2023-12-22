<?php

namespace App\Controllers;



class ErrorController
{


    // 404 =================
    public static function notFound($message = 'Resource Not Found')
    {
        http_response_code(404);
        loadView('error', [
            'status' => '404',
            'message' => $message
        ]);
    }

    // 403
    public static function unauthorized($message = 'You are not authorized to view this')
    {
        http_response_code(403);
        loadView('error', [
            'status' => '403',
            'message' => $message
        ]);
    }
}
