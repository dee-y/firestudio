<?php

namespace Fire\Studio\Application\Module\AdminModule\Controller;

use \Fire\Studio\Controller;
use \Fire\Studio\Application\Module\AdminModule;

abstract class AbstractAdminController extends Controller
{

    public function init()
    {
        $this->model->title = 'FireStudio Admin';
        $this->setLayout(AdminModule::TEMPLATE_ADMIN_LAYOUT);
    }

    public function setPageTemplate($partialId)
    {
        $this->loadPartial(AdminModule::PARTIAL_ADMIN_PAGE, $partialId);
    }

}
