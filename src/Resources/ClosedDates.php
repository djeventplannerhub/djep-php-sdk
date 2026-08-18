<?php
namespace DJEP\Resources;

class ClosedDates extends Resource
{
    public function list(array $params = []): object
    {
        return $this->http->get('closed_dates', $params);
    }
}
