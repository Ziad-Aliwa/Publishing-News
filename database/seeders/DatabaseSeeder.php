<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $writer = User::updateOrCreate(
            ['email' => 'writer@example.test'],
            [
                'name' => 'Demo Writer',
                'password' => 'DemoPass123!',
                'email_verified_at' => now(),
            ],
        );

        $secondWriter = User::updateOrCreate(
            ['email' => 'second.writer@example.test'],
            [
                'name' => 'Second Demo Writer',
                'password' => 'DemoPass123!',
                'email_verified_at' => now(),
            ],
        );

        $pendingWriter = User::updateOrCreate(
            ['email' => 'pending.writer@example.test'],
            [
                'name' => 'Pending Demo Writer',
                'password' => 'DemoPass123!',
                'email_verified_at' => null,
            ],
        );

        $welcomePost = $writer->posts()->updateOrCreate(
            ['title' => 'Welcome to Publishing News'],
            ['description' => 'A sample article for testing the public listing and detail page.'],
        );

        $writer->posts()->updateOrCreate(
            ['title' => 'A Writer\'s Guide'],
            ['description' => 'A second sample article for testing edit and delete ownership.'],
        );

        $secondWriter->posts()->updateOrCreate(
            ['title' => 'Community Update'],
            ['description' => 'This article belongs to a different account for permission checks.'],
        );

        Post::updateOrCreate(
            ['title' => 'Imported Article Without Author'],
            [
                'description' => 'This sample has no author to exercise the unassigned post display.',
                'user_id' => null,
            ],
        );

        $firstComment = Comment::updateOrCreate(
            [
                'post_id' => $welcomePost->id,
                'user_id' => $secondWriter->id,
                'parent_id' => null,
                'body' => 'A thoughtful conversation makes every story better.',
            ],
        );

        $firstComment->replies()->updateOrCreate(
            [
                'post_id' => $welcomePost->id,
                'user_id' => $writer->id,
                'body' => 'Agreed. Thanks for adding your perspective.',
            ],
        );

        $welcomePost->reactions()->updateOrCreate(
            ['user_id' => $writer->id],
            ['reaction' => 'like'],
        );

        $welcomePost->reactions()->updateOrCreate(
            ['user_id' => $secondWriter->id],
            ['reaction' => 'dislike'],
        );
    }
}
