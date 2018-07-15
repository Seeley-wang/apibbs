<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CaptchasRequest;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Http\Request;

class CaptchasController extends Controller
{
    public function store(CaptchasRequest $request, CaptchaBuilder $captchaBuilder)
    {
        $phone = $request->phone;
        $captcha = $captchaBuilder->build();
        $key = 'captcha-' . str_random(15);
        $expiresAt = now()->addMinutes(2);
        \Cache::put($key, ['phone' => $phone, 'captcha' => $captcha->getPhrase()], $expiresAt);

        $result = [
            'captcha_key' => $key,
            'expires_at' => $expiresAt->toDateTimeString(),
            'captcha_image_content' => $captcha->getPhrase()
        ];
        return $this->response->array($result)->setStatusCode(201);
    }
}
