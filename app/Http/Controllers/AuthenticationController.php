<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    public function authenticate(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response([
                'message' => $validator->messages()
            ], 400);
        }

        $userData = User::where('email', $request->get('email'));
        if ($userData->exists()) {
            $userData = $userData->first();
            $authChecked = Hash::check($request->get('password'), $userData->password);
            if ($userData && $authChecked) {
                $userData->rollApiToken();
                /* Bu endpontin kullanacağı pakete göre bu token headerda da gönderbilirim.
                Ön taraf ilgili yerden okur. */
                return response([
                    'data' => [ 'token' => $userData->token ],
                    'message' => 'Authorization Successful!'
                ], 200);
            }
        }

        return response([
            'message' => 'Unauthorized, Please check your credentials.',
        ], 401);
    }

    public function register(Request $request)
    {
        $rules = [
            'name'  => 'required|string',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
            'twitter_username' => 'required|unique:users',
            'password' => 'required|string|min:8
                           |regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
            'password_confirm' => 'required|same:password'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response([
                'message' => $validator->messages()
            ], 400);
        }

        $userData = new User();
        $userData->fill($request->all());
        $userData->password = Hash::make($request->password);
        $userData->save();
        $userData->sendCodeForValidate();
        $userData->syncTweets();

        return response([
            'message' => 'User Registered'
        ], 200);
    }

    public function validateEmail(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'code'  => 'required|string'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response([
                'message' => $validator->messages()
            ], 400);
        }

        $userData = User::where('email', $request->email)->first();

        if (!$userData) {
            return response([
                'message' => 'User Not Found'
            ], 404);
        }

        if ($userData->email_validate_code !== $request->code) {
            return response([
                'message' => 'Validate code is not right'
            ], 422);
        }

        /* Normalde bu tarz kod doğrulamalarının (zaman kısıtlaması olduğu için) redis gibi sistemlerde tutulması
        daha doğru olurdu. Ama şuanlık zaman alacağından dolayı db yazdıp ordan check ettim */

        $userData->is_email_validated = true;
        $userData->email_validate_code = null;
        $userData->update();
        return response([
            'message' => 'User Email Validated'
        ], 200);
    }

    public function validatePhone(Request $request)
    {
        $rules = [
            'phone' => 'required',
            'code'  => 'required|numeric'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response([
                'message' => $validator->messages()
            ], 400);
        }

        $userData = User::where('phone', $request->phone)->first();

        if (!$userData) {
            return response([
                'message' => 'User Not Found'
            ], 404);
        }

        if ($userData->phone_validate_code !== $request->code) {
            return response([
                'message' => 'Validate code is not right'
            ], 422);
        }

        $userData->is_phone_validated = true;
        $userData->phone_validate_code = null;
        $userData->update();
        return response([
            'message' => 'User Phone Validated'
        ], 200);
    }
}
