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
}
