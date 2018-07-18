<?php
/**
 * Created by PhpStorm.
 * User: Clown
 * Date: 7/18/18
 * Time: 12:23 PM
 */

namespace App\Transformers;


use League\Fractal\TransformerAbstract;
use Spatie\Permission\Models\Role;

class RolesTransformer extends TransformerAbstract
{
    public function transform(Role $role)
    {
        return [
            'id' => $role->id,
            'name' => $role->name
        ];
    }
}