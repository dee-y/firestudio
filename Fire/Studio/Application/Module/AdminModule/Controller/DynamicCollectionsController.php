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
            $config = $this->injector()->get(Studio::INJECTOR_CONFIG)->getConfig();
            $collectionSlug = $this->_dynamicCollectionsHelper->getSlug();
            $singularName = $this->_dynamicCollectionsHelper->getSingularName();
            $collectionName = $this->_dynamicCollectionsHelper->getCollectionName();
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
                $obj = $this->_dynamicCollectionsHelper->upsertObject($form);
                $sessionMessage = 'The new ' . $singularName . ' object was successfully added to the ' .
                    $collectionName . ' collection and was assigned ID ' . $obj->__id . '.';
                $this->setSessionMessage($sessionMessage);
                $this->redirectToUrl($this->_dynamicCollectionsHelper->getCollectionUrl());
            } else {
                $sessionMessage = 'There was a problem adding the new ' . $singularName .
                    ' to the ' . $collectionName . ' collection.';
                $this->setSessionMessage($sessionMessage);
            }
        }
        $this->redirectToUrl($this->_dynamicCollectionsHelper->getNewObjUrl());
    }

    public function viewObj()
    {
        if ($this->_isPageFound()) {
            $this->model->viewObj = $this->_dynamicCollectionsHelper
                ->getViewObjModel();


            $this->addInlineStyle(
                self::STYLE_ADMIN_DYNAMIC_COLLECTIONS,
                __DIR__ . '/../../AdminModule/Public/css/admin/dynamic-collections.css'
            );
            $this->setPageTemplate(__DIR__ . '/../Template/admin/dynamic-collections-view.phtml');
        }
        echo $this->renderHtml();
    }

    public function editObj()
    {
        if ($this->_isPageFound()) {
            $this->model->editObjForm = $this->_dynamicCollectionsHelper
                ->getEditObjFormModel();
            $this->setPageTemplate(__DIR__ . '/../Template/admin/dynamic-collections-edit.phtml');
        }
        echo $this->renderHtml();
    }

    public function editObjPOST()
    {
        if ($this->_isPageFound()) {
            $config = $this->injector()->get(Studio::INJECTOR_CONFIG)->getConfig();
            $collectionSlug = $this->_dynamicCollectionsHelper->getSlug();
            $singularName = $this->_dynamicCollectionsHelper->getSingularName();
            $collectionName = $this->_dynamicCollectionsHelper->getCollectionName();
            $id = $this->_dynamicCollectionsHelper->getId();
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
                $this->_dynamicCollectionsHelper->upsertObject($form, $id);
                $sessionMessage = 'The ' . $singularName . ' object ' . $id . ' was successfully updated in the ' .
                    $collectionName . ' collection.';
                $this->setSessionMessage($sessionMessage);
                $this->redirectToUrl($this->_dynamicCollectionsHelper->getCollectionUrl());
            } else {
                $sessionMessage = 'There was a problem editing the ' . $singularName .
                    ' object ' . $id . ' in the ' . $collectionName . ' collection.';
                $this->setSessionMessage($sessionMessage);
            }
        }
        $this->redirectToUrl($this->_dynamicCollectionsHelper->getEditObjUrl());
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
            $collectionName = $this->_dynamicCollectionsHelper->getCollectionName();
            $singularName = $this->_dynamicCollectionsHelper->getSingularName();
            $collection = $this->_dynamicCollectionsHelper->getCollection();
            $form = $this->getFormPost();
            $collection->delete($form->objectId);
            $this->setSessionMessage('The ' . $singularName . ' object ' . $form->objectId . ' was successfully deleted from your ' .
                $collectionName . ' collection.');
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