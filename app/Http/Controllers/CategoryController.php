<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use App\Services\Slug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller {
    public function store(StoreCategoryRequest $request) {
        $fields = $request->validated();
        $fields['posts_count'] = $fields['comments_count'] = 0;
        $fields['slug'] = Slug::createUnique($fields['title'], Category::class);

        $category = Category::create($fields);

        return response()->json([
            'message' => 'Category created sucessfully.',
            'category' => $category,
        ], Response::HTTP_CREATED);
    }

    public function index(Request $request) {
        $withRecentPost = $request->has('with-recent-post');

        if (!$withRecentPost) {
            return Category::orderBy('depth')->get();
        }

        return DB::select('
            select c.id, c.title, c.slug, c.parent_id, c.posts_count,
                   c.comments_count ,c.depth, p.id as post_id, p.title as post_title,
                   p.slug as post_slug
            from categories c
            left join lateral (
                select id, title, slug from posts p
                    where p.category_id = c.id
                    order by id desc
                    limit 1
            ) p on true
            order by depth;
        ');
    }

    public function show(Category $category) {
        return $category;
    }
}
