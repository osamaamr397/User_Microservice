<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;


class UserController extends Controller
{
    use GeneralTrait;


    public function getUser(Request $request)
    {

    }


    public function store(Request $request)
    {
        $header = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $header);


        try {

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->post('http://localhost:8001/api/notes', [
                'title' => $request->input('title'),
                'cont' => $request->input('cont'),
            ]);

            if ($response->successful()) {
                return $this->returnData(201, $response->body());
            } else {
                // Handle the error
                echo $token;
                return $this->returnError('10', 'Failed to create post in Microservice B');
            }
        } catch (\Exception $e) {
            // Handle errors
            return $this->returnError("401", 'API request failed: ' . $e->getMessage());
        }
    }


}

