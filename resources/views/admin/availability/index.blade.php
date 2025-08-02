@extends('layouts.admin')

@section('title', 'Availability Calendar')

@push('styles')
<style>
    .fc-datagrid-cell-main { font-weight: 600; color: #374151; }
    .fc-resource-group-lane { border-color: #e5e7eb; }
    .fc .fc-resource-group-label { font-weight: 700; font-size: 1.1em; background-color: #f9fafb; cursor: pointer; }
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

<!-- NEW STRATEGY: SEPARATE CONTROL PANEL -->
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

{{-- Modal HTML is correct and does not need changes --}}
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // **FIX: DEFINE ALL CONSTANTS FIRST**
    const calendarEl = document.getElementById('availability-calendar');
    const modal = document.getElementById('action-modal');
    const blockBtn = document.getElementById('block-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const modalDetails = document.getElementById('modal-details');
    let currentSelection = null;
    const resources = {!! $resources !!};

    const calendar = new FullCalendar.Calendar(calendarEl, {
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        initialView: 'resourceTimelineMonth',
        aspectRatio: 1.5,
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'resourceTimelineMonth,resourceTimelineWeek' },
        titleFormat: { month: 'long', year: 'numeric' },
        editable: true,
        selectable: true,
        resources: resources,
        events: {!! $events !!},
        views: { resourceTimelineWeek: { slotDuration: { days: 1 }, slotLabelFormat: { weekday: 'short', day: 'numeric', month: 'short' } } },

        eventClick: function(info) {
            if (info.event.title === 'Blocked') {
                if (confirm('Are you sure you want to make this period available again?')) {
                    const data = { action: 'unblock', room_ids: [info.event.getResources()[0].id], start_date: info.event.startStr, end_date: info.event.endStr };
                    sendUpdateRequest(data, () => info.event.remove());
                }
            }
        },

        select: function(selectionInfo) {
            currentSelection = selectionInfo;
            let selectedRoomIds = [];
            let selectedCategories = [];
            const checkedBoxes = document.querySelectorAll('input[name="bulk_room_type"]:checked');
            
            if (checkedBoxes.length > 0) {
                checkedBoxes.forEach(box => {
                    selectedCategories.push(box.nextElementSibling.textContent);
                    const resourceId = box.value;
                    const mainResource = resources.find(r => r.id === resourceId);
                    if (mainResource && mainResource.children) {
                        mainResource.children.forEach(child => {
                            const childResource = calendar.getResourceById(child.id);
                            if (childResource && childResource.extendedProps.status === 'Available') {
                                selectedRoomIds.push(child.id);
                            }
                        });
                    }
                });
                modalDetails.innerText = `Rooms: All available in "${selectedCategories.join(', ')}"\nFrom: ${selectionInfo.startStr}\nTo: ${selectionInfo.endStr.split('T')[0]}`;
            } else {
                const selectedResource = calendar.getResourceById(selectionInfo.resource.id);
                if (!selectedResource || selectedResource.extendedProps.status !== 'Available') { calendar.unselect(); return; }
                selectedRoomIds.push(selectedResource.id);
                modalDetails.innerText = `Room: ${selectedResource.title}\nFrom: ${selectionInfo.startStr}\nTo: ${selectionInfo.endStr.split('T')[0]}`;
            }

            if(selectedRoomIds.length === 0) {
                alert('No available rooms were found for the selected criteria.'); calendar.unselect(); return;
            }
            
            currentSelection.room_ids = selectedRoomIds;
            modal.classList.remove('hidden');
        },
    });

    calendar.render();

    // **FIX: FULL, UNABRIDGED HELPER FUNCTIONS**
    function sendUpdateRequest(data, successCallback) {
        fetch('{{ route("admin.availability.store") }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify({...data, _token: '{{ csrf_token() }}' })
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                if (successCallback) successCallback();
            } else {
                alert('An error occurred. Check console.');
                console.error(result);
            }
        }).catch(error => {
            console.error('Error:', error);
            alert('An error occurred.');
        });
    }

    blockBtn.addEventListener('click', () => {
        if (!currentSelection) return;
        const data = {
            action: 'block',
            room_ids: currentSelection.room_ids,
            start_date: currentSelection.startStr,
            end_date: currentSelection.endStr,
        };
        sendUpdateRequest(data, () => {
            currentSelection.room_ids.forEach(roomId => {
                calendar.addEvent({
                    title: 'Blocked',
                    resourceId: roomId,
                    start: currentSelection.startStr,
                    end: currentSelection.endStr,
                    color: '#fbbf24',
                    editable: false
                });
            });
            closeModal();
        });
    });

    function closeModal() {
        modal.classList.add('hidden');
        currentSelection = null;
        calendar.unselect();
    }
    
    cancelBtn.addEventListener('click', closeModal);
});
</script>
@endpush