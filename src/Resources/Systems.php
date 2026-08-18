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
}
