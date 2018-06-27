<?php

namespace Fire\Studio\Application\Module\AdminModule\Controller\Helper;

use \Fire\Studio;
use \Fire\Sql\Filter;
use \Fire\Studio\ControllerHelper;
use \Fire\Studio\Application\Module\AdminModule;
use \Fire\Studio\Application\Module\AdminModule\Controller\DynamicCollectionsController;

class DynamicCollectionsHelper extends ControllerHelper
{
    use \Fire\Studio\Injector;

    const PARTIAL_FORM_TEXT_INPUT = 'admin.form.textInput';

    private $_collection;
    private $_slug;
    private $_id;
    private $_collectionName;
    private $_singularName;
    private $_pluralName;
    private $_fields;
    private $_collectionUrl;
    private $_newObjUrl;
    private $_viewObjUrl;
    private $_editObjUrl;
    private $_deleteObjUrl;

    public function __construct(
        $urlSlug,
        $objId,
        $collectionName,
        $singularName,
        $pluralName,
        $fields
    ) {
        $db = $this->injector()->get(Studio::INJECTOR_DATABASE);
        $this->_slug = $urlSlug;
        $this->_id = $objId;
        $this->_collection = $db->collection($collectionName);
        $this->_collectionName = $collectionName;
        $this->_singularName = $singularName;
        $this->_pluralName = $pluralName;
        $this->_fields = $fields;
        $this->_setupUrls();
        $this->_loadFormPartials();
    }

    public function getCollection()
    {
        return $this->_collection;
    }

    public function getSlug()
    {
        return $this->_slug;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getCollectionUrl()
    {
        return $this->_collectionUrl;
    }

    public function getNewObjUrl()
    {
        return $this->_newObjUrl;
    }

    public function getCollectionName()
    {
        return $this->_collectionName;
    }

    public function getSingularName()
    {
        return $this->_singularName;
    }

    public function getPluralName()
    {
        return $this->_pluralName;
    }

    public function getDynamicCollectionModel($currentPage, $numPerPage)
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

        $fieldMapping = $this->_getFieldMapping();

        return (object) [
            'searchAvailable' => false,
            'singularName' => $this->_singularName,
            'pluralName' => $this->_pluralName,
            'newObjectUrl' => $this->_newObjUrl,
            'viewObjectUrl' => $this->_viewObjUrl,
            'editObjectUrl' => $this->_editObjUrl,
            'deleteObjectUrl' => $this->_deleteObjUrl,
            'pageNav' => $pageNav,
            'previousPage' => ($previousPage > 0) ? $previousPage : false,
            'currentPage' => $currentPage,
            'pageCount' => ceil($pageCount),
            'nextPage' => ($currentPage < $pageCount) ? $currentPage + 1 : false,
            'numCountName' => ($totalCount > 1) ? $this->_pluralName : $this->_singularName,
            'totalCount' => $totalCount,
            'hasTableData' => count($collectionData) > 0,
            'tableHeadings' => array_values($fieldMapping),
            'table' => $this->_prepareTableData($collectionData)
        ];
    }

    public function getNewObjFormModel()
    {
        return (object) [
            'singularName' => $this->_singularName,
            'pluralName' => $this->_pluralName,
            'collectionUrl' => $this->_collectionUrl,
            'fields' => $this->_prepareFormFields(true)
        ];
    }

    public function getDeleteObjModel()
    {
        return (object) [
            'singularName' => $this->_singularName,
            'collectionsUrl' => $this->_collectionUrl,
            'id' => $this->_id
        ];
    }

