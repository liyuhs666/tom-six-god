<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{

	/**
	 * 个人主页
	 * @param  User   $user 当前用户模型
	 * @return [type]       [description]
	 */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }
}
