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
namespace Taxonomy\Options;

use Zend\Stdlib\AbstractOptions;
use Taxonomy\Exception;

class ModuleOptions extends AbstractOptions
{

    protected $types = [];

    /**
     *
     * @return array $types
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     *
     * @param string $type            
     * @throws Exception\RuntimeException
     * @return TaxonomyOptions
     */
    public function getType($type)
    {
        if (! array_key_exists($type, $this->types)) {
            throw new Exception\RuntimeException(sprintf('No configuration for type "%s" found.', $type));
        }
        
        if (! is_object($this->types[$type])) {
            $this->types[$type] = new TaxonomyOptions($this->types[$type]);
        }
        
        return $this->types[$type];
    }

    /**
     *
     * @param array $types            
     * @return self
     */
    public function setTypes(array $types)
    {
        $this->types = $types;
        return $this;
    }
}