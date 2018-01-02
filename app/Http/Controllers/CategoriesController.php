<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\Category;
use App\Models\User;
use App\Models\Link;


class CategoriesController extends Controller
{

	/**
	 * 这里的参数, 是分别引入各个模型, 也就是 app/Model/xxx.php, 这样就可以用$topic-> 调用
	 * @param  Category $category [description]
	 * @param  Request  $request  [description]
	 * @param  Topic    $topic    [description]
	 * @return [type]             [description]
	 */
    public function show(Category $category, Request $request, Topic $topic, User $user, Link $link)
    {
        // 读取分类 ID 关联的话题，并按每 20 条分页
        $topics = $topic->withOrder($request->order)
                        ->where('category_id', $category->id)
                        ->paginate(20);
        // 活跃用户列表
        $active_users = $user->getActiveUsers();
        // 资源链接
        $links = $link->getAllCached();
        // 传参变量到模板中
        return view('topics.index', compact('topics', 'category', 'active_users', 'links'));
    }
}