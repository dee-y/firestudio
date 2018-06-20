<?php

namespace Fire\Studio\Application\Module\ApplicationModule\Controller;

use \session_destroy;
use \Fire\Studio\Controller;
use \Fire\Studio\Form;
use \Fire\Studio\Application\Module\ApplicationModule;
use \Fire\Studio\Application\Module\AdminModule;
use \Fire\Studio\Application\Module\ApplicationModule\Service\UserAuth;
use \Fire\Studio\Application\Module\ApplicationModule\Model\User;
use \Fire\Studio;

class AuthController extends Controller
{
    private $_usersCollection;

    public function run()
    {
        $db = $this->injector()->get(Studio::INJECTOR_DATABASE);
        $this->_usersCollection = $db->collection(User::COLLECTION_FSUSERS);

        $this->setLayout(ApplicationModule::TEMPLATE_APPLICATION_LAYOUT);
    }

    public function register()
    {
        $this->setPageTemplate(__DIR__ . '/../Template/application/register.phtml');
        echo $this->renderHtml();
    }

    public function registerPOST()
    {
        $form = $this->getFormPost();
        $form->fieldValidation('userFullName', Form::VALIDATION_REQUIRED, 'The username is required.');
        $form->fieldValidation('userEmail', Form::VALIDATION_REQUIRED, 'The email address is required.');
        $form->fieldValidation('userEmail', Form::VALIDATION_EMAIL, 'The email address must be valid.');
        $form->fieldValidation('userPassword', Form::VALIDATION_REQUIRED, 'The password is required.');
        $form->fieldValidation('userPassword', [Form::VALIDATION_LENGTH_MIN, 8], 'The password must be at least 8 characters long.');

        if ($form->isValid()) {
            $isExistinUser = $this->_usersCollection->find('{"email":"' . strtolower($form->userEmail) . '"}');
            if ($isExistinUser) {
                $this->setSessionMessage('An account has already been created with that email address!');
            } else {
                $user = new User();
                $user->setName($form->userFullName);
                $user->setEmail(strtolower($form->userEmail));
                $user->setPassword($form->userPassword);
                $this->_usersCollection->insert($user);
                $this->setSessionMessage('Your acccount has been successfully created and you may now login!');
                $this->redirect(ApplicationModule::URL_LOGIN);
            }
        } else {
            $this->setSessionMessage('There were errors found while processing your new account information.');
        }
        $this->redirect(ApplicationModule::URL_REGISTER);
    }

    public function login()
    {
        $this->setPageTemplate(__DIR__ . '/../Template/application/login.phtml');
        echo $this->renderHtml();
    }

    public function loginPOST()
    {
        $form = $this->getFormPost();
        $form->fieldValidation('userNameEmail', Form::VALIDATION_REQUIRED, 'The email address is required.');
        $form->fieldValidation('userNameEmail', Form::VALIDATION_EMAIL, 'The email address must be valid.');
        $form->fieldValidation('userPassword', Form::VALIDATION_REQUIRED, 'The password is required.');
        if ($form->isValid()) {
            $userInfo = $this->_usersCollection->find('{"email":"' . strtolower($form->userNameEmail) . '"}');
            if ($userInfo) {
                $user = new User($userInfo[0]);
                $verifiedUser = $user->verifyPassword($form->userPassword);
                if ($verifiedUser) {
                    $_SESSION[UserAuth::SESSION_AUTHENTICATION_KEY] = $user->getData();
                    $this->redirect(AdminModule::URL_ADMIN_DASHBOARD);
                } else {
                    $this->setSessionMessage('Invalid email address or password.');
                }
            } else {
                $this->setSessionMessage('Invalid email address or password.');
            }
        } else {
            $this->setSessionMessage('There was an error processing your login.');
        }

        $this->redirect(ApplicationModule::URL_LOGIN);
    }

    public function logout()
    {
        session_destroy();
        $this->redirect(ApplicationModule::URL_LOGIN);
    }

}
