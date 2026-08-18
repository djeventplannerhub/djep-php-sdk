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
}
