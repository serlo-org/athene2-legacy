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
namespace Ui\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Registry extends AbstractHelper
{

    protected $registry = array();

    public function __invoke()
    {
        return $this;
    }

    public function add($key, $value)
    {
        $this->registry[$key] = $value;
        return $this;
    }

    public function get($key)
    {
        if (array_key_exists($key, $this->registry)) {
            return $this->registry[$key];
        } else {
            return NULL;
        }
    }
}