<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Topic;

class TopicPolicy extends Policy
{
    public function update(User $user, Topic $topic)
    {
        // return $topic->user_id == $user->id;	//只有当帖子的作者id和当前用户id一致, 才能update
        return $user->isAuthorOf($topic);		//同上
    }

    public function destroy(User $user, Topic $topic)
    {
         return $user->isAuthorOf($topic);
    }
}
