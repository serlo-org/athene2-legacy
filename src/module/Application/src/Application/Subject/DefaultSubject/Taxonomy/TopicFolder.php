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
namespace Application\Subject\DefaultSubject\Taxonomy;

use Application\Taxonomy\Factory\EntityTaxonomy;
use Taxonomy\Service\TermServiceInterface;

class TopicFolder extends EntityTaxonomy
{
    /*
     * (non-PHPdoc) @see \Taxonomy\Factory\FactoryInterface::build()
    */
    public function build (TermServiceInterface $termService)
    {
        $instance = parent::build($termService);
        $instance->disableRadix();
        $instance->allowParentNodesByTaxonomy(1);
        $instance->allowParentNodesByTaxonomy(2);
        return $instance;
    }
}