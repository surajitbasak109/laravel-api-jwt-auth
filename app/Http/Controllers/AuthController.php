<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public $loginAfterSignup = true;


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $token       = null;

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(
                [
                    'status'  => false,
                    'message' => 'Unauthorized',
                ]
            );
        }

        return response()->json(
            [
                'status' => true,
                'token'  => $token,
            ]
        );

    }//end login()


    public function register(Request $request)
    {
        $this->validate(
            $request,
            [
                'name'     => 'required|string',
                'email'    => 'required|email|unique:users',
                'password' => 'required|string|min:6|max:10',
            ]
        );

        $user           = new User();
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        if ($this->loginAfterSignup) {
            return $this->login($request);
        }

        return response()->json(
            [
                'status' => true,
                'user'   => $user,
            ]
        );

    }//end register()


    /**
     * Logout Method
     *
     * @return void
     */
    public function logout(Request $request)
    {
        $this->validate(
            $request,
            ['token' => 'required']
        );

        try {
            JWTAuth::invalidate($request->token);

            return response()->json(
                [
                    'status'  => true,
                    'message' => 'User logged out successfully',
                ]
            );
        } catch (JWTException $exception) {
            return response()->json(
                [
                    'status'  => false,
                    'message' => 'Oops, the user can not be logged out',
                ]
            );
        }

    }//end logout()


    /**
     * User method
     *
     * @return void
     */
    public function user()
    {
        $user = $this->getUser();

        return response()->json(
            [
                'status' => true,
                'user'   => $user,
            ]
        );

    }//end user()


    protected function getUser()
    {
        return JWTAuth::parseToken()->authenticate();

    }//end getUser()


}//end class
