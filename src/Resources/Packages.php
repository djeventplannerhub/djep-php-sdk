<?php
namespace DJEP\Resources;

class Packages extends Resource
{
    public function list(array $params = []): object
    {
        return $this->http->get('packages', $params);
    }

    public function all(array $params = []): \Generator
    {
        return $this->autoPaginate('packages', $params);
    }

    public function get(int $id): object
    {
        return $this->http->get('package', ['pkg_idnumber' => $id]);
    }

    /**
     * Create a package.
     *
     * @param array $data  Required: name. Optional: client_facing_name,
     *                     category, description, notes, price, taxable, active,
     *                     display_order, hourly, hourly_rate, half_hourly_rate,
     *                     default_length, deposit, deposit_percent,
     *                     overtime_rate, auto_assign_addons, and the photo
     *                     gallery fields.
     *
     * Package names are not required to be unique, matching the software.
     */
    public function create(array $data): object
    {
        return $this->http->post('create_package', $data);
    }

    /**
     * Update a package. Only the fields you pass are written.
     */
    public function update(int $packageid, array $fields): object
    {
        $data = $fields;
        $data['packageid'] = $packageid;
        $data['fields'] = implode(',', array_keys($fields));
        return $this->http->post('update_package', $data);
    }
}
