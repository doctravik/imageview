<?php

namespace Tests\Feature\ActivationToken;

use App\User;
use Tests\TestCase;
use App\ActivationToken;
use App\Jobs\SendActivationToken;
use Illuminate\Support\Facades\Queue;
use App\Notifications\ActivationTokenSent;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ActivationTokenTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function user_is_not_active_as_default()
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret', 
            'password_confirmation' => 'secret'
        ]);

        $this->assertNotNull($user = User::whereName('John Doe')->first());
        $this->assertFalse($user->isActive());
    }

    /** @test */
    public function it_can_send_activation_email_to_registered_user()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret', 
            'password_confirmation' => 'secret'
        ]);        

        Notification::assertSentTo(
            $user = User::whereName('John Doe')->first(),
            ActivationTokenSent::class,
            function ($notification) use ($user) {
                return $notification->username === $user->name;
            }
        );

        $response->assertSessionHas('status', 'Confirm your email please. Activation code was sent on your email.');
    }

    /** @test */
    public function it_can_activate_user_account()
    {
        $user = factory(User::class)->create();
        $activationToken = $user->generateActivationToken();

        $response = $this->get("/account/activate/{$activationToken->token}");

        $response->assertRedirect('/home');
        $this->assertTrue($user->fresh()->isActive());
        $this->assertCount(0, ActivationToken::all());
    }

    /** @test */
    public function it_can_resend_activation_token_on_user_request()
    {
        Notification::fake();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/from')->post('/activation/token/resend/' . $user->id);

        $response->assertRedirect('/from');
        $response->assertSessionHas('status', 'Activation token was resent to your email.');

        Notification::assertSentTo(
            $user = $user,
            ActivationTokenSent::class,
            function ($notification) use ($user) {
                return $notification->username === $user->name;
            }
        );
    }

    /** @test */
    public function it_does_not_resend_token_for_activated_user()
    {
        $user = factory(User::class)->create(['active' => true]);

        Notification::fake();

        $response = $this->actingAs($user)
            ->post('/activation/token/resend/' . $user->id);

        $response->assertRedirect('/home');
        $response->assertSessionMissing('status', 'Activation token was resent to your email.');

        Notification::assertNotSentTo(
            [$user], ActivationTokenSent::class
        );
    }

    /** @test */
    public function it_does_not_resend_token_for_unauthenticated_user()
    {
        $user = factory(User::class)->create();

        Notification::fake();

        $response = $this->post('/activation/token/resend/' . $user->id);

        $response->assertRedirect('/login');        
        $response->assertSessionMissing('status', 'Activation token was resent to your email.');

        Notification::assertNotSentTo(
            [$user], ActivationTokenSent::class
        );
    }
}
