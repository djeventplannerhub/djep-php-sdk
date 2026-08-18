<?php
namespace DJEP\Resources;

class Contacts extends Resource
{
    public function list(array $params = []): object
    {
        return $this->http->get('contacts', $params);
    }

    public function all(array $params = []): \Generator
    {
        return $this->autoPaginate('contacts', $params);
    }

    public function get(int $id): object
    {
        return $this->http->get('contact', ['contactid' => $id]);
    }
}
