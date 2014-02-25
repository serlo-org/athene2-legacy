<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author    Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license   LGPL-3.0
 * @license   http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link      https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace User\Controller;

use Instance\Manager\InstanceManagerAwareTrait;
use Zend\Form\Form;
use Zend\View\Model\ViewModel;
use ZfcRbac\Exception\UnauthorizedException;

class UserController extends AbstractUserController
{
    use \Common\Traits\ConfigAwareTrait;
    use InstanceManagerAwareTrait;

    protected $forms = [];

    public function meAction()
    {
        $user = $this->getUserManager()->getUserFromAuthenticator();

        if (!$user) {
            throw new UnauthorizedException;
        }

        $view = new ViewModel([
            'user' => $user
        ]);

        $this->layout('layout/1-col');
        $view->setTemplate('user/user/profile');

        return $view;
    }

    public function profileAction()
    {
        $user = $this->getUserManager()->getUser($this->params('id'));
        $view = new ViewModel([
            'user' => $user
        ]);
        $this->layout('layout/1-col');
        $view->setTemplate('user/user/profile');

        return $view;
    }

    public function registerAction()
    {
        if (!$this->isGranted('user.create')) {
            $this->redirect()->toReferer();
        }

        $this->layout('layout/1-col');

        $form = $this->getForm('register');

        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $user = $this->getUserManager()->createUser($form->getData());

                $this->getEventManager()->trigger(
                    'register',
                    $this,
                    [
                        'user'     => $user,
                        'instance' => $this->getInstanceManager()->getInstanceFromRequest(),
                        'data'     => $data
                    ]
                );

                $this->getUserManager()->persist($user);
                $this->getUserManager()->flush();

                $this->redirect()->toUrl($this->params('ref', '/'));

                return false;
            }
        }

        $view = new ViewModel([
            'form' => $form
        ]);

        return $view;
    }

    public function getForm($name)
    {
        if (!array_key_exists($name, $this->forms)) {
            $form = $this->getOption('forms')[$name];
            if ($name == 'register' || $name = 'settings') {
                $this->forms[$name] = new $form($this->getUserManager()->getObjectManager());
            } else {
                $this->forms[$name] = new $form();
            }
        }

        return $this->forms[$name];
    }

    public function removeAction()
    {
        // todo: make sure this doesn't get abused and remove exception
        throw new \Exception();

        $user = $this->getUserManager()->getUser($this->params('id', null));
        $user->setTrashed(true);

        $this->getUserManager()->persist($user);
        $this->getUserManager()->flush();
        $this->redirect()->toReferer();

        return false;
    }

    /**
     * @param string $name
     * @param Form   $form
     * @return self
     */
    public function setForm($name, Form $form)
    {
        $this->forms[$name] = $form;

        return $this;
    }

    public function settingsAction()
    {
        $form = $this->getForm('settings');
        $user = $this->getUserManager()->getUserFromAuthenticator();
        if (!$user) {
            throw new UnauthorizedException;
        }

        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();
                $user->setEmail($data['email']);

                $this->getUserManager()->persist($user);
                $this->getUserManager()->flush();
            }
        } else {
            $data = ['email' => $user->getEmail()];
            $form->setData($data);
        }

        $view = new ViewModel(['user' => $user, 'form' => $form]);
        $view->setTemplate('user/user/settings');
        $this->layout('layout/1-col');

        return $view;
    }

    protected function getDefaultConfig()
    {
        return [
            'forms' => [
                'register'         => 'User\Form\Register',
                'login'            => 'User\Form\Login',
                'user_select'      => 'User\Form\SelectUserForm',
                'restore_password' => 'User\Form\LostPassword',
                'settings'         => 'User\Form\SettingsForm'
            ]
        ];
    }
}
