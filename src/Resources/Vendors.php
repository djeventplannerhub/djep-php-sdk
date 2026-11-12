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
     * Create a vendor. Same fields as contacts->create(), and the record is
     * marked as a vendor for you.
     *
     * Administrators, master admins and salespeople only.
     */
    public function create(array $data): object
    {
        return $this->http->post('create_vendor', $data);
    }

    /**
     * Update a vendor. Only the fields you pass are written.
     *
     * This endpoint only reaches contacts already marked as vendors. A general
     * contact returns 404 here even though it exists, so use
     * $djep->contacts->update() for those.
     */
    public function update(int $contactid, array $fields): object
    {
        $data = $fields;
        $data['contactid'] = $contactid;
        $data['fields'] = implode(',', array_keys($fields));
        return $this->http->post('update_vendor', $data);
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
