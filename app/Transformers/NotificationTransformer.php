<?php
/**
 * Created by PhpStorm.
 * User: Clown
 * Date: 7/17/18
 * Time: 9:28 PM
 */

namespace App\Transformers;


use Illuminate\Notifications\DatabaseNotification;
use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
{
    public function transform(DatabaseNotification $notification)
    {
        return [
            'id' => $notification->id,
            'type' => $notification->type,
            'data' => $notification->data,
            'read_at' => $notification->read_at ? $notification->read_at->toDateTimeString() : null,
            'created_at' => $notification->created_at->toDateTimeString(),
            'updated_at' => $notification->updated_at->toDateTimeString(),
        ];
    }
}