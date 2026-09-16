<?php
namespace DJEP\Resources;

class Equipment extends Resource
{
    public function list(array $params = []): object
    {
        return $this->http->get('equipment', $params);
    }

    public function all(array $params = []): \Generator
    {
        return $this->autoPaginate('equipment', $params);
    }

    /**
     * Create an inventory item.
     *
     * @param array $data  Required: name. Optional: assign_to (the systemid this
     *                     item belongs to, or 0 for standalone), manufacturer,
     *                     model_number, serial_number, category, location,
     *                     quantity_available, purchase_price, current_value,
     *                     rental_fee, addon_fee, date_of_purchase,
     *                     purchased_from, warranty_length, insured, active,
     *                     description, notes.
     */
    public function create(array $data): object
    {
        return $this->http->post('create_equipment', $data);
    }

    /**
     * Update an inventory item. Only the fields you pass are written.
     */
    public function update(int $equipmentid, array $fields): object
    {
        $data = $fields;
        $data['equipmentid'] = $equipmentid;
        $data['fields'] = implode(',', array_keys($fields));
        return $this->http->post('update_equipment', $data);
    }
}
