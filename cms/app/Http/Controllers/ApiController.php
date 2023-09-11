<?php

namespace App\Http\Controllers;
use App\Models\Post;
use App\Models\Page;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    //
    public function getPosts(Request $request)
    {
        $posts = Post::all();

        return response()->json(['data' => $posts]);
    }
    public function getPages(Request $request)
    {
        $posts = Page::all();

        return response()->json(['data' => $pages]);
    }
}
