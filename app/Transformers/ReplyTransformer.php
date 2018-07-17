<?php
/**
 * Created by PhpStorm.
 * User: Clown
 * Date: 7/17/18
 * Time: 6:39 AM
 */

namespace App\Transformers;


use App\Models\Reply;
use App\Models\Topic;
use League\Fractal\TransformerAbstract;

class ReplyTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user','topic'];

    public function transform(Reply $reply)
    {
        return [
            'id' => $reply->id,
            'user_id' => $reply->user_id,
            'topic_id' => $reply->topic_id,
            'content' => $reply->content,
            'created_at' => $reply->created_at->toDateTimeString(),
            'update_at' => $reply->updated_at->toDateTimeString()
        ];
    }

    public function includeUser(Reply $reply)
    {
        return $this->item($reply->user, new UserTransformer());
    }

    public function includeTopic(Reply $reply)
    {
        return $this->item($reply->topic,new TopicTransformer());
    }

}