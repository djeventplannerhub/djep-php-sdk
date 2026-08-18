<?php
namespace DJEP\Resources;

class HourlyTracker extends Resource
{
    /**
     * List hourly time tracking entries (paginated)
     *
     * @param array $params  Optional: page, per_page, sort_by, sort_dir, date_from, date_to,
     *                       djemployeeid, approved, paid
     */
    public function list(array $params = []): object
    {
        return $this->http->get('hourly_tracker', $params);
    }

    /**
     * Auto-paginate through all hourly tracker entries
     */
    public function all(array $params = []): \Generator
    {
        return $this->autoPaginate('hourly_tracker', $params);
    }
}
