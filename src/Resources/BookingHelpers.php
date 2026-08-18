<?php
namespace DJEP\Resources;

class BookingHelpers extends Resource
{
    /**
     * List all booking helper configurations
     */
    public function list(): object
    {
        return $this->http->get('booking_helpers');
    }

    /**
     * Run a booking helper on an event
     *
     * @param int         $eventid    The event to run the helper on
     * @param string|null $uniqueId   The helper's unique_id (recommended)
     * @param int|null    $bhid       The helper's 0-based position index (alternative)
     */
    public function run(int $eventid, ?string $uniqueId = null, ?int $bhid = null): object
    {
        $data = ['eventid' => $eventid];
        if ($uniqueId !== null) {
            $data['unique_id'] = $uniqueId;
        } elseif ($bhid !== null) {
            $data['bhid'] = $bhid;
        }
        return $this->http->post('run_booking_helper', $data);
    }
}
