<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Taxonomy\Controller;

use Zend\View\Model\ViewModel;
use ZfcRbac\Exception\UnauthorizedException;

class TermController extends AbstractController
{
    public function createAction()
    {
        $this->assertGranted('taxonomy.term.create');
        $form = $this->termForm;

        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $data = array_merge(
                $data,
                [
                    'taxonomy' => $this->params('taxonomy'),
                    'parent'   => $this->params('parent', null)

                ]
            );
            $form->setData($data);
            if ($form->isValid()) {
                $this->getTaxonomyManager()->createTerm($form);
                $this->getTaxonomyManager()->flush();
                $this->flashMessenger()->addSuccessMessage('The node has been added successfully!');
                return $this->redirect()->toUrl($this->referer()->fromStorage());
            }
        } else {
            $this->referer()->store();
        }
        $view = new ViewModel(['form' => $form, 'isUpdating' => false]);
        $this->layout('editor/layout');
        $view->setTemplate('taxonomy/term/create');
        return $view;
    }

    public function orderAction()
    {
        $term = $this->getTaxonomyManager()->getTerm($this->params('term'));
        $this->assertGranted('taxonomy.term.update', $term);
        $data = $this->params()->fromPost('sortable', []);
        $this->iterWeight($data, $this->params('term'));
        $this->getTaxonomyManager()->flush();
        return true;
    }

    public function orderAssociatedAction()
    {
        $association = $this->params('association');
        $term        = $this->getTerm($this->params('term'));
        $this->assertGranted('taxonomy.term.associated.sort', $term);

        if ($this->getRequest()->isPost()) {
            $associations = $this->params()->fromPost('sortable', []);
            $i            = 0;

            foreach ($associations as $a) {
                $term->positionAssociatedObject($a['id'], $i, $association);
                $i++;
            }

            $this->getTaxonomyManager()->flush();

            return true;
        }

        $associations = $term->getAssociated($association);
        $view         = new ViewModel([
            'term' => $term,
            'associations' => $associations,
            'association' => $association
        ]);
        $view->setTemplate('taxonomy/term/order-associated');
        return $view;
    }

    public function organizeAction()
    {
        $term = $this->getTerm();
        if ($this->assertGranted('taxonomy.term.create', $term) || $this->assertGranted(
                'taxonomy.term.update',
                $term
            )
        ) {
            throw new UnauthorizedException;
        }
        $view = new ViewModel(['term' => $term]);
        $view->setTemplate('taxonomy/term/organize');
        return $view;
    }

    public function updateAction()
    {
        $term = $this->getTerm();
        $form = $this->termForm;
        $this->assertGranted('taxonomy.term.update', $term);
        $form->bind($term);

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $form->setData($post);
            if ($form->isValid()) {
                $this->getTaxonomyManager()->updateTerm($form);
                $this->getTaxonomyManager()->flush();
                $this->flashMessenger()->addSuccessMessage('Your changes have been saved!');
                return $this->redirect()->toUrl($this->referer()->fromStorage());
            }
        } else {
            $this->referer()->store();
        }

        $view = new ViewModel(['form' => $form]);
        $this->layout('editor/layout');
        $view->setTemplate('taxonomy/term/update');
        return $view;
    }

    protected function iterWeight($terms, $parent = null)
    {
        $position = 1;
        $form     = $this->termForm;
        foreach ($terms as $term) {
            $entity = $this->getTaxonomyManager()->getTerm($term['id']);
            $data   = $form->getHydrator()->extract($entity);
            $data   = array_merge($data, ['parent' => $parent, 'position' => $position]);
            $form->bind($entity);
            $form->setData($data);
            $this->getTaxonomyManager()->updateTerm($form);
            if (isset($term['children'])) {
                $this->iterWeight($term['children'], $term['id']);
            }
            $position++;
        }
        return true;
    }
}
