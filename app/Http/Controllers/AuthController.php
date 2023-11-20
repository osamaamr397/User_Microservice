<?php

namespace App\Http\Controllers;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    use GeneralTrait;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function login(Request $request)
    {

        try {
            $rules = [
                "email" => "required",
                "password" => "required"

            ];

            $credentials = $request->only(['email', 'password']);
            $token = Auth::guard('api')->attempt($credentials);
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            if (!$token)
                return $this->returnError('E001', 'data is not corrected');

            $admin = Auth::guard('api')->user();
            $admin->api_token = $token;
            //return token

              return response()->json([
                'user' => $admin,
                'message' => 'User logged'
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $admin->api_token
            ]);

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }


    }

    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if (User::where('email', $request->email)->exists()) {
            return $this->returnError(400,"Email Already exist");
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        $token = Auth::login($user);
        return $this->returnData('user', $user);

    }

}

