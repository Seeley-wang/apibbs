<?php
/**
 * Created by PhpStorm.
 * User: Clown
 * Date: 7/18/18
 * Time: 1:27 AM
 */

namespace App\Transformers;


use League\Fractal\TransformerAbstract;
use Spatie\Permission\Models\Permission;

class PermissionTransformer extends TransformerAbstract
{
    public function transform(Permission $permission)
    {
        return [
            'id' => $permission->id,
            'name' => $permission->name
        ];
    }
}