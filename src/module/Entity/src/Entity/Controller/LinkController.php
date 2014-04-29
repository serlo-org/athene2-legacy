<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Entity\Controller;

use Entity\Form\MoveForm;
use Entity\Options\ModuleOptionsAwareTrait;
use Link\Service\LinkServiceAwareTrait;
use Zend\View\Model\ViewModel;

class LinkController extends AbstractController
{
    use LinkServiceAwareTrait, ModuleOptionsAwareTrait;

    public function moveAction()
    {
        $entity = $this->getEntity();
        if (!$entity) {
            return false;
        }
        $type = $this->params('type');
        $form = new MoveForm();
        $view = new ViewModel(['form' => $form]);

        $this->assertGranted('entity.link.create', $entity);
        $this->assertGranted('entity.link.purge', $entity);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                // todo this really should be done in a hydrator or similar
                $data        = $form->getData();
                $from        = $this->getEntityManager()->getEntity($this->params('from'));
                $to          = $this->getEntityManager()->getEntity($data['to']);
                $fromType    = $from->getType()->getName();
                $fromOptions = $this->getModuleOptions()->getType($fromType)->getComponent($type);
                $toType      = $to->getType()->getName();
                $toOptions   = $this->getModuleOptions()->getType($toType)->getComponent($type);

                $this->getLinkService()->dissociate($from, $entity, $fromOptions);
                $this->getLinkService()->associate($to, $entity, $toOptions);
                $this->getEntityManager()->getObjectManager()->flush();
                $this->flashMessenger()->addSuccessMessage('Your changes have been saved.');
                return $this->redirect()->toUrl($this->referer()->fromStorage());
            }
        } else {
            $this->referer()->store();
        }

        $view->setTemplate('entity/link/move');
        $this->layout('layout/1-col');
        return $view;
    }

    public function orderChildrenAction()
    {
        $entity = $this->getEntity();
        $this->assertGranted('entity.link.order', $entity);

        if ($this->getRequest()->isPost()) {
            $scope = $this->params('type');
            $data  = $this->params()->fromPost()['sortable'];
            $data  = $this->prepareDataForOrdering($data);

            $this->getLinkService()->sortChildren($entity, $scope, $data);
            $this->getEntityManager()->flush();
        }

        return false;
    }

    protected function prepareDataForOrdering($data)
    {
        $return = [];
        foreach ($data as $child) {
            $return[] = $child['id'];
        }
        return $return;
    }
}
