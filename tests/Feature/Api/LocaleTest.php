<?php

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\Mail;

class LocaleTest extends ApiTestCase
{
    /**
     * Symfony Request::create() adds "Accept-Language: en-us,en;q=0.5" by default, real clients may send nothing.
     */
    private function withoutLanguageHeader(): static
    {
        return $this->withServerVariables(['HTTP_ACCEPT_LANGUAGE' => '']);
    }

    private function wrongLogin(array $headers = [])
    {
        $user = $this->createUser();

        return $this->withHeaders($headers)->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'wrong',
            'device_name' => 'Phone',
        ])->assertStatus(422);
    }

    public function testRussianHeader()
    {
        app()->setLocale('en');

        $this->wrongLogin(['Accept-Language' => 'ru'])
            ->assertJsonPath('errors.email.0', 'Неверный e-mail или пароль.');
    }

    public function testEnglishHeader()
    {
        app()->setLocale('ru');

        $this->wrongLogin(['Accept-Language' => 'en'])
            ->assertJsonPath('errors.email.0', 'These credentials do not match our records.');
    }

    public function testNoHeaderUsesDefaultLocale()
    {
        app()->setLocale('en');
        $this->withoutLanguageHeader()->wrongLogin()->assertJsonPath('errors.email.0', 'These credentials do not match our records.');
    }

    public function testNoHeaderUsesDefaultRussianLocale()
    {
        app()->setLocale('ru');
        $this->withoutLanguageHeader()->wrongLogin()->assertJsonPath('errors.email.0', 'Неверный e-mail или пароль.');
    }

    public function testUnsupportedLanguageUsesDefaultLocale()
    {
        app()->setLocale('ru');
        $this->wrongLogin(['Accept-Language' => 'de'])->assertJsonPath('errors.email.0', 'Неверный e-mail или пароль.');
    }

    public function testUnsupportedLanguageFallsBackToNextByWeight()
    {
        app()->setLocale('ru');
        $this->wrongLogin(['Accept-Language' => 'de,en;q=0.5'])
            ->assertJsonPath('errors.email.0', 'These credentials do not match our records.');
    }

    public function testRegionalLanguageWithWeights()
    {
        app()->setLocale('en');

        $this->wrongLogin(['Accept-Language' => 'ru-RU,ru;q=0.9,en;q=0.8'])
            ->assertJsonPath('errors.email.0', 'Неверный e-mail или пароль.');
    }

    public function testRequiredMessagesUseRussianAttributeNames()
    {
        app()->setLocale('en');

        $this->withHeaders(['Accept-Language' => 'ru'])
            ->postJson('/api/v1/auth/login', [])
            ->assertStatus(422)
            ->assertJsonPath('errors.email.0', 'Поле e-mail обязательно для заполнения.')
            ->assertJsonPath('errors.password.0', 'Поле пароль обязательно для заполнения.')
            ->assertJsonPath('errors.device_name.0', 'Поле название устройства обязательно для заполнения.');
    }

    public function testWrong2FACodeInRussian()
    {
        app()->setLocale('en');
        Mail::fake();
        $user = $this->createUser();

        $token = $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'password', 'device_name' => 'Phone'])
            ->json('token');

        $this->withBearer($token)
            ->withHeaders(['Accept-Language' => 'ru'])
            ->postJson('/api/v1/auth/2fa/verify', ['code' => '000000'])
            ->assertStatus(422)
            ->assertJsonPath('errors.code.0', 'Неверный код подтверждения');
    }

    public function testFrameworkMessagesAreTranslated()
    {
        app()->setLocale('en');

        $this->withHeaders(['Accept-Language' => 'ru'])
            ->getJson('/api/v1/me')
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Требуется авторизация.');
    }

    public function testThrottleMessageIsTranslated()
    {
        app()->setLocale('en');
        $user = $this->createUser();
        $token = $this->accessToken($user);

        // throttle:api allows 60 requests per minute
        for ($i = 0; $i < 60; $i++) {
            $this->withBearer($token)->getJson('/api/v1/me')->assertOk();
        }

        $this->withBearer($token)
            ->withHeaders(['Accept-Language' => 'ru'])
            ->getJson('/api/v1/me')
            ->assertStatus(429)
            ->assertJsonPath('message', 'Слишком много попыток.');
    }

    public function testProfileNameAttribute()
    {
        app()->setLocale('en');
        $user = $this->createUser();

        $this->withBearer($this->accessToken($user))
            ->withHeaders(['Accept-Language' => 'ru'])
            ->patchJson('/api/v1/me', ['name' => 'x'])
            ->assertStatus(422)
            ->assertJsonPath('errors.name.0', 'Длина поля имя должна быть не меньше 2 символов.');
    }

    public function testWebIsNotAffectedByHeader()
    {
        app()->setLocale('en');

        $this->withHeaders(['Accept-Language' => 'ru'])->get('/login')->assertOk();
        $this->assertSame('en', app()->getLocale());
    }
}
