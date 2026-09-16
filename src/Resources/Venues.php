<?php
namespace DJEP\Resources;

class Venues extends Resource
{
    public function list(array $params = []): object
    {
        return $this->http->get('venues', $params);
    }

    public function all(array $params = []): \Generator
    {
        return $this->autoPaginate('venues', $params);
    }

    public function get(int $id): object
    {
        return $this->http->get('venue', ['venueid' => $id]);
    }

    /**
     * Create a venue.
     *
     * @param array $data  Required: name. Optional: address, address2, city,
     *                     state, zipcode, country, telephone, cell_phone, email,
     *                     manager, category, website, directions,
     *                     one_time_venue.
     *
     * Set one_time_venue to true for a site you do not want in the master
     * picklist, such as a private residence. It is still a real venue record,
     * because an event points at a venue rather than storing an address.
     */
    public function create(array $data): object
    {
        return $this->http->post('create_venue', $data);
    }

    /**
     * Update a venue. Only the fields you pass are written.
     */
    public function update(int $venueid, array $fields): object
    {
        $data = $fields;
        $data['venueid'] = $venueid;
        $data['fields'] = implode(',', array_keys($fields));
        return $this->http->post('update_venue', $data);
    }
}
