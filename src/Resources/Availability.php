<?php
namespace DJEP\Resources;

class Availability extends Resource
{
    /**
     * Check availability for a single date (with employee detail)
     *
     * @param string $date  Date to check (MM/DD/YYYY or YYYY-MM-DD)
     */
    public function check(string $date): object
    {
        return $this->http->get('check_availability', ['date' => $date]);
    }

    /**
     * Check availability for a date range (max 90 days)
     *
     * @param string $startDate  Start of range
     * @param string $endDate    End of range
     */
    public function range(string $startDate, string $endDate): object
    {
        return $this->http->get('check_availability_range', [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }
}
