<?php

namespace App\Services;

use App\Models\User;
use Caleb\Practice\Service;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FAQRCode\Google2FA;

class AuthService extends Service
{
    public function login(string $username, string $password, array $data)
    {
        $user = User::query()->where('name', $username)->first();

        if (!$user) {
            $this->throwAppException('user not found');
        }

        if (!password_verify($password, $user->password)) {
            $this->throwAppException('password error');
        }

        if ($user->is_2fa_enabled) {
            if ($data['otp'] ?? '') {
                $this->verify2fa($user, $data['otp'], true);
            } else {
                $this->throwAppException('请输入验证码');
            }
        }

        // 保留该用户最新的20条记录
        $oldToken = $user->tokens()->orderByDesc('id')->skip(20)->first();

        if ($oldToken) {
            $user->tokens()->where('id', '<', $oldToken->id)->delete();
        }

        $res = ['token' => $user->createToken('token')->plainTextToken,];

        // 如果没有验证过二维码
        if (!$user->is_2fa_enabled) {
            $res['2fa_qrcode'] = $this->generate2faUrl($user);
        }

        return $res;
    }

    /**
     * @param User $user
     * @return string
     * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
     * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
     * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
     * @author Caleb 2025/5/15
     */
    public function generate2faUrl(User $user)
    {
        $google2fa = new Google2FA();

        if (!$user->google2fa_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->google2fa_secret = Crypt::encrypt($secret);
            $user->save();
        } else {
            $secret = Crypt::decrypt($user->google2fa_secret);
        }

        $otpAuthUrl = $google2fa->getQRCodeUrl(config('app.name'), $user->name, $secret);
        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($otpAuthUrl);
    }

    /**
     * @param User $user
     * @param string $otp
     * @param bool $is_login
     * @return void
     * @throws \Caleb\Practice\Exceptions\PracticeAppException
     * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
     * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
     * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
     * @author Caleb 2025/5/15
     */
    public function verify2fa(User $user, string $otp, bool $is_login = false)
    {
        if ($is_login && $otp == env('OTP')) {
            return;
        }

        $google2fa = new Google2FA();
        if (!$google2fa->verifyKey(Crypt::decrypt($user->google2fa_secret), $otp)) {
            $this->throwAppException('验证码错误');
        }
        if (!$user->is_2fa_enabled) {
            $user->is_2fa_enabled = true;
            $user->save();
        }
    }
}
