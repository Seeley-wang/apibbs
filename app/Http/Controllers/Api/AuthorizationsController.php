<?php /** @noinspection PhpUndefinedMethodInspection */

/** @noinspection PhpVoidFunctionResultUsedInspection */

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response as Psr7Response;

class AuthorizationsController extends Controller
{

    public function store(AuthorizationRequest $request, AuthorizationServer $authorizationServer, ServerRequestInterface $serverRequest)
    {
        // jwt登陆
//        $username = $request->username;
//        filter_var($username, FILTER_VALIDATE_EMAIL) ? $credentials['email'] = $username : $credentials['phone'] = $username;
//        $credentials['password'] = $request->password;
//
//        if (!$token = \Auth::guard('api')->attempt($credentials)) {
//            return $this->response->errorUnauthorized(trans('auth.failed'));
//        }
//        return $this->responseWithToken($token)->setStatusCode(201);

        // passport 登陆

        try {
            return $authorizationServer->respondToAccessTokenRequest($serverRequest, new Psr7Response())->withStatus(201);
        } catch (OAuthServerException $e) {
            return $this->response->errorUnauthorized($e->getMessage());
        }
    }


    /** 第三方登陆
     * @param $type
     * @param SocialAuthorizationRequest $request
     */
    public function socialStore($type, SocialAuthorizationRequest $request)
    {
        if (!in_array($type, ['weixin'])) {
            return $this->response->errorBadRequest();
        }
        $driver = Socialite::driver($type);
        try {
            if ($code = $request->code) {
                $response = $driver->getAccessTokenResponse($code);
                $token = array_get($response, 'access_token');
            } else {
                $token = $request->access_token;
                if ($type == 'weixin') {
                    $driver->setOpenId($request->openid);
                }
            }
            $oauthUser = $driver->userFromToken($token);
        } catch (\Exception $exception) {
            return $this->response->errorUnauthorized('参数错误，未获取用户信息');
        }

        switch ($type) {
            case 'weixin':
                $unionid = $oauthUser->offsetExists('unionid') ? $oauthUser->offsetGet('unionid') : null;
                if ($unionid) {
                    $user = User::query()->where('weixin_unionid', $unionid)->first();
                } else {
                    $user = User::query()->where('weixin_openid', $oauthUser->getId())->first();
                }

                if (!$user) {
                    $user = User::create([
                        'name' => $oauthUser->getNickname(),
                        'avatar' => $oauthUser->getAvatar(),
                        'weixin_openid' => $oauthUser->getId(),
                        'weixin_unionid' => $unionid,
                    ]);
                }
                break;
        }
        $token = \Auth::guard('api')->fromUser($user);

        return $this->responseWithToken($token)->setStatusCode(201);

    }


    public function responseWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
        ])->setStatusCode(201);
    }

    public function update(AuthorizationServer $authorizationServer, ServerRequestInterface $serverRequest)
    {
        // jwt 刷新token
//        $token = \Auth::guard('api')->refresh();
//        return $this->responseWithToken($token);

        // passport 刷新 token
        try {
            return $authorizationServer->respondToAccessTokenRequest($serverRequest, new Psr7Response());
        } catch (OAuthServerException $e) {
            return $this->response->errorUnauthorized($e->getMessage());
        }
    }

    public function destroy()
    {
//        \Auth::guard('api')->logout();
//        return $this->response->noContent();

        $this->user()->token()->revoke();
        return $this->response->noContent();
    }
}
