<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\EventSpace; 
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display the meetings and events page with a list of spaces.
     */
    public function meetings()
    {
        $eventSpaces = EventSpace::orderBy('capacity', 'asc')->get();
        return view('public.meetings', compact('eventSpaces'));
    }
    
    /**
     * Display a single event space.
     */
    public function showEventSpace(EventSpace $eventSpace)
    {
        $eventSpace->load('images'); // Eager load the gallery images
        return view('public.event_space_show', compact('eventSpace'));
    }

    /**
     * Display a list of published posts.
     */
    public function localGuide()
    {
        $posts = Post::where('status', 'published')
                     ->where('published_at', '<=', now())
                     ->latest('published_at')
                     ->paginate(9); // Paginate for long lists

        return view('public.local_guide', compact('posts'));
    }

    /**
     * Display a single post.
     */
    public function showPost(Post $post)
    {
        // Abort if someone tries to access a draft post directly
        if ($post->status !== 'published' || $post->published_at > now()) {
            abort(404);
        }

        return view('public.posts.show', compact('post'));
    }


    public function contact()
    {
        return view('public.contact');
    }
}