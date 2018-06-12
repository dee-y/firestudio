<?php

namespace Fire\Studio\Application\Module\Admin\Controller;

use \Fire\Studio\Controller;
use \Fire\Studio\Application\Module\Admin as AdminModule;

class Admin extends Controller {

    const TEMPLATE_ADMIN_DASHBOARD = 'fire.studio.admin.dashboard';

    public function dashboard()
    {
        $this->loadTemplate(
            self::TEMPLATE_ADMIN_DASHBOARD,
            __DIR__ . '/../Template/admin/dashboard.phtml'
        );
        echo $this->render(
            AdminModule::TEMPLATE_ADMIN_LAYOUT,
            [

            ]
        );
    }

}
