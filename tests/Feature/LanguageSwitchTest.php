<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_language_switch_persists_and_sets_direction_for_both_locales(): void
    {
        $this->get(route('posts.index'))
            ->assertOk()
            ->assertSee('<html lang="en" dir="ltr">', false);

        $this->from(route('posts.index'))
            ->post(route('language.update'), ['locale' => 'ar'])
            ->assertRedirect(route('posts.index'));

        $this->get(route('posts.index'))
            ->assertOk()
            ->assertSee('<html lang="ar" dir="rtl">', false)
            ->assertSee('أحدث المقالات')
            ->assertSee('تغيير اللغة');

        $this->from(route('posts.index'))
            ->post(route('language.update'), ['locale' => 'en'])
            ->assertRedirect(route('posts.index'));

        $this->get(route('posts.index'))
            ->assertOk()
            ->assertSee('<html lang="en" dir="ltr">', false)
            ->assertSee('The latest stories');
    }

    public function test_arabic_is_applied_to_auth_and_post_forms_and_validation(): void
    {
        $this->withSession(['locale' => 'ar'])
            ->get(route('register'))
            ->assertOk()
            ->assertSee('<html lang="ar" dir="rtl">', false)
            ->assertSee('إنشاء حسابي');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('posts.create'))
            ->post(route('posts.store'), [
                'title' => 'x',
                'description' => 'short',
            ])
            ->assertSessionHasErrors(['title', 'description']);

        $this->get(route('posts.create'))
            ->assertOk()
            ->assertSee('يجب ألا يقل عنوان المقال عن 3 أحرف.');

        $post = Post::create([
            'title' => 'مقال تجريبي',
            'description' => 'محتوى عربي للاختبار.',
            'user_id' => $user->id,
        ]);

        $this->get(route('posts.show', $post))
            ->assertOk()
            ->assertSee('الحوار')
            ->assertSee('أضف تعليقك');

        $this->actingAs($user)
            ->postJson(route('posts.comments.store', $post), ['body' => 'تعليق باللغة العربية'])
            ->assertCreated()
            ->assertJsonPath('message', 'تمت إضافة التعليق.');
    }
}
