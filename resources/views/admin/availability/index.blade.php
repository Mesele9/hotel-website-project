@extends('layouts.admin')

@section('title', 'Availability Calendar')

@push('styles')
<style>
    .fc-datagrid-cell-main { font-weight: 600; color: #374151; }
    .fc-resource-group-lane { border-color: #e5e7eb; }
    .fc .fc-resource-group-label { font-weight: 700; font-size: 1.1em; background-color: #f9fafb; }
    .fc .fc-datagrid-cell-frame { display: flex; align-items: center; }
    .custom-label-container { display: flex; align-items: center; padding-left: 4px; cursor: pointer; }
    .fc-event[title="Blocked"] { cursor: pointer; }
</style>
@endpush

@section('content')
<h1 class="text-3xl font-bold text-gray-800 my-4">Availability & Rates Calendar</h1>
<div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4" role="alert">
  <p class="font-bold">Instructions</p>
  <p>1. **Block Dates:** Drag across an available room's timeline. For bulk blocking, use the "Bulk Actions" panel below, then drag.</p>
  <p>2. **Make Available:** Simply click on any amber "Blocked" area to remove it.</p>
</div>

<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Bulk Actions</h3>
    <p class="text-sm text-gray-600 mb-4">Select one or more categories below. Any date selection you make on the calendar will apply to all available rooms in the checked categories.</p>
    <div id="bulk-action-controls" class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach (json_decode($resources) as $resource)
            @if (isset($resource->children))
                <div class="flex items-center">
                    <input type="checkbox" id="bulk-{{ $resource->id }}" name="bulk_room_type" value="{{ $resource->id }}" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="bulk-{{ $resource->id }}" class="ml-2 block text-sm text-gray-900">{{ $resource->title }}</label>
                </div>
            @endif
        @endforeach
    </div>
</div>

<div class="bg-white shadow-md rounded-lg p-6">
    <div id="availability-calendar"></div>
</div>

{{-- Action Modal --}}
<div id="action-modal" class="hidden fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Confirm Action</h3>
                <p class="text-sm text-gray-600 whitespace-pre-line" id="modal-details"></p>
                <div class="mt-4 space-y-2"><button id="block-btn" class="w-full bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">Confirm Block</button></div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"><button type="button" id="cancel-btn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button></div>
        </div>
    </div>
</div>

{{-- Booking Details Modal --}}
<!-- Booking Details Modal -->
<div id="booking-details-modal" class="hidden fixed z-20 inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-xl leading-6 font-bold text-gray-900 border-b pb-2" id="booking-modal-title">
                    Booking Details
                </h3>
                <div class="mt-4 text-gray-700 space-y-2">
                    <p><strong>Guest:</strong> <span id="booking-modal-guest"></span></p>
                    <p><strong>Reference:</strong> <span id="booking-modal-ref" class="font-mono"></span></p>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse justify-between items-center">
                <a href="#" id="booking-modal-link" class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700">
                    View Full Details
                </a>
                <button type="button" id="booking-modal-close-btn" class="mt-3 w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const calendarEl         = document.getElementById('availability-calendar');
    const actionModal        = document.getElementById('action-modal');
    const blockBtn           = document.getElementById('block-btn');
    const cancelBtn          = document.getElementById('cancel-btn');
    const actionModalDetails = document.getElementById('modal-details');

    const bookingModal          = document.getElementById('booking-details-modal');
    const bookingModalGuest     = document.getElementById('booking-modal-guest');
    const bookingModalRef       = document.getElementById('booking-modal-ref');
    const bookingModalLink      = document.getElementById('booking-modal-link');
    const bookingModalCloseBtn  = document.getElementById('booking-modal-close-btn');

    let currentSelection = null;
    const resources      = {!! $resources !!};

    const calendar = new FullCalendar.Calendar(calendarEl, {
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        initialView:        'resourceTimelineMonth',
        headerToolbar:      { left: 'prev,next today', center: 'title', right: 'resourceTimelineMonth,resourceTimelineWeek' },
        titleFormat:        { month: 'long', year: 'numeric' },
        aspectRatio:        1.5,
        editable:           true,
        selectable:         true,
        selectOverlap:      function(event) {
            // only allow selecting on truly empty slots (not on any existing event)
            return false;
        },
        resources:          resources,
        events:             {!! $events !!},
        views: {
            resourceTimelineWeek: {
                slotDuration:   { days: 1 },
                slotLabelFormat:{ weekday: 'short', day: 'numeric', month: 'short' }
            }
        },

        eventContent: function(arg) {
            if (arg.event.display === 'background') {
                return { html: `<div class="fc-event-title" style="padding:2px 5px; cursor:pointer;">${arg.event.title}</div>` };
            }
            return true;
        },

        eventClick: function(info) {
            // 1) Unblock background "Blocked" events
                        // 2) Show booking details for real bookings
            if (info.event.extendedProps.booking_id) {
                bookingModalGuest.textContent = info.event.extendedProps.guest_name;
                bookingModalRef.textContent   = info.event.extendedProps.reference;

                let url = "{{ route('admin.bookings.show', ['booking' => ':id']) }}";
                url = url.replace(':id', info.event.extendedProps.booking_id);
                bookingModalLink.setAttribute('href', url);

                bookingModal.classList.remove('hidden');
            }
            
            if (info.event.title === 'Blocked') {
                if (confirm('Are you sure you want to make this period available again?')) {
                    const data = {
                        action:    'unblock',
                        room_ids:  [ info.event.getResources()[0].id ],
                        start_date: info.event.startStr,
                        end_date:   info.event.endStr
                    };
                    sendUpdateRequest(data, () => info.event.remove());
                }
                return;
            }

        },

        select: function(selectionInfo) {
            // only fires when you drag‐select empty slots
            currentSelection = selectionInfo;
            const checkedBoxes = document.querySelectorAll('input[name="bulk_room_type"]:checked');
            let selectedRoomIds = [], selectedCategories = [];

            if (checkedBoxes.length) {
                checkedBoxes.forEach(box => {
                    selectedCategories.push(box.nextElementSibling.textContent);
                    const group = resources.find(r => r.id === box.value);
                    group.children.forEach(child => {
                        const res = calendar.getResourceById(child.id);
                        if (res.extendedProps.status === 'Available') {
                            selectedRoomIds.push(child.id);
                        }
                    });
                });
                actionModalDetails.innerText = 
                    `Rooms: All available in "${selectedCategories.join(', ')}"\n` +
                    `From: ${selectionInfo.startStr}\nTo: ${selectionInfo.endStr.split('T')[0]}`;
            } else {
                const res = calendar.getResourceById(selectionInfo.resource.id);
                if (!res || res.extendedProps.status !== 'Available') {
                    calendar.unselect();
                    return;
                }
                selectedRoomIds.push(res.id);
                actionModalDetails.innerText = 
                    `Room: ${res.title}\n` +
                    `From: ${selectionInfo.startStr}\nTo: ${selectionInfo.endStr.split('T')[0]}`;
            }

            if (!selectedRoomIds.length) {
                alert('No available rooms were found for the selected criteria.');
                calendar.unselect();
                return;
            }

            currentSelection.room_ids = selectedRoomIds;
            actionModal.classList.remove('hidden');
        }
    });

    calendar.render();

    // Close booking modal
    bookingModalCloseBtn.addEventListener('click', () => {
        bookingModal.classList.add('hidden');
    });

    // AJAX helper
    function sendUpdateRequest(data, successCallback) {
        fetch('{{ route("admin.availability.store") }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify({...data, _token: '{{ csrf_token() }}' })
        })
        .then(response => {
            // **THE FIX: Check if the response is OK before parsing JSON**
            if (!response.ok) {
                // If we get a 422 error, Laravel sends a JSON error payload
                return response.json().then(errorData => {
                    // Reject the promise to trigger the .catch() block
                    return Promise.reject(errorData);
                });
            }
            return response.json();
        })
        .then(result => {
            if (result.status === 'success') {
                if (successCallback) successCallback();
            }
        })
        .catch(error => {
            // **THE FIX: Display the specific error message from the server**
            console.error('Error:', error);
            // If our custom error response has a message, display it. Otherwise, show a generic error.
            const errorMessage = error.message || 'An unexpected error occurred.';
            alert('Action Failed: ' + errorMessage);
        });
    }


    // Block button handler
    blockBtn.addEventListener('click', () => {
        if (!currentSelection) return;
        const data = {
            action:     'block',
            room_ids:   currentSelection.room_ids,
            start_date: currentSelection.startStr,
            end_date:   currentSelection.endStr
        };
        sendUpdateRequest(data, () => {
            currentSelection.room_ids.forEach(id => {
                calendar.addEvent({
                    title:      'Blocked',
                    resourceId: id,
                    start:      currentSelection.startStr,
                    end:        currentSelection.endStr,
                    color:      '#f3b10bff',
                    display:    'background',
                    editable:   false
                });
            });
            actionModal.classList.add('hidden');
            calendar.unselect();
            currentSelection = null;
        });
    });

    // Cancel button
    cancelBtn.addEventListener('click', () => {
        actionModal.classList.add('hidden');
        calendar.unselect();
        currentSelection = null;
    });
});
</script>
@endpush
