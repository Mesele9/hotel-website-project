<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\EventSpace; 
use App\Models\ContactMessage;

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

    /**
     * Handle the contact form submission.
     */
    public function storeContactForm(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        ContactMessage::create($validated);
        
        // Optional: Send an email notification to the admin here

        return redirect()->route('page.contact')->with('success', 'Thank you for your message! We will get back to you shortly.');
    }

    /**
     * Handle the event space inquiry form submission.
     */
    public function storeEventInquiry(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'company_name' => 'nullable|string|max:255',
            'event_type' => 'nullable|string|max:255',
            'number_of_guests' => 'nullable|integer',
            'space_name' => 'required|string', // Hidden field
            'services' => 'nullable|array',
            'details' => 'nullable|string',
        ]);

        $subject = "Event Inquiry for: " . $validated['space_name'];

        // Format the message body
        $messageBody = "An inquiry has been submitted for an event.\n\n";
        $messageBody .= "Event Space: " . $validated['space_name'] . "\n";
        $messageBody .= "Contact Name: " . $validated['name'] . "\n";
        $messageBody .= "Contact Email: " . $validated['email'] . "\n";
        if ($request->filled('company_name')) {
            $messageBody .= "Company: " . $validated['company_name'] . "\n";
        }
        if ($request->filled('event_type')) {
            $messageBody .= "Type of Event: " . $validated['event_type'] . "\n";
        }
        if ($request->filled('number_of_guests')) {
            $messageBody .= "Number of Guests: " . $validated['number_of_guests'] . "\n";
        }
        if ($request->filled('services')) {
            $messageBody .= "\nServices Requested:\n- " . implode("\n- ", $validated['services']) . "\n";
        }
        if ($request->filled('details')) {
            $messageBody .= "\nAdditional Details:\n" . $validated['details'];
        }

        // Save to the database
        ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $subject,
            'message' => $messageBody,
        ]);

        return redirect()->back()->with('success', 'Thank you for your inquiry! Our events team will contact you shortly.');
    }

}
 