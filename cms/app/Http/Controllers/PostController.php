<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    //
    public function getPosts(Request $request)
    {
        $posts = Post::all();
    
        return PostResource::collection($posts);
    }
}
