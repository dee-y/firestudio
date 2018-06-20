<?php

namespace Fire\Studio\Application\Module\ApplicationModule\Controller;

use \Fire\Studio\Controller;
use \Fire\Studio\Application\Module\ApplicationModule;

class ApplicationController extends Controller {

    const TEMPLATE_APPLICATION_NOTFOUND = 'fire.studio.error.notFound';

    public function run()
    {
        $this->setLayout(ApplicationModule::TEMPLATE_APPLICATION_LAYOUT);
    }

    public function notFound()
    {
        $this->setResponceCode(404);
        $this->setPageTemplate(__DIR__ . '/../Template/error/not-found.phtml');
        echo $this->renderHtml();
    }

    public function notAuthorized()
    {
        $this->setResponceCode(401);
        $this->setPageTemplate(__DIR__ . '/../Template/error/not-authorized.phtml');
        echo $this->renderHtml();
    }

}
