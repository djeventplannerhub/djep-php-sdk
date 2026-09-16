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
     *                     notes, price, taxable, active, display_order,
     *                     image_1_url through image_6_url with matching _title
     *                     and _description, default_icon.
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
     */
    public function update(int $addonid, array $fields): object
    {
        $data = $fields;
        $data['addonid'] = $addonid;
        $data['fields'] = implode(',', array_keys($fields));
        return $this->http->post('update_addon', $data);
    }
}
