<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(title="Tweet Feeder API", version="1.0.0")
 */

/**
 * @OA\Post(
 * path="/api/login",
 * summary="Login",
 * description="Login by email, password",
 * tags={"Auth Endpoints"},
 * @OA\RequestBody(
 *    required=true,
 *    description="User credentials",
 *    @OA\JsonContent(
 *       required={"email","password"},
 *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
 *       @OA\Property(property="password", type="string", format="password", example="PassWord12345")
 *    ),
 * ),
 * @OA\Response(
 *    response=200,
 *    description="User credentials success"
 *     )
 * )
 */

/**
 * @OA\Post(
 * path="/api/register",
 * summary="Register",
 * description="Register by name, email, password, password_confirm, phone, twitter_username",
 * tags={"Auth Endpoints"},
 * @OA\RequestBody(
 *    required=true,
 *    description="Register user",
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
 *    response=200,
 *    description="User Register Success",
 *  )
 * )
 */

/**
 * @OA\Post(
 * path="/api/validate-phone",
 * summary="Validate User Phone",
 * description="Validate User Phone by Phone Number and Code",
 * tags={"Auth Endpoints"},
 * @OA\RequestBody(
 *    required=true,
 *    description="Validate User Phone",
 *    @OA\JsonContent(
 *       required={"phone", "code"},
 *       @OA\Property(property="phone", type="string", example="5552123433"),
 *       @OA\Property(property="code", type="number", example="1234")
 *    ),
 * ),
 * @OA\Response(response=200,description="User Phone Validate Success"),
 * @OA\Response(response=400, description="Validator fails"),
 * @OA\Response(response=404, description="User Not Found"),
 * @OA\Response(response=422, description="Validate code is not right")
 * )
 */

/**
 * @OA\Post(
 * path="/api/validate-email",
 * summary="Validate User Email",
 * description="Validate User Email by Email and Code",
 * tags={"Auth Endpoints"},
 * @OA\RequestBody(
 *    required=true,
 *    description="Validate User Email",
 *    @OA\JsonContent(
 *       required={"email", "code"},
 *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
 *       @OA\Property(property="code", type="string", example="ABCDEF"),
 *    ),
 * ),
 * @OA\Response(response=200, description="User Email Validate Success"),
 * @OA\Response(response=400, description="Validator fails"),
 * @OA\Response(response=404, description="User Not Found"),
 * @OA\Response(response=422, description="Validate code is not right")
 * )
 */

/**
 * @OA\SecurityScheme(
 *     @OA\Flow(
 *         flow="clientCredentials",
 *         tokenUrl="oauth/token",
 *         scopes={}
 *     ),
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="bearer"
 * )
 */

/**
 *  @OA\Get(
 *      path="/api/tweets",
 *      tags={"Tweet Endpoints"},
 *      security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *          name="twitter_username",
 *          in="query",
 *          required=false,
 *          description="Twitter username",
 *          @OA\Schema(
 *              type="string"
 *          ),
 *      ),
 *      @OA\Response(response="200", description="Listing of Tweets paginate with size 20.")
 *  )
 */

/**
 *  @OA\Put(
 *      path="/api/tweets/{id}",
 *      tags={"Tweet Endpoints"},
 *      security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\RequestBody(
 *          required=false,
 *          description="Tweet Update",
 *          @OA\JsonContent(
 *              required=true,
 *              required={"content"},
 *              @OA\Property(property="content", type="text", example="Tweet content")
 *          ),
 *      ),
 *      @OA\Response(response="200", description="Tweet Updated")
 *  )
 */

/**
 *  @OA\Post(
 *      path="/api/tweets/{id}/publish",
 *      tags={"Tweet Endpoints"},
 *      security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          @OA\Schema(
 *           type="integer"
 *          )
 *      ),
 *      @OA\RequestBody(
 *          required=false,
 *          description="Publish Tweet with remote"
 *      ),
 *      @OA\Response(response="200", description="Tweet is published to remote")
 *  )
 */

/**
 *  @OA\Post(
 *      path="/api/tweets/sync",
 *      tags={"Tweet Endpoints"},
 *      security={{"bearerAuth":{}}},
 *      @OA\Response(response="200", description="Tweets syncronized with remote")
 *  )
 */

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;
}
