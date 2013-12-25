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
namespace Alias\Options;

use Zend\Stdlib\AbstractOptions;

class ManagerOptions extends AbstractOptions
{

    protected $aliases = [];

    /**
     *
     * @return array $aliases
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     *
     * @param array $aliases            
     * @return self
     */
    public function setAliases(array $aliases)
    {
        $this->aliases = $aliases;
        return $this;
    }
}