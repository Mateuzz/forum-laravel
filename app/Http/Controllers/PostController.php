<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexPostRequest;
use App\Http\Requests\StorePostRequest;
use App\Jobs\PostAddView;
use App\Models\Post;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Slug;

class PostController extends Controller {
    private const DEFAULT_PAGES_COUNT = 20;
    private const MAX_PAGES_COUNT = 100;

    public function store(StorePostRequest $request) {
        $fields = $request->validated();

        $fields['slug'] = Slug::createUnique($fields['title'], Post::class);
        $fields['user_id'] = $request->user()->id;

        // We need this to load default fields
        $post = Post::factory()
                    ->create($fields);


        ++$post->category->posts_count;
        $post->category->save();

        return response()->json([
            'message' => 'Created post sucessfully.',
            'post' => $post->load(Post::getRelationsConstraints()),
        ], Response::HTTP_CREATED);
    }

    public function index(IndexPostRequest $request) {
        $validated = $request->validated();

        $pagesCount = min(self::MAX_PAGES_COUNT,
                          $validated['results-per-page'] ?? self::DEFAULT_PAGES_COUNT);


        $posts = Post::filter($validated);

        if (!isset($validated['search']))
             $posts->order($validated['order'] ?? null);

        return $posts->with(Post::getRelationsConstraints())
                     ->paginate($pagesCount, Post::getVisibleFieldsForIndex());
    }

    public function show(Post $post) {
        PostAddView::dispatch($post);
        return $post->load($post->getRelationsConstraints());
    }
}
