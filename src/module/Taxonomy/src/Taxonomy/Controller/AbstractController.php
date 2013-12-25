<?php
/**
 * 
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author	Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license	LGPL-3.0
 * @license	http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link		https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Taxonomy\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class AbstractController extends AbstractActionController
{
    use \Taxonomy\Manager\TaxonomyManagerAwareTrait;
    use\Language\Manager\LanguageManagerAwareTrait;
    
    protected function getTerm($id = NULL){
        if($id === NULL){
            if($this->params('id', NULL) === NULL) {
                return $this->getTaxonomyManager()->findTaxonomyByName('root', $this->getLanguageManager()->getLanguageFromRequest())->getChildren()->first();            
            } else {
                return $this->getTaxonomyManager()->getTerm($this->params('id'));
            }
        } else {
            return $this->getTaxonomyManager()->getTerm($id);            
        }
    }
}