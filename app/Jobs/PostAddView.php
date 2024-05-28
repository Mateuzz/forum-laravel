<?php

namespace App\Jobs;

use App\Lib\UserIdentifierCookie;
use App\Models\Post;
use App\Models\PostUserView;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;

class PostAddView implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const MINUTES_BEFORE_ANOTHER_VISIT = 10;

    public function __construct(public Post $post) {
    }

    public function handle(): void {
        $userCookieIdentifier = UserIdentifierCookie::get();
        $ip = Request::ip();
        $post = $this->post;

        $postUserView = PostUserView::where('post_id', $post->id)
            ->where('user_cookie_identifier', $userCookieIdentifier)
            ->unionAll(
                PostUserView::where('post_id', $post->id)
                    ->where('user_ip_identifier', $ip)
                    ->whereNot('user_cookie_identifier', $userCookieIdentifier)
            )
            ->first();

        $wasViewedRecently = false;

        if (!$postUserView) {
            $postUserView = new PostUserView([ 'post_id' => $post->id ]);
            ++$post->unique_views_count;
        } else {
            $wasViewedRecently = now()->diffInMinutes($postUserView->last_view_date, true) < self::MINUTES_BEFORE_ANOTHER_VISIT;
        }

        if (!$wasViewedRecently) {
            ++$post->views_count;
            ++$post->author->posts_views_received;
            $postUserView->last_view_date = now();
            $post->push();
        }

        $postUserView->user_cookie_identifier = $userCookieIdentifier;
        $postUserView->user_ip_identifier = $ip;

        $postUserView->save();
    }
}
