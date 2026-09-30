<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class WebAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_must_verify_email(): void
    {
        Event::fake([Registered::class]);

        $response = $this->post(route('register'), [
            'name' => 'News Writer',
            'email' => 'writer@example.com',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertDatabaseHas('users', ['email' => 'writer@example.com']);
        $this->assertAuthenticated();
        Event::assertDispatched(Registered::class);

        $this->get(route('posts.create'))->assertRedirect(route('verification.notice'));
    }

    public function test_database_seeder_creates_repeatable_demo_accounts_and_posts(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 3);
        $this->assertDatabaseCount('posts', 4);
        $this->assertDatabaseCount('comments', 2);
        $this->assertDatabaseCount('post_reactions', 2);

        $writer = User::where('email', 'writer@example.test')->firstOrFail();
        $pendingWriter = User::where('email', 'pending.writer@example.test')->firstOrFail();

        $this->assertTrue(Hash::check('DemoPass123!', $writer->password));
        $this->assertTrue($writer->hasVerifiedEmail());
        $this->assertFalse($pendingWriter->hasVerifiedEmail());
        $this->assertDatabaseHas('posts', [
            'title' => 'Imported Article Without Author',
            'user_id' => null,
        ]);
    }

    public function test_login_and_logout_work(): void
    {
        $user = User::factory()->create();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('posts.index'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))
            ->assertRedirect(route('posts.index'));

        $this->assertGuest();
    }

    public function test_only_verified_owners_can_manage_their_posts(): void
    {
        $owner = User::factory()->create();
        $anotherUser = User::factory()->create();
        $otherPost = Post::create([
            'title' => 'Another user post',
            'description' => 'This post belongs to another user.',
            'user_id' => $anotherUser->id,
        ]);

        $this->get(route('posts.create'))->assertRedirect(route('login'));

        $this->actingAs($owner)
            ->post(route('posts.store'), [
                'title' => 'My post',
                'description' => 'A description long enough for validation.',
                'post_creator' => $anotherUser->id,
            ])
            ->assertRedirect(route('posts.index'));

        $this->assertDatabaseHas('posts', [
            'title' => 'My post',
            'user_id' => $owner->id,
        ]);

        $this->actingAs($owner)
            ->get(route('posts.edit', $otherPost))
            ->assertNotFound();

        $this->actingAs($owner)
            ->delete(route('posts.destroy', $otherPost))
            ->assertNotFound();
    }

    public function test_post_pages_render_for_public_readers_and_verified_owners(): void
    {
        $owner = User::factory()->create();
        $post = Post::create([
            'title' => 'Published post',
            'description' => 'A public description for this published post.',
            'user_id' => $owner->id,
        ]);

        $this->get(route('posts.index'))->assertOk();
        $this->get(route('posts.show', $post))->assertOk();

        $this->actingAs($owner)->get(route('posts.create'))->assertOk();
        $this->get(route('posts.edit', $post))->assertOk();
    }

    public function test_password_reset_link_is_sent(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_authentication_forms_render(): void
    {
        $this->get(route('register'))->assertOk();
        $this->get(route('login'))->assertOk();
        $this->get(route('password.request'))->assertOk();
        $this->get(route('password.reset', ['token' => 'test-token', 'email' => 'writer@example.com']))->assertOk();

        $user = User::factory()->unverified()->create();
        $this->actingAs($user)->get(route('verification.notice'))->assertOk();
    }

    public function test_password_reset_changes_the_password(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewStrongPassword123!',
            'password_confirmation' => 'NewStrongPassword123!',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('NewStrongPassword123!', $user->fresh()->password));
    }

    public function test_signed_verification_link_verifies_the_user(): void
    {
        Event::fake([Verified::class]);
        $user = User::factory()->unverified()->create();
        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(30), [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        $this->actingAs($user)
            ->get($url)
            ->assertRedirect(route('posts.index'));

        $this->assertNotNull($user->fresh()->email_verified_at);
        Event::assertDispatched(Verified::class);
    }
}
