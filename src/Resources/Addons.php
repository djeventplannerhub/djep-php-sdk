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
}
