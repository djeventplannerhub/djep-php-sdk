<?php
namespace DJEP\Resources;

class Systems extends Resource
{
    public function list(array $params = []): object
    {
        return $this->http->get('systems', $params);
    }

    public function all(array $params = []): \Generator
    {
        return $this->autoPaginate('systems', $params);
    }

    /**
     * Create a system, which is a named group of equipment such as a DJ rack.
     *
     * @param array $data  Required: name. Optional: category, location, active,
     *                     allow_check_availability, description, notes.
     */
    public function create(array $data): object
    {
        return $this->http->post('create_system', $data);
    }

    /**
     * Update a system. Only the fields you pass are written.
     */
    public function update(int $systemid, array $fields): object
    {
        $data = $fields;
        $data['systemid'] = $systemid;
        $data['fields'] = implode(',', array_keys($fields));
        return $this->http->post('update_system', $data);
    }
}
