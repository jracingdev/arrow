<?php

namespace App\Http\Controllers;


class FirebaseController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function config()
    {
        $data = array(
            'apiKey' => base64_encode(config('firebase.api_key')),
            'authDomain' => base64_encode(config('firebase.auth_domain')),
            'databaseURL' => base64_encode(config('firebase.database_url')),
            'projectId' => base64_encode(config('firebase.project_id')),
            'storageBucket' => base64_encode(config('firebase.storage_bucket')),
            'messagingSenderId' => base64_encode(config('firebase.messaging_sender_id')),
            'appId' => base64_encode(config('firebase.app_id')),
            'measurementId' => base64_encode(config('firebase.measurement_id')),
        );

        return response()->json($data);
    }

}
