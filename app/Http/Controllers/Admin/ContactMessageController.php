<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->get();
        return view('admin.messages.index', compact('messages'));
    }

    public function show(ContactMessage $message)
    {
        // Mark the message as read when it's viewed
        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }
        return view('admin.messages.show', compact('message'));
    }
}