<?php
/**
 * Created by PhpStorm.
 * User: Clown
 * Date: 18/7/18
 * Time: 下午6:37
 */

namespace Tests\Traits;


use App\Models\User;

trait ActingJWTUser
{
    public function JWTActingAs(User $user)
    {
        $token = \Auth::guard('api')->fromUser($user);
        $this->withHeaders(['Authorization' => 'Bearer '.$token]);

        return $this;
    }
}