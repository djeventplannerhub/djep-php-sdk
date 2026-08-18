<?php
namespace DJEP\Resources;

class Employees extends Resource
{
    public function list(array $params = []): object
    {
        return $this->http->get('employees', $params);
    }

    public function all(array $params = []): \Generator
    {
        return $this->autoPaginate('employees', $params);
    }

    public function get(int $id): object
    {
        return $this->http->get('employee', ['djemployeeid' => $id]);
    }
}
