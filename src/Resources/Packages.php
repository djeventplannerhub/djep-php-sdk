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
}
