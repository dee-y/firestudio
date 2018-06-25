<?php

namespace Fire\Studio\Application\Module\AdminModule\Controller;

use \Fire\Studio\Application\Module\AdminModule\Controller\BaseController;
use \Fire\Studio\Application\Module\AdminModule\Controller\Helper\AdminDataPanel;
use \Fire\Studio;

class DynamicCollectionsController extends BaseController
{
    const ROUTE_VARIABLE_COLLECTION_ID = 'collectionConfigId';

    private $_collectionConfig;
    private $_adminDataPanelHelper;

    public function run()
    {
        parent::run();
        $collectionConfigId = $this->getVariables(self::ROUTE_VARIABLE_COLLECTION_ID);
        $config = $this->injector()->get(Studio::INJECTOR_CONFIG)->getConfig();

        if (
            $collectionConfigId
            && isset($config->collections->{$collectionConfigId})
        ) {
            $this->setPageTemplate(__DIR__ . '/../Template/admin/collections.phtml');
            $collectionConfig = $config->collections->{$collectionConfigId};
            $this->_setupAdminDataPanelHelper($collectionConfig);
        } else {
            $this->_setupNotFound();
        }
    }

    public function index()
    {
        if ($this->_isPageFound()) {
            $page = $this->getGet('page');
            $currentPage = ($page) ? $page : 1;
            $this->model->adminDataPanel = $this->_adminDataPanelHelper->getModel($currentPage, 10);
        }
        echo $this->renderHtml();
    }

    public function view()
    {

    }

    public function edit()
    {

    }

    public function delete()
    {

    }

    private function _setupAdminDataPanelHelper($collectionConfig)
    {
        $fieldMapping = [];
        foreach ($collectionConfig->fields as $field) {
            if (isset($field->property) && isset($field->label)) {
                $fieldMapping[$field->property] = $field->label;
            }
        }

        $actionLinks = [
            (object) [
                'label' => 'View',
                'url' => 'application.admin.users.view'
            ],
            (object) [
                'label' => 'Edit',
                'url' => 'application.admin.users.edit'
            ],
            (object) [
                'label' => 'Delete',
                'url' => 'application.admin.users.delete'
            ]
        ];

        $this->_adminDataPanelHelper = new AdminDataPanel(
            $collectionConfig->collectionName,
            $collectionConfig->singularName,
            $collectionConfig->pluralName,
            $fieldMapping,
            $actionLinks
        );
    }
}