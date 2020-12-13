<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @OA\Post(
 * path="/api/login",
 * summary="Login",
 * description="Login by email, password",
 * operationId="authLogin",
 * tags={"Auth"},
 * @OA\RequestBody(
 *    required=true,
 *    description="Pass user credentials",
 *    @OA\JsonContent(
 *       required={"email","password"},
 *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
 *       @OA\Property(property="password", type="string", format="password", example="PassWord12345")
 *    ),
 * ),
 * @OA\Response(
 *    response=401,
 *    description="Wrong credentials response",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string", example="Unauthorized, Please check your credentials")
 *        )
 *     )
 * )
 */

/**
 * @OA\Post(
 * path="/api/register",
 * summary="Sign Up",
 * description="Sign Up by name, email, password, password_confirm, phone, twitter_username",
 * operationId="authRegister",
 * tags={"Auth"},
 * @OA\RequestBody(
 *    required=true,
 *    description="Sign Up user",
 *    @OA\JsonContent(
 *       required={"name", "email","password", "password_confirm", "phone", "twitter_username"},
 *       @OA\Property(property="name", type="string", example="name"),
 *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
 *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
 *       @OA\Property(property="password_confirm", type="string", format="password", example="PassWord12345"),
 *       @OA\Property(property="phone", type="string", example="5552123433"),
 *       @OA\Property(property="twitter_username", type="string", example="username123")
 *    ),
 * ),
 * @OA\Response(
 *    response=400,
 *    description="Input Format response",
 *  )
 * )
 */

/**
 * @OA\Post(
 * path="/api/validate-user-phone",
 * summary="Validate User Phone",
 * description="Validate User Phone by Phone Number",
 * operationId="authValidateUserPhone",
 * tags={"Auth"},
 * @OA\RequestBody(
 *    required=true,
 *    description="Validate User Phone",
 *    @OA\JsonContent(
 *       required={"phone", "code"},
 *       @OA\Property(property="phone", type="string", example="5552123433"),
 *       @OA\Property(property="code", type="string", example="A1B2C3"),
 *    ),
 * ),
 * @OA\Response(
 *    response=400,
 *    description="Input Format response",
 *  )
 * )
 */

/**
 * @OA\Post(
 * path="/api/validate-user-email",
 * summary="Validate User Email",
 * description="Validate User Email",
 * operationId="authValidateUserEmail",
 * tags={"Auth"},
 * @OA\RequestBody(
 *    required=true,
 *    description="Validate User Email",
 *    @OA\JsonContent(
 *       required={"email", "code"},
 *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
 *       @OA\Property(property="code", type="string", example="A1B2C3"),
 *    ),
 * ),
 * @OA\Response(
 *    response=400,
 *    description="Input Format response",
 *  )
 * )
 */

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