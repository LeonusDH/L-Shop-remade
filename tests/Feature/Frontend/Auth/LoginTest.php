<?php
declare(strict_types = 1);

namespace Tests\Feature\Frontend\Auth;

use app\Services\Auth\Checkpoint\ActivationCheckpoint;
use app\Services\Auth\Checkpoint\Pool;
use app\Services\Response\Status;
use Illuminate\Http\Response;
use Tests\TestCase;

class LoginTest extends TestCase
{
    private function register(): void
    {
        $response = $this->post(route('frontend.auth.register.handle', [
            'username' => 'D3lph1',
            'email' => 'd3lph1.contact@gmail.com',
            'password' => '123456',
            'password_confirmation' => '123456'
        ]));

        $response->assertJson(['status' => Status::SUCCESS]);
    }

    public function testSuccessfully(): void
    {
        $this->transaction();
        $this->app->singleton(Pool::class, function () {
            return new Pool([]);
        });
        $this->register();
        $response = $this->post(route('frontend.auth.login.handle'), [
            'username' => 'D3lph1',
            'password' => '123456'
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => Status::SUCCESS
        ]);
        $this->rollback();
    }

    public function testUserNotActivated(): void
    {
        $this->transaction();
        $this->app->singleton(Pool::class, function () {
            return new Pool([$this->app->make(ActivationCheckpoint::class)]);
        });
        $this->register();
        $response = $this->post(route('frontend.auth.login.handle'), [
            'username' => 'D3lph1',
            'password' => '123456'
        ]);
        $response->assertStatus(Response::HTTP_CONFLICT);
        $response->assertJson([
            'status' => 'user_not_activated'
        ]);
        $this->rollback();
    }

    public function testBadCredentials(): void
    {
        $this->transaction();
        $this->app->singleton(Pool::class, function () {
            return new Pool([]);
        });
        $this->register();
        $response = $this->post(route('frontend.auth.login.handle'), [
            'username' => 'admin',
            'password' => 'qwerty'
        ]);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson([
            'status' => 'user_not_found'
        ]);
        $this->rollback();
    }
}
