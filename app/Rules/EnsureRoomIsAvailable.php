<?php

namespace App\Rules;

use App\Models\Booking;
use App\Models\DateBlock;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Carbon;

class EnsureRoomIsAvailable implements Rule, DataAwareRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    /**
     * The booking ID to ignore during checks (for updates).
     *
     * @var int|null
     */
    protected $ignoreBookingId = null;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($ignoreBookingId = null)
    {
        $this->ignoreBookingId = $ignoreBookingId;
    }

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute The attribute being validated (e.g., 'room_id')
     * @param  mixed  $value The value of the attribute (the room_id)
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function passes($attribute, $value)
    {
        $roomId = $value;
        // The setData method provides access to other fields in the request
        $checkInDate = Carbon::parse($this->data['check_in_date']);
        $checkOutDate = Carbon::parse($this->data['check_out_date']);

        // 1. Check for overlapping guest bookings
        $bookingQuery = Booking::where('room_id', $roomId)
            ->where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->where('check_in_date', '<', $checkOutDate)
                      ->where('check_out_date', '>', $checkInDate);
            });
            
        // If we're updating a booking, ignore its own record
        if ($this->ignoreBookingId) {
            $bookingQuery->where('id', '!=', $this->ignoreBookingId);
        }

        if ($bookingQuery->exists()) {
            return false; // Found an overlapping booking
        }

        // 2. Check for overlapping admin date blocks
        $blockQuery = DateBlock::where('room_id', $roomId)
            ->where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->where('start_date', '<', $checkOutDate)
                      ->where('end_date', '>=', $checkInDate); // Note: >= because blocks are inclusive
            });

        if ($blockQuery->exists()) {
            return false; // Found an overlapping date block
        }

        return true; // The room is available
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This room is not available for the selected dates due to an existing booking or admin block.';
    }
}