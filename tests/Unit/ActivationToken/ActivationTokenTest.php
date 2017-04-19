<?php

namespace Tests\Unit\ActivationToken;

use App\User;
use Tests\TestCase;
use App\ActivationToken;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ActivationTokenTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_check_if_user_is_active()
    {
        $user = factory(User::class)->create();

        $this->assertFalse($user->isActive());
    }

    /** @test */
    public function it_can_activate_user()
    {
        $user = factory(User::class)->create();

        $user->activate();

        $this->assertTrue($user->isActive());
    }

    /** @test */
    public function it_can_generate_activation_token_for_user()
    {
        $user = factory(User::class)->create();

        $token = $user->generateActivationToken();

        $this->assertEquals($token->user_id, $user->id);
    }

    /** @test */
    public function it_doesnt_generate_new_token_if_it_exists()
    {
        $user = factory(User::class)->create();

        $tokenOld = $user->generateActivationToken();
        $tokenNew = $user->generateActivationToken();

        $this->assertCount(1, ActivationToken::where('user_id', $user->id)->get());
        $this->assertEquals($user->fresh()->activationToken->id, $tokenOld->id);   
    }
}
