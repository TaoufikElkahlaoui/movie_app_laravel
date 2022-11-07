<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Mail\VerifyCodeMail;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->api([], 1, $validator->errors()->first());
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            $user = Auth::user();
            $data['user'] = new UserResource($user);
            $data['token'] = $user->createToken('my-app-token')->plainTextToken;

            return response()->api($data);
        } else {

            return response()->api([], 1, __('auth.failed'));
        } //end of else

    } //end of login

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users',
            'password' => 'required|min:6',
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->api([], 1, $validator->errors()->first());
        }

        $verifyCode =  rand(10000, 99999);

        $request->merge([
            'password' => bcrypt($request->password),
            'type' => 'user',
            'verify_code' => $verifyCode,
        ]);



        $user = User::create($request->all());

        $data['user'] = new UserResource($user);
        $data['token'] = $user->createToken('my-app-token')->plainTextToken;

        $inputs =  [
            'title' => 'Verify Email',
            'body' => $verifyCode
        ];

        Mail::to($user->email)->send(new VerifyCodeMail($inputs));

        return response()->api($data);
    } //end of register

    public function user()
    {
        $data['user'] = new UserResource(auth()->user('sanctum'));

        return response()->api($data);
    } // end of user

    public function verifyCode(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'verify_code' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->api([], 1, $validator->errors()->first());
        }

        $email = $request->email;
        $verifyCode = $request->verifyCode;

        $user = User::where([
            'email' => $email,
            'verify_code' => $verifyCode,
        ])->first();

        if ($user) {
            $user->email_verified_at = Carbon::now()->timestamp;
            $user->update();
            return Response()->api([]);
        } else
            return response()->api([], 1, "Verification Code is not correct");
    }
}//end of controller
