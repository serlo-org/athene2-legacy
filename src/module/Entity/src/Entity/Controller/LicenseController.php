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

use Instance\Manager\InstanceManagerAwareTrait;
use License\Form\UpdateLicenseForm;
use License\Manager\LicenseManagerAwareTrait;
use Zend\View\Model\ViewModel;

class LicenseController extends AbstractController
{
    use LicenseManagerAwareTrait;

    public function updateAction()
    {
        $licenses = $this->getLicenseManager()->findAllLicenses();
        $entity   = $this->getEntity();

        if (!$entity || $entity->isTrashed()) {
            $this->getResponse()->setStatusCode(404);
            return false;
        }

        $this->assertGranted('entity.license.update', $entity);

        $form = new UpdateLicenseForm($licenses);
        $view = new ViewModel(['form' => $form]);
        $view->setTemplate('entity/license/update');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data    = $form->getData();
                $license = $this->getLicenseManager()->getLicense((int)$data['license']);
                $this->getLicenseManager()->injectLicense($entity, $license);
                $this->getLicenseManager()->flush();
                return $this->redirect()->toUrl($this->referer()->fromStorage());
            }
        } else {
            $this->referer()->store();
        }

        return $view;
    }
}
