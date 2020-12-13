<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class AuthenticationController extends Controller
{
    public function authenticate(Request $request) {
        $rules = array(
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/'
            ]
        );

        $validator = \Validator::make($request->all(),$rules);
        if($validator->fails()) {
            return response()->json($validator->messages(), 400);
        } else {
            if(User::where('email', $request->get('email'))->exists()) {
                $user = User::where('email', $request->get('email'))->first();
                $auth = Hash::check($request->get('password'), $user->password);
                if($user && $auth){
                    $user->rollApiKey();
                    return response()->json(array(
                        'token' => $user->token,
                        'message' => 'Authorization Successful!',
                    ), 200);
                }
            }
            return response(array(
                'message' => 'Unauthorized, Please check your credentials.',
            ), 401);
        }
    }

    public function register(Request $request) {
        $rules = array(
            'name'  => 'required|string',
            'email' => 'required|email|unique:users',
            'phone' => 'required|size:10|unique:users',
            'twitter_username' => 'required|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/'
            ],
            'password_confirm' => 'required|same:password'
        );

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
           return response()->json($validator->messages(),400);
        } else {
            $user = new User();
            $user->fill($request->all());
            $user->password = Hash::make($request->password);
            $user->save();
            $this->sendGeneratedCodeForValidate($user->id);
            return response()->json(['message'=> 'User Created'],200);
        }
    }

    public function validateUserEmail(Request $request) {
        $rules = array(
            'email' => 'required|email',
            'code'  => 'required|string'
        );
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->messages(),400);
        } else {
            $user = User::where('email',$request->email)->first();
            $user->is_email_validated = true;
            $user->update();
            return response()->json('User Email Validated',200);
        }
    }

    public function validateUserPhone(Request $request) {
        $rules = array(
            'phone' => 'required|size:10',
            'code'  => 'required|string'
        );
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->messages(),400);
        } else {
            $user = User::where('phone',$request->phone)->first();
            $user->is_phone_validated = true;
            $user->update();
            return response()->json('User Phone Validated',200);
        }
    }

    // TODO: Bu generator u bi helper a filan mı alsam ? Bu kodu db ye kaydetsem mi sonrasında valide etmek için vs
    private function sendGeneratedCodeForValidate(int $userId){
        $generatedCode = $this->generateCode();
        \Log::info($userId. " id li kullanıcının email ve phone için doğrulama kodu " . $generatedCode);

    }

    // TODO: TYPE enum yapayım mı ? Bu generator u bi helper a filan mı alsam ?
    private function generateCode():string {
        return  Str::random(6);
    }

}