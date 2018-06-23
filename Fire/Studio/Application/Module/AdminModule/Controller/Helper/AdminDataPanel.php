<?php

namespace Fire\Studio\Application\Module\AdminModule\Controller\Helper;

use Fire\Studio;

class AdminDataPanel
{
    use \Fire\Studio\Injector;

    private $_collection;
    private $_filter;

    public $data;
    public $singlerName;
    public $pluralName;
    public $currentPage;

    public function __construct($collectionName, $filter, $singularName, $pluralName)
    {
        $db = $this->injector()->get(Studio::INJECTOR_DATABASE);
        $this->_collection = $db->collection($collectionName);
        $this->_filter = $filter;
        $this->singularName = $singularName;
        $this->pluralName = $pluralName;
    }

    public function getModel($currentPage, $numPerPage)
    {
        $this->currentPage = $currentPage;
        $filter = json_decode($this->_filter);
        $filter->offset = ($currentPage - 1) * $numPerPage;
        $filter->length = $numPerPage;
        $this->data = $this->_collection->find(json_encode($filter));
        return $this;
    }

    private function _prepareTableData($data)
    {

    }

}