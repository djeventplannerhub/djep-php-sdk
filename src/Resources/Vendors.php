<?php
namespace DJEP\Resources;

class Vendors extends Resource
{
    public function list(array $params = []): object
    {
        return $this->http->get('vendors', $params);
    }

    public function all(array $params = []): \Generator
    {
        return $this->autoPaginate('vendors', $params);
    }

    public function get(int $id): object
    {
        return $this->http->get('vendor', ['contactid' => $id]);
    }

    /**
     * Link a vendor to an event
     */
    public function linkToEvent(int $eventid, int $contactid): object
    {
        return $this->http->post('link_vendor', [
            'eventid' => $eventid,
            'contactid' => $contactid,
        ]);
    }
}
