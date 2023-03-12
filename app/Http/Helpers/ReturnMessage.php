<?php

namespace App\Http\Helpers;

class ReturnMessage {

    public function getReturn($data = [], $status = 200, $success = true)
    {
        return response()->json(['success' => $success, 'dados' => $data, 'error' => false], $status);
    }

    public function getErrorReturn($error, $status = 500)
    {
        return response()->json(['success' => false, 'dados' => false, 'error' => $error], $status);
    }

}



?>