<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostInteractionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_toggle_and_change_their_post_reaction(): void
    {
        $post = $this->createPost();
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();

        $this->actingAs($firstUser)->post(route('posts.reaction', $post), ['reaction' => 'like'])->assertRedirect();
        $this->actingAs($secondUser)->post(route('posts.reaction', $post), ['reaction' => 'like'])->assertRedirect();
        $this->assertDatabaseCount('post_reactions', 2);

        $this->actingAs($firstUser)->post(route('posts.reaction', $post), ['reaction' => 'like'])->assertRedirect();
        $this->assertDatabaseMissing('post_reactions', [
            'post_id' => $post->id,
            'user_id' => $firstUser->id,
        ]);

        $this->actingAs($firstUser)->post(route('posts.reaction', $post), ['reaction' => 'dislike'])->assertRedirect();
        $this->assertDatabaseHas('post_reactions', [
            'post_id' => $post->id,
            'user_id' => $firstUser->id,
            'reaction' => 'dislike',
        ]);
        $this->assertDatabaseCount('post_reactions', 2);

        $this->actingAs($firstUser)
            ->get(route('posts.index'))
            ->assertOk()
            ->assertSee('aria-pressed="true"', false);

        $this->get(route('posts.index'))
            ->assertOk()
            ->assertSee('data-tooltip="Like"', false)
            ->assertSee('data-tooltip="Dislike"', false);

        $this->get(route('posts.show', $post))
            ->assertOk()
            ->assertSee('Like')
            ->assertSee('Dislike')
            ->assertSee('aria-pressed="true"', false);
    }

    public function test_ajax_requests_return_json_for_reactions_comments_replies_and_deletion(): void
    {
        $owner = User::factory()->create();
        $commenter = User::factory()->create();
        $post = $this->createPost($owner);

        $this->actingAs($commenter)
            ->postJson(route('posts.reaction', $post), ['reaction' => 'like'])
            ->assertOk()
            ->assertJson([
                'likes_count' => 1,
                'dislikes_count' => 0,
                'viewer_reaction' => 'like',
            ]);

        $this->actingAs($commenter)
            ->getJson(route('posts.comments.index', $post))
            ->assertOk()
            ->assertJsonPath('html', fn (string $html) => str_contains($html, 'data-comment-list'));

        $commentResponse = $this->actingAs($commenter)
            ->postJson(route('posts.comments.store', $post), ['body' => 'An AJAX comment.'])
            ->assertCreated()
            ->assertJsonPath('parent_id', null)
            ->assertJsonPath('comments_count', 1);

        $comment = Comment::where('body', 'An AJAX comment.')->firstOrFail();

        $this->actingAs($commenter)
            ->postJson(route('posts.comments.store', $post), [
                'body' => 'An AJAX reply.',
                'parent_id' => $comment->id,
            ])
            ->assertCreated()
            ->assertJsonPath('parent_id', $comment->id)
            ->assertJsonPath('comments_count', 2);

        $this->actingAs($owner)
            ->deleteJson(route('posts.comments.destroy', [$post, $comment]))
            ->assertOk()
            ->assertJsonPath('comments_count', 0)
            ->assertJsonCount(2, 'deleted_comment_ids');
    }

    public function test_users_can_comment_and_reply_only_to_top_level_comments_on_the_same_post(): void
    {
        $post = $this->createPost();
        $otherPost = $this->createPost();
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();

        $this->actingAs($firstUser)
            ->post(route('posts.comments.store', $post), ['body' => 'A thoughtful first comment.'])
            ->assertRedirect();

        $comment = Comment::where('body', 'A thoughtful first comment.')->firstOrFail();

        $this->actingAs($secondUser)
            ->post(route('posts.comments.store', $post), [
                'body' => 'A considered reply.',
                'parent_id' => $comment->id,
            ])
            ->assertRedirect();

        $reply = Comment::where('body', 'A considered reply.')->firstOrFail();
        $this->assertSame($comment->id, $reply->parent_id);

        $otherPostComment = Comment::create([
            'post_id' => $otherPost->id,
            'user_id' => $firstUser->id,
            'body' => 'A comment on a different article.',
        ]);

        $this->actingAs($secondUser)
            ->from(route('posts.show', $post))
            ->post(route('posts.comments.store', $post), [
                'body' => 'This reply must be rejected.',
                'parent_id' => $otherPostComment->id,
            ])
            ->assertSessionHasErrors('parent_id');

        $this->actingAs($firstUser)
            ->from(route('posts.show', $post))
            ->post(route('posts.comments.store', $post), [
                'body' => 'Replies cannot be nested further.',
                'parent_id' => $reply->id,
            ])
            ->assertSessionHasErrors('parent_id');

        $this->get(route('posts.show', $post))
            ->assertOk()
            ->assertSee('A thoughtful first comment.')
            ->assertSee('A considered reply.');
    }

    public function test_only_comment_authors_and_post_owners_can_delete_comments(): void
    {
        $postOwner = User::factory()->create();
        $commentAuthor = User::factory()->create();
        $outsider = User::factory()->create();
        $post = $this->createPost($postOwner);
        $otherPost = $this->createPost($postOwner);
        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $commentAuthor->id,
            'body' => 'A comment protected by ownership rules.',
        ]);
        $reply = Comment::create([
            'post_id' => $post->id,
            'user_id' => $outsider->id,
            'parent_id' => $comment->id,
            'body' => 'A reply that should be removed with its parent.',
        ]);
        $authorOwnedComment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $commentAuthor->id,
            'body' => 'A separate comment by its author.',
        ]);
        $foreignComment = Comment::create([
            'post_id' => $otherPost->id,
            'user_id' => $commentAuthor->id,
            'body' => 'A comment on another article.',
        ]);

        $this->actingAs($outsider)
            ->delete(route('posts.comments.destroy', [$post, $comment]))
            ->assertForbidden();

        $this->actingAs($outsider)
            ->delete(route('posts.comments.destroy', [$post, $foreignComment]))
            ->assertNotFound();

        $this->actingAs($commentAuthor)
            ->delete(route('posts.comments.destroy', [$post, $authorOwnedComment]))
            ->assertRedirect();
        $this->assertDatabaseMissing('comments', ['id' => $authorOwnedComment->id]);

        $this->actingAs($postOwner)
            ->delete(route('posts.comments.destroy', [$post, $comment]))
            ->assertRedirect();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
        $this->assertDatabaseMissing('comments', ['id' => $reply->id]);
    }

    public function test_only_verified_users_can_react_or_comment(): void
    {
        $post = $this->createPost();
        $unverifiedUser = User::factory()->unverified()->create();

        $this->post(route('posts.reaction', $post), ['reaction' => 'like'])
            ->assertRedirect(route('login'));

        $this->actingAs($unverifiedUser)
            ->post(route('posts.comments.store', $post), ['body' => 'Not verified yet.'])
            ->assertRedirect(route('verification.notice'));
    }

    public function test_newsroom_shows_reaction_buttons_and_inline_comments_control_for_each_post(): void
    {
        $post = $this->createPost();
        $user = User::factory()->create();

        $this->get(route('posts.index'))
            ->assertOk()
            ->assertSee('data-tooltip="Like"', false)
            ->assertSee('data-tooltip="Dislike"', false)
            ->assertSee(route('posts.comments.index', $post->id), false)
            ->assertSee('data-comments-toggle', false);

        $post->reactions()->create(['user_id' => $user->id, 'reaction' => 'like']);

        $this->actingAs($user)
            ->get(route('posts.index'))
            ->assertOk()
            ->assertSee('aria-pressed="true"', false);

        $this->get(route('posts.show', $post))
            ->assertOk()
            ->assertSee('Like')
            ->assertSee('Dislike');
    }

    private function createPost(?User $owner = null): Post
    {
        $owner ??= User::factory()->create();

        return Post::create([
            'title' => 'A sample post',
            'description' => 'A description long enough for a test article.',
            'user_id' => $owner->id,
        ]);
    }
}
