<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author    Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license   LGPL-3.0
 * @license   http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link      https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013-2014 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Authentication\Controller;

use Authentication\Form\ActivateForm;
use Authorization\Service\RoleServiceAwareTrait;
use Authorization\Service\RoleServiceInterface;
use Common\Traits\AuthenticationServiceAwareTrait;
use User\Exception\UserNotFoundException;
use User\Form\ChangePasswordForm;
use User\Form\Login;
use User\Form\LostPassword;
use User\Form\SelectUserForm;
use User\Manager\UserManagerAwareTrait;
use User\Manager\UserManagerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AuthenticationController extends AbstractActionController
{
    use AuthenticationServiceAwareTrait, UserManagerAwareTrait;
    use RoleServiceAwareTrait;

    public function __construct(
        AuthenticationService $authenticationService,
        RoleServiceInterface $roleService,
        UserManagerInterface $userManager
    ) {
        $this->authenticationService = $authenticationService;
        $this->userManager           = $userManager;
        $this->roleService           = $roleService;
    }

    public function activateAction()
    {
        if ($this->authenticationService->getIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        if ($this->params('token', false)) {
            try {
                $user = $this->getUserManager()->findUserByToken($this->params('token'));
                $role = $this->getRoleService()->findRoleByName('login');

                if (!$user->hasRole($role)) {
                    $user->addRole($role);
                }

                $this->getUserManager()->generateUserToken($user->getId());
                $this->getUserManager()->flush();
                $this->flashMessenger()->addSuccessMessage('Your account has been activated, you may now log in.');

                return $this->redirect()->toRoute('authentication/login');
            } catch (UserNotFoundException $e) {
                $this->flashMessenger()->addErrorMessage(
                    'I couldn\'t find an account by that token. You can now try to re-activate your account.'
                );

                return $this->redirect()->toRoute('authentication/activate');
            }
        } else {
            $form     = new ActivateForm();
            $messages = [];

            if ($this->getRequest()->isPost()) {
                $post = $this->params()->fromPost();
                $form->setData($post);
                if ($form->isValid()) {
                    $data = $form->getData();
                    try {
                        $user = $this->getUserManager()->findUserByEmail($data['email']);
                        $this->getEventManager()->trigger('activate', $this, ['user' => $user]);
                        $this->flashMessenger()->addSuccessMessage(
                            'Your have been sent an activation email. Please check your spam folder as well.'
                        );

                        return $this->redirect()->toRoute('authentication/login');
                    } catch (UserNotFoundException $e) {
                        $messages[] = 'No such user could be found.';
                    }
                }
            }

            $view = new ViewModel([
                'form'     => $form,
                'messages' => $messages
            ]);
            $view->setTemplate('authentication/activate');

            return $view;
        }
    }

    public function changePasswordAction()
    {
        $form     = new ChangePasswordForm();
        $user     = $this->getUserManager()->getUserFromAuthenticator();
        $messages = [];

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data    = $form->getData();
                $adapter = $this->getAuthenticationService()->getAdapter();
                $adapter->setIdentity($user->getEmail());
                $adapter->setCredential($data['currentPassword']);
                $result = $adapter->authenticate();
                if ($result->isValid()) {
                    $user->setPassword($data['password']);
                    $this->getUserManager()->persist($user);
                    $this->getUserManager()->flush();
                    $this->flashmessenger()->addSuccessMessage('Your password has successfully been changed.');
                    return $this->redirect()->toRoute('user/me');
                }
                $messages = $result->getMessages();
            }
        }

        $view = new ViewModel([
            'user'     => $user,
            'form'     => $form,
            'messages' => $messages
        ]);

        $this->layout('layout/1-col');
        $view->setTemplate('authentication/change-password');

        return $view;
    }

    public function loginAction()
    {
        if ($this->authenticationService->getIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        $form     = new Login();
        $messages = [];

        $this->layout('layout/1-col');

        if ($this->getRequest()->isPost()) {

            $post = $this->params()->fromPost();
            $form->setData($post);

            if ($form->isValid()) {
                $data    = $form->getData();
                $adapter = $this->getAuthenticationService()->getAdapter();
                $storage = $this->getAuthenticationService()->getStorage();

                $adapter->setIdentity($data['email']);
                $adapter->setCredential($data['password']);
                $storage->setRememberMe($data['remember']);

                $result = $this->getAuthenticationService()->authenticate();

                if ($result->isValid()) {
                    $user = $this->getUserManager()->getUser($result->getIdentity()->getId());

                    $user->updateLoginData();
                    $this->getUserManager()->persist($user);
                    $this->getUserManager()->flush();

                    $url = $this->params()->fromQuery('redir', $this->referer()->fromStorage());

                    return $this->redirect()->toUrl($url);
                }
                $messages = $result->getMessages();
            }
        } else {
            $this->referer()->store();
        }

        $view = new ViewModel([
            'form'          => $form,
            'errorMessages' => $messages
        ]);

        $view->setTemplate('authentication/login');

        return $view;
    }

    public function logoutAction()
    {
        $storage = $this->getAuthenticationService()->getStorage();
        $storage->forgetMe();
        $this->getAuthenticationService()->clearIdentity();
        return $this->redirect()->toReferer();
    }

    public function restorePasswordAction()
    {
        if ($this->authenticationService->getIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        $messages = [];
        $view     = new ViewModel();

        $this->layout('layout/1-col');

        if (!$this->params('token', false)) {
            $form = new SelectUserForm();

            $view->setTemplate('authentication/reset-password/select');

            if ($this->getRequest()->isPost()) {
                $data = $this->params()->fromPost();
                $form->setData($data);
                if ($form->isValid()) {
                    try {
                        $user = $this->getUserManager()->findUserByEmail($data['email']);

                        $this->getUserManager()->generateUserToken($user->getId());
                        $this->getEventManager()->trigger('restore-password', $this, ['user' => $user]);
                        $this->getUserManager()->flush();
                        $this->flashmessenger()->addSuccessMessage(
                            'You have been sent an email with instructions on how to restore your password! Please check your spam folder as well.'
                        );

                        return $this->redirect()->toRoute('home');
                    } catch (UserNotFoundException $e) {
                        $messages[] = 'Sorry, this email address does not seem to be registered yet.';
                    }
                }
            }
        } else {
            $form  = new LostPassword();
            $token = $this->params('token');
            $url   = $this->url()->fromRoute('authentication/password/restore', ['token' => $token]);
            $user  = $this->getUserManager()->findUserByToken($token);

            $view->setTemplate('authentication/reset-password/restore');
            $form->setAttribute('action', $url);

            if ($this->getRequest()->isPost()) {
                $data = $this->params()->fromPost();
                $form->setData($data);
                if ($form->isValid()) {
                    $data = $form->getData();

                    $this->getUserManager()->updateUserPassword($user->getId(), $data['password']);
                    $this->getUserManager()->flush();

                    return $this->redirect()->toRoute('authentication/login');
                }
            }
        }

        $view->setVariable('form', $form);
        $view->setVariable('messages', $messages);

        return $view;
    }
}
