<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Handlers\ImageUploadHandler;

class UsersController extends Controller
{

	//构造函数, 当载入此类时, 进行身份验证, 只允许游客访问show方法, 否则跳转到登录
	public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);
    }

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
    	$this->authorize('update', $user);	//授权策略
    	return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, ImageUploadHandler $uploader, User $user)
    {
    	$this->authorize('update', $user);
        $data = $request->all();

        if ($request->avatar) {
            $result = $uploader->save($request->avatar, 'avatars', $user->id, 362);
            if ($result) {
                $data['avatar'] = $result['path'];
            }
        }

        $user->update($data);
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }

}
