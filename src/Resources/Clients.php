<?php
namespace DJEP\Resources;

class Clients extends Resource
{
    public function list(array $params = []): object
    {
        return $this->http->get('clients', $params);
    }

    public function all(array $params = []): \Generator
    {
        return $this->autoPaginate('clients', $params);
    }

    public function get(int $clientid): object
    {
        return $this->http->get('client', ['clientid' => $clientid]);
    }

    /**
     * @param array $data  Required: first_name, last_name. Optional: email, cell_phone, etc.
     */
    public function create(array $data): object
    {
        return $this->http->post('create_client', $data);
    }

    /**
     * @param int   $clientid  The client to update
     * @param array $fields    Associative array of field => value pairs
     */
    public function update(int $clientid, array $fields): object
    {
        $data = $fields;
        $data['clientid'] = $clientid;
        $data['fields'] = implode(',', array_keys($fields));
        return $this->http->post('update_client', $data);
    }

    public function delete(int $clientid): object
    {
        return $this->http->post('delete_client', ['clientid' => $clientid]);
    }
}
