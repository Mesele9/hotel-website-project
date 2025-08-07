@extends('layouts.admin')
@section('title', 'Contact Messages')

@section('content')
<h1 class="text-3xl font-bold text-gray-800 my-4">Inbox</h1>
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <table class="min-w-full bg-white">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">From</th>
                <th class="w-1/2 text-left py-3 px-4 uppercase font-semibold text-sm">Subject</th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Received</th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Action</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            @forelse ($messages as $message)
            <tr class="{{ $message->is_read ? '' : 'font-bold bg-gray-50' }}">
                <td class="w-1/4 text-left py-3 px-4">{{ $message->name }}</td>
                <td class="w-1/2 text-left py-3 px-4">{{ Str::limit($message->subject, 60) }}</td>
                <td class="text-left py-3 px-4">{{ $message->created_at->diffForHumans() }}</td>
                <td class="text-left py-3 px-4">
                    <a href="{{ route('admin.messages.show', $message->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center py-4">You have no messages.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection