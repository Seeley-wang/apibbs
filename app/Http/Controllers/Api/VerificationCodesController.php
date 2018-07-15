<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Http\Request;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\InvalidArgumentException;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class VerificationCodesController extends Controller
{
    /**
     * @param VerificationCodeRequest $request
     * @param EasySms $sms
     */
    public function store(VerificationCodeRequest $request, EasySms $sms)
    {

        $captchaData = \Cache::get($request->captcha_key);
        if (!$captchaData) {
            return $this->response->error('图片验证码失效', 422);
        }
        if (!hash_equals($captchaData['captcha'], $request->captcha_code)) {
            \Cache::forget($request->captcha_key);
            return $this->response->errorUnauthorized('图片验证码错误');
        }
        $phone = $captchaData['phone'];
        if (!app()->environment('production')) {
            $code = '1234';
        } else {
            $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);
            try {
                $result = $sms->send($phone, [
                    'content' => "【王跃武】您的验证码是{$code}。如非本人操作，请忽略本短信 "
                ]);
            } catch (InvalidArgumentException $e) {
            } catch (NoGatewayAvailableException $e) {
                $response = $e->getResponse();
                $result = json_decode($response->getBody()->getContents(), true);
                return $this->response->errorInternal($result['msg'] ?? '短信发送异常');
            }
        }

        $key = 'verificationCode_' . str_random(15);
        $expiredAt = now()->addMinutes(10);

        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        \Cache::forget($request->captcha_key);
        return $this->response->array([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString()
        ])->setStatusCode(201);

    }
}
