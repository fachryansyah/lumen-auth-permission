<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function login(Request $req)
    {
    	$this->validate($req, [
    		"email" 	=> "required|email|max:100",
    		"password" 	=> "required|string|max:100"
    	]);

    	$user = User::whereEmail($req->email)->first();

    	if ($user && Hash::check($req->password, $user->password)) {
    		
    		$apiKey = base64_encode(str_random(40));
    		$user->api_key = $apiKey;
    		$user->save();

    		return response()->json([
    			"message" => "login successfully",
    			"status" => 200,
    			"data" => [
    				"api_key" => $apiKey
    			]
    		]);
    	}else{
    		return response()->json([
    			"message" => "wrong email or password",
    			"status" => 304,
    		]);
    	}
    }

    public function register(Request $req)
    {
    	$this->validate($req, [
    		"first_name" 	=> "required|string|max:100",
    		"last_name" 	=> "required|string|max:100",
    		"email" 		=> "required|email|max:100|unique:users",
    		"phone" 		=> "required|string|max:16",
    		"password" 		=> "required|string|max:100",
    	]);

    	$user = new User;
    	$user->avatar = "https://ui-avatars.com/api/?name=" . $req->first_name . " " . $req->last_name;
    	$user->first_name = $req->first_name;
    	$user->last_name = $req->last_name;
    	$user->email = $req->email;
    	$user->phone = $req->phone;
    	$user->password = Hash::make($req->password);
    	$user->save();

    	$user->syncRoles('user');

    	return response()->json([
    		"message" => "Registration has been successfully",
    		"status" => 200,
    	]);
    }

    public function giveRole()
    {
    	$user = Auth::user();
    	$user->syncRoles('user');
    }
}