    private function _setupUrls()
    {
        $router = $this->injector()->get(Studio::INJECTOR_ROUTER);
        $this->_collectionUrl = $router->getUrl(
            AdminModule::URL_DYNAMIC_COLLECTIONS,
            [
                DynamicCollectionsController::ROUTE_VARIABLE_COLLECTION_ID => $this->_slug
            ]
        );
        $this->_newObjUrl = $router->getUrl(
            AdminModule::URL_DYNAMIC_COLLECTIONS_NEW,
            [
                DynamicCollectionsController::ROUTE_VARIABLE_COLLECTION_ID => $this->_slug
            ]
        );
        $this->_viewObjUrl = $router->getUrl(
            AdminModule::URL_DYNAMIC_COLLECTIONS_VIEW,
            [
                DynamicCollectionsController::ROUTE_VARIABLE_COLLECTION_ID => $this->_slug,
                DynamicCollectionsController::ROUTE_VARIABLE_OBJECT_ID => $this->_id
            ]
        );
        $this->_editObjUrl = $router->getUrl(
            AdminModule::URL_DYNAMIC_COLLECTIONS_EDIT,
            [
                DynamicCollectionsController::ROUTE_VARIABLE_COLLECTION_ID => $this->_slug,
                DynamicCollectionsController::ROUTE_VARIABLE_OBJECT_ID => $this->_id
            ]
        );
        $this->_deleteObjUrl = $router->getUrl(
            AdminModule::URL_DYNAMIC_COLLECTIONS_DELETE,
            [
                DynamicCollectionsController::ROUTE_VARIABLE_COLLECTION_ID => $this->_slug,
                DynamicCollectionsController::ROUTE_VARIABLE_OBJECT_ID => $this->_id
            ]
        );
    }

    private function _loadFormPartials()
    {
        $this->loadPartial(
            self::PARTIAL_FORM_TEXT_INPUT,
            __DIR__ . '/../../Template/admin/form/text-input.phtml'
        );
    }

    private function _getFieldMapping()
    {
        $fieldMapping = [];
        foreach ($this->_fields as $field) {
            if (
                isset($field->property)
                && isset($field->label)
                && $field->displayOnTable
            ) {
                $fieldMapping[$field->property] = $field->label;
            }
        }
        return $fieldMapping;
    }

    private function _prepareTableData($collectionData)
    {
        $router = $this->injector()->get(Studio::INJECTOR_ROUTER);
        $tableData = [];
        foreach ($collectionData as $obj) {
            $tableRow = [];
            $firstRow = true;
            $i = 0;
            foreach ($this->_fields as $field) {
                if ($field->displayOnTable) {
                    $data = isset($obj->{$field->property}) ? $obj->{$field->property} : false;
                    if (is_array($data)) {
                        $data = implode(', ', $data);
                    }
                    $tableRow[$i] = (object) [
                        'value' => !empty($data) ? $data : 'none',
                        'actionLinks' => false
                    ];

                    if ($i === 0) {
                        $tableRow[$i]->actionLinks = (object) [
                            'view' => $router->getUrl(
                                AdminModule::URL_DYNAMIC_COLLECTIONS_VIEW,
                                [
                                    DynamicCollectionsController::ROUTE_VARIABLE_COLLECTION_ID => $this->_slug,
                                    DynamicCollectionsController::ROUTE_VARIABLE_OBJECT_ID => $obj->__id
                                ]
                            ),
                            'edit' => $router->getUrl(
                                AdminModule::URL_DYNAMIC_COLLECTIONS_EDIT,
                                [
                                    DynamicCollectionsController::ROUTE_VARIABLE_COLLECTION_ID => $this->_slug,
                                    DynamicCollectionsController::ROUTE_VARIABLE_OBJECT_ID => $obj->__id
                                ]
                            ),
                            'delete' => $router->getUrl(
                                AdminModule::URL_DYNAMIC_COLLECTIONS_DELETE,
                                [
                                    DynamicCollectionsController::ROUTE_VARIABLE_COLLECTION_ID => $this->_slug,
                                    DynamicCollectionsController::ROUTE_VARIABLE_OBJECT_ID => $obj->__id
                                ]
                            )
                        ];
                    }
                    $i++;
                }
            }
            $tableData[] = $tableRow;
        }
        return $tableData;
    }

    private function _prepareFormFields($isForForm = false)
    {
        $fieldValues = $this->getSessionForm();
        $fields = [];
        foreach ($this->_fields as $field) {
            $field->value = isset($fieldValues->{$field->property})
                ? $fieldValues->{$field->property} : '';
            if ($isForForm && $field->displayOnForm) {
                $fields[] = $this->_renderField($field);
            }
        }
        return $fields;
    }

    private function _renderField($field)
    {
        switch($field->type) {
            case 'text':
                return $this->renderPartial(
                    self::PARTIAL_FORM_TEXT_INPUT,
                    $field
                );
                break;
        }
    }

}