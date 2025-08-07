@extends('layouts.admin')
@section('title', 'View Message')

@section('content')
<div class="flex justify-between items-center my-4">
    <h1 class="text-3xl font-bold text-gray-800">View Message</h1>
    <a href="{{ route('admin.messages.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
        &larr; Back to Inbox
    </a>
</div>

<div class="bg-white shadow-md rounded-lg p-6">
    <div class="border-b pb-4 mb-4">
        <h2 class="text-2xl font-semibold">{{ $message->subject }}</h2>
        <p class="text-gray-600 mt-2">From: <a href="mailto:{{ $message->email }}" class="text-blue-600">{{ $message->name }} &lt;{{ $message->email }}&gt;</a></p>
        <p class="text-sm text-gray-500">Received: {{ $message->created_at->format('M d, Y \a\t H:i A') }}</p>
    </div>
    <div class="prose max-w-none">
        {!! nl2br(e($message->message)) !!}
    </div>
</div>
@endsection