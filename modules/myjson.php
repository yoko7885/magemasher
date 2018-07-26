<?php

class myjson
{

    private $json;

    public function __construct()
    {
        $this->json = array();
        return $this;
    }

    public function set($key, $val)
    {
        $this->json[$key] = $val;
        return $this;
    }

    public function get()
    {
        $jsonstr = json_encode($this->json);
        $this->json = array();
        return $jsonstr;
    }
}
?>