<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PostInteractionController extends Controller
{
    public function react(Request $request, Post $post): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'reaction' => ['required', Rule::in(['like', 'dislike'])],
        ]);

        $reaction = $post->reactions()->where('user_id', $request->user()->id)->first();

        if ($reaction?->reaction === $validated['reaction']) {
            $reaction->delete();

            if ($request->expectsJson()) {
                return $this->reactionResponse($request, $post);
            }

            return back()->with('status', __('Your reaction was removed.'));
        }

        $post->reactions()->updateOrCreate(
            ['user_id' => $request->user()->id],
            ['reaction' => $validated['reaction']],
        );

        if ($request->expectsJson()) {
            return $this->reactionResponse($request, $post);
        }

        return back()->with('status', __('Your reaction was recorded.'));
    }

    public function comments(Post $post): JsonResponse
    {
        $comments = $post->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->oldest()
            ->get();

        return response()->json([
            'html' => view('posts.partials.comment-thread', [
                'post' => $post,
                'comments' => $comments,
                'commentsCount' => $post->comments()->count(),
            ])->render(),
        ]);
    }

    public function storeComment(Request $request, Post $post): JsonResponse|RedirectResponse
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

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'body' => $validated['body'],
        ]);

        if ($request->expectsJson()) {
            $comment->load('user');

            return response()->json([
                'html' => view('posts.partials.comment-item', [
                    'post' => $post,
                    'comment' => $comment,
                    'isReply' => $comment->parent_id !== null,
                ])->render(),
                'parent_id' => $comment->parent_id,
                'comments_count' => $post->comments()->count(),
                'message' => $comment->parent_id ? __('Reply added.') : __('Comment added.'),
            ], 201);
        }

        return back()->with('status', isset($validated['parent_id']) ? __('Your reply was added.') : __('Your comment was added.'));
    }

    public function destroyComment(Request $request, Post $post, Comment $comment): JsonResponse|RedirectResponse
    {
        $comment = $post->comments()->findOrFail($comment->id);
        $userId = $request->user()->id;

        abort_unless($comment->user_id === $userId || $post->user_id === $userId, 403);

        $deletedCommentIds = Comment::where('id', $comment->id)
            ->orWhere('parent_id', $comment->id)
            ->pluck('id');
        $comment->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'deleted_comment_ids' => $deletedCommentIds,
                'comments_count' => $post->comments()->count(),
                'message' => __('Comment deleted.'),
            ]);
        }

        return back()->with('status', __('The comment and its replies were deleted.'));
    }

    private function reactionResponse(Request $request, Post $post): JsonResponse
    {
        return response()->json([
            'likes_count' => $post->reactions()->where('reaction', 'like')->count(),
            'dislikes_count' => $post->reactions()->where('reaction', 'dislike')->count(),
            'viewer_reaction' => $post->reactions()->where('user_id', $request->user()->id)->value('reaction'),
        ]);
    }
}
