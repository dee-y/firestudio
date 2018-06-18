<?php

namespace Fire\Studio\Application\Module\ApplicationModule\Controller;

use \session_destroy;
use \Fire\Studio\Controller;
use \Fire\Studio\Application\Module\ApplicationModule;

class AuthController extends Controller
{

    public function run()
    {
        $this->setLayout(ApplicationModule::TEMPLATE_APPLICATION_LAYOUT);
    }

    public function register()
    {
        $this->setPageTemplate(__DIR__ . '/../Template/application/register.phtml');
        echo $this->renderHtml();
    }

    public function registerPOST()
    {
        debugger($this->getPost());
        $form = $this->getFormPost();
        $form->mapFieldRules('userFullName', ['required']);
        //$form->rule('required', 'userFullName');
        $form->rule('required', 'userEmail');
        $form->rule('email', 'userEmail');
        $form->rule('required', 'userPassword');
        $form->rule('length', 'userPassword', 8);

        debugger($form->validate());
        debugger($form->errors());
        echo $this->renderDebugPanel();
    }

    public function login()
    {
        $this->setPageTemplate(__DIR__ . '/../Template/application/login.phtml');
        echo $this->renderHtml();
    }

    public function loginPOST()
    {
        debugger($this->getPost());
        echo 'login attempt';
        echo $this->renderDebugPanel();
    }

    public function logout()
    {
        session_destroy();
        $this->redirect(ApplicationModule::URL_LOGIN);
    }

    public function unauthorized()
    {

    }

}
