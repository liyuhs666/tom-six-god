<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;

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


    public function edit(User $user)
    {
    	return view('users.edit', compact('user'));
    }

     public function update(UserRequest $request, User $user)
    {
        $user->update($request->all());
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }

}
