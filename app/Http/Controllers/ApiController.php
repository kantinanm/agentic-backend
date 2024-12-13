<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; 
use Validator;
use Illuminate\Support\Carbon;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class ApiController extends Controller
{
    //
    public $successStatus = 200;
    public function login(Request $request){ 
        
        //Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        //Passport::personalAccessTokensExpireIn(now()->addMinutes(1));
        
        
        $request->validate([
            "email" => "required|string|email",
            "password" => "required|string"
          ]);
        $credentials = request(["email", "password"]);
        
        if(!Auth::attempt($credentials))
            return response()->json([
            "message" => "Unauthorized"
            ],401);
     
            //$user = $request->user();
            $user = Auth::user();
            // $tokenResult = $user->createToken("Personal Access Token", ['user_profile','email-notify','list-acceptance']);
            // $token = $tokenResult->token;


            //return response()->json(["API_SCOPES" => Config::get('app.api_scopes')], 200);
            //return response()->json(["API_SCOPES" => env('PASSPORT_PASSWORD_SECRET')], 200);
            $tokenResult = $user->createToken('Personal Access Token', explode(',', Config::get('app.api_scopes')));
            $token = $tokenResult->token; 


            //$token->expires_at = Carbon::now()->addYear(); //Carbon::now()->addWeeks(1);
            $token->expires_at = Carbon::now()->addWeeks(1); // token will expire in 1 week ใน postman จะหมดอายุวันที่ 18 ธันวาคม 
            $token->save();

            return response()->json([
                "access_token" => $tokenResult->accessToken,
                "token_type" => "Bearer",
                "expires_at" => Carbon::parse(
                    $tokenResult->token->expires_at
                 )->toDateTimeString()
             ]);

    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
          "message" => "Successfully logged out"]);
    }

        /**
     * Logout
     */
    // public function logout(): JsonResponse
    // {
    //     Auth::user()->tokens()->delete();

    //     return response()->json([
    //         'success' => true,
    //         'statusCode' => 204,
    //         'message' => 'Logged out successfully.',
    //     ], 204);
    // }

    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);
        if ($validator->fails()) { 
                    return response()->json(['error'=>$validator->errors()], 401);            
            }
            
                $input = $request->all(); 
                $input['password'] = bcrypt($input['password']); 
                $user = User::create($input); 
                $success['token'] =  $user->createToken('Personal Access Token')-> accessToken; 
                $success['name'] =  $user->name;

                // $response = Http::post(env('APP_URL') . '/oauth/token', [
                //     'grant_type' => 'password',
                //     'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
                //     'client_secret' => env('PASSPORT_PASSWORD_SECRET'),
                //     'username' => $userData['email'],
                //     'password' => $userData['password'],
                //     'scope' => '',
                // ]);

        return response()->json(['success'=>$success], $this-> successStatus); 



    }

      /**
     * Login user
     *
     * @param  LoginRequest  $request
     */
    public function me(): JsonResponse
    {

        $user = auth()->user();

        return response()->json([
            'success' => true,
            'statusCode' => 200,
            'message' => 'Authenticated use info.',
            'data' => $user,
        ], 200);
    }

    public function emailNotify(Request $request)
    {
        // Implement email notification logic here
        $success['status_message'] =  'Email notification sent';
        $success['status'] =  'OK';
        return response()->json(['result'=>$success],$this-> successStatus);
    }

    public function listAcceptance(Request $request)
    {
        // Implement list of accepted papers logic here
        return response()->json(['accepted_papers' => []],$this-> successStatus);
    }
}
