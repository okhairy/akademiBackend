<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getAllUsers(): JsonResponse
{
    $users = User::all();
    return response()->json(['users' => $users], 200);
}

}
