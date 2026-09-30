<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PostInteractionController extends Controller
{
    public function react(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'reaction' => ['required', Rule::in(['like', 'dislike'])],
        ]);

        $reaction = $post->reactions()->where('user_id', $request->user()->id)->first();

        if ($reaction?->reaction === $validated['reaction']) {
            $reaction->delete();

            return back()->with('status', 'Your reaction was removed.');
        }

        $post->reactions()->updateOrCreate(
            ['user_id' => $request->user()->id],
            ['reaction' => $validated['reaction']],
        );

        return back()->with('status', 'Your reaction was recorded.');
    }

    public function storeComment(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('comments', 'id')->where(
                    fn (Builder $query) => $query
                        ->where('post_id', $post->id)
                        ->whereNull('parent_id'),
                ),
            ],
        ]);

        $post->comments()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'body' => $validated['body'],
        ]);

        return back()->with('status', isset($validated['parent_id']) ? 'Your reply was added.' : 'Your comment was added.');
    }

    public function destroyComment(Request $request, Post $post, Comment $comment): RedirectResponse
    {
        $comment = $post->comments()->findOrFail($comment->id);
        $userId = $request->user()->id;

        abort_unless($comment->user_id === $userId || $post->user_id === $userId, 403);

        $comment->delete();

        return back()->with('status', 'The comment and its replies were deleted.');
    }
}
