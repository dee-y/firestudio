<?php

namespace Fire\Studio\Application\Module\AdminModule\Controller;

use \Fire\Studio\Application\Module\AdminModule;
use \Fire\Studio\Application\Module\AdminModule\Controller\BaseController;
use \Fire\Studio\Application\Module\AdminModule\Controller\Helper\DynamicCollectionsHelper;
use \Fire\Studio;

class DynamicCollectionsController extends BaseController
{
    const ROUTE_VARIABLE_COLLECTION_ID = 'collectionSlug';
    const ROUTE_VARIABLE_OBJECT_ID = 'objectId';
    const STYLE_ADMIN_DYNAMIC_COLLECTIONS = 'admin.partial.adminDynamicCollections';

    private $_collectionConfig;
    private $_dynamicCollectionsHelper;

    public function run()
    {
        parent::run();
        $collectionSlug = $this->getParams(self::ROUTE_VARIABLE_COLLECTION_ID);
        $objId = $this->getParams(self::ROUTE_VARIABLE_OBJECT_ID);
        $config = $this->injector()->get(Studio::INJECTOR_CONFIG)->getConfig();

        if (
            $collectionSlug
            && isset($config->collections->{$collectionSlug})
        ) {
            $collectionConfig = $config->collections->{$collectionSlug};
            $this->model->title = 'FireStudio:Admin:' .  $collectionConfig->pluralName;
            $this->_setupDynamicCollectionsHelper($collectionConfig, $collectionSlug, $objId);
        } else {
            $this->_setupNotFound();
        }
    }

    public function index()
    {
        if ($this->_isPageFound()) {
            $page = $this->getGet('page');
            $currentPage = ($page) ? $page : 1;
            $this->model->dynamicCollection = $this->_dynamicCollectionsHelper
                ->getDynamicCollectionModel($currentPage, 10);

            $this->addInlineStyle(
                self::STYLE_ADMIN_DYNAMIC_COLLECTIONS,
                __DIR__ . '/../../AdminModule/Public/css/admin/dynamic-collections.css'
            );
            $this->setPageTemplate(__DIR__ . '/../Template/admin/dynamic-collections.phtml');
        }
        echo $this->renderHtml();
    }

    public function newObj()
    {
        if ($this->_isPageFound()) {
            $this->model->newObjForm = $this->_dynamicCollectionsHelper
                ->getNewObjFormModel();

            $this->setPageTemplate(__DIR__ . '/../Template/admin/dynamic-collections-new.phtml');
        }
        echo $this->renderHtml();
    }

    public function newObjPOST()
    {
        if ($this->_isPageFound()) {
            $collectionSlug = $this->_dynamicCollectionsHelper->getSlug();
            $singularName = $this->_dynamicCollectionsHelper->getSingularName();
            $pluralName = $this->_dynamicCollectionsHelper->getPluralName();
            $config = $this->injector()->get(Studio::INJECTOR_CONFIG)->getConfig();
            $collectionFieldsConfig = $config->collections->{$collectionSlug}->fields;
            $fieldsMap = (object) [];
            foreach ($collectionFieldsConfig as $field) {
                $fieldsMap->{$field->property} = $field;
            }

            $form = $this->getFormPost();
            $formFieldIds = $form->getFieldIds();
            foreach($formFieldIds as $fieldId) {
                $validations = $fieldsMap->{$fieldId}->validation;
                foreach($validations as $validation) {
                    $form->fieldValidation($fieldId, $validation->type, $validation->message);
                }
            }

            if ($form->isValid()) {
                $collection = $this->_dynamicCollectionsHelper->getCollection();
                $collection->insert($form);
                $sessionMessage = 'The new ' . $singularName . ' object was successfully added to your ' . 
                    $pluralName . ' collection.';
                $this->setSessionMessage($sessionMessage);
                $this->redirectToUrl($this->_dynamicCollectionsHelper->getCollectionUrl());
            } else {
                $sessionMessage = 'There was a problem adding the new ' . $singularName .
                    ' to your ' . $pluralName . ' collection.';
                $this->setSessionMessage($sessionMessage);
            }
        }
        $this->redirectToUrl($this->_dynamicCollectionsHelper->getNewObjUrl());
    }

    public function viewObj()
    {
        echo $this->renderHtml();
    }

    public function editObj()
    {
        echo $this->renderHtml();
    }

    public function deleteObj()
    {
        if ($this->_isPageFound()) {
            $this->model->deleteObj = $this->_dynamicCollectionsHelper
                ->getDeleteObjModel();
            $this->setPageTemplate(__DIR__ . '/../Template/admin/dynamic-collections-delete.phtml');
        }
        echo $this->renderHtml();
    }

    public function deleteObjPOST()
    {
        if ($this->_isPageFound()) {
            $collection = $this->_dynamicCollectionsHelper->getCollection();
            $form = $this->getFormPost();
            $collection->delete($form->objectId);
        }
        $this->redirectToUrl($this->_dynamicCollectionsHelper->getCollectionUrl());
    }

    private function _setupDynamicCollectionsHelper($collectionConfig, $urlSlug, $objId)
    {
        $this->_dynamicCollectionsHelper = new DynamicCollectionsHelper(
            $urlSlug,
            $objId,
            $collectionConfig->collectionName,
            $collectionConfig->singularName,
            $collectionConfig->pluralName,
            $collectionConfig->fields
        );
    }
}