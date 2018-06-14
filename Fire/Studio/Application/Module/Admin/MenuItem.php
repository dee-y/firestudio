<?php

namespace Fire\Studio\Application\Module\Admin;

class MenuItem
{

    public $id;
    public $title;
    public $url;

    public function __construct($id, $title, $url)
    {
        $this->id = $id;
        $this->title = $title;
        $this->url = $url;
    }

}
