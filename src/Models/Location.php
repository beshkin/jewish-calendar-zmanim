<?php

namespace Kosherdev\Models;

class Location
{
    private $lat;
    private $lng;
    private $offset;

    public function __construct($lat, $lng, $offset)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->offset = $offset;
    }

    /**
     * @return mixed
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @return mixed
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @return mixed
     */
    public function getOffset()
    {
        return $this->offset;
    }


}
