<?php
namespace DJEP\Resources;

class Addons extends Resource
{
    public function list(array $params = []): object
    {
        return $this->http->get('addons', $params);
    }

    public function all(array $params = []): \Generator
    {
        return $this->autoPaginate('addons', $params);
    }

    public function get(int $id): object
    {
        return $this->http->get('addon', ['addonid' => $id]);
    }

    /**
     * Create an addon.
     *
     * @param array $data  Required: name. Optional: category, description,
     *                     notes, price, deposit, taxable, active,
     *                     display_order, image_1_url through image_6_url with
     *                     matching _title and _description, default_icon.
     *
     *                     deposit is a flat amount added to the retainer of any
     *                     event this addon goes on, unless that event's package
     *                     takes a percentage deposit. It cannot be negative.
     *
     * @throws \DJEP\Exceptions\ConflictException when an addon with that name
     *         already exists. Call getExistingId() on the exception for its id.
     */
    public function create(array $data): object
    {
        return $this->http->post('create_addon', $data);
    }

    /**
     * Update an addon. Only the fields you pass are written.
     *
     * deposit behaves like price: named with no value keeps the stored figure,
     * and 0 clears it.
     */
    public function update(int $addonid, array $fields): object
    {
        $data = $fields;
        $data['addonid'] = $addonid;
        $data['fields'] = implode(',', array_keys($fields));
        return $this->http->post('update_addon', $data);
    }
}
