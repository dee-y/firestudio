<?php

namespace Fire\Studio\Application\Module\AdminModule\Controller\Helper;

use Fire\Studio;
use Fire\Sql\Filter;

class AdminDataPanel
{
    use \Fire\Studio\Injector;

    private $_collection;
    private $_filter;
    private $_singularName;
    private $_pluralName;
    private $_fieldMapping;
    private $_actionLinks;

    public function __construct(
        $collectionName,
        $singularName,
        $pluralName,
        $fieldMapping,
        $actionLinks
    ) {
        $db = $this->injector()->get(Studio::INJECTOR_DATABASE);
        $this->_collection = $db->collection($collectionName);
        $this->_singularName = $singularName;
        $this->_pluralName = $pluralName;
        $this->_fieldMapping = $fieldMapping;
        $this->_actionLinks = $actionLinks;
    }

    public function getModel($currentPage, $numPerPage)
    {
        $filter = new Filter();
        $filter->offset(($currentPage - 1) * $numPerPage);
        $filter->length($numPerPage);
        $collectionData = $this->_collection->find($filter);

        $totalCount = $this->_collection->count();
        $pageCount = $totalCount/$numPerPage;
        $pageNav = $pageCount > 1;
        $previousPage = $currentPage - 1;
        $nextPage = $currentPage + 1;

        return (object) [
            'searchAvailable' => false,
            'singularName' => $this->_singularName,
            'pluralName' => $this->_pluralName,
            'pageNav' => $pageNav,
            'previousPage' => ($previousPage > 0) ? $previousPage : false,
            'currentPage' => $currentPage,
            'pageCount' => ceil($pageCount),
            'nextPage' => ($currentPage < $pageCount) ? $currentPage + 1 : false,
            'totalCount' => $totalCount,
            'hasTableData' => count($collectionData) > 0,
            'tableHeadings' => array_values($this->_getFieldMapping()),
            'table' => $this->_prepareTableData($collectionData)
        ];
    }

    private function _getFieldMapping()
    {
        return (array) $this->_fieldMapping;
    }

    private function _prepareTableData($collectionData)
    {
        $tableData = [];
        $fields = array_keys($this->_getFieldMapping());
        foreach ($collectionData as $obj) {
            $tableRow = [];
            foreach ($fields as $i => $field) {
                $data = isset($obj->{$field}) ? $obj->{$field} : false;
                if (is_array($data)) {
                    $data = implode(', ', $data);
                }
                $tableRow[$i] = (object) [
                    'value' => !empty($data) ? $data : 'none',
                    'hasActionLinks' => false
                ];

                if ($i === 0) {
                    $tableRow[$i]->hasActionLinks = true;
                    $tableRow[$i]->actionLinks = $this->_actionLinks;
                }
            }
            $tableData[] = $tableRow;
        }
        return $tableData;
    }

}