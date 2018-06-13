<?php

namespace Fire\Studio\Application\Module\Application\Controller;

use \Fire\Studio\Controller;
use \Fire\Studio\Application\Module\Application as ApplicationModule;

class Application extends Controller {

    const TEMPLATE_APPLICATION_NOTFOUND = 'fire.studio.error.notFound';

    public function notFound()
    {
        $this->loadTemplate(
            self::TEMPLATE_APPLICATION_NOTFOUND,
            __DIR__ . '/../Template/error/not-found.phtml'
        );
        echo $this->render(ApplicationModule::TEMPLATE_APPLICATION_LAYOUT);
    }

}
