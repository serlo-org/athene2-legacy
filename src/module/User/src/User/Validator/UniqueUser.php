<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace User\Validator;

use DoctrineModule\Validator\UniqueObject;

class UniqueUser extends UniqueObject
{
    /**
     * (non-PHPdoc)
     *
     * @see \DoctrineModule\Validator\UniqueObject::isValid()
     */
    public function isValid($value, $context = null)
    {
        $value = $this->cleanSearchValue($value);
        $match = $this->objectRepository->findOneBy($value);

        if (!is_object($match)) {
            return true;
        }

        /*$expectedIdentifiers = $this->getExpectedIdentifiers($context);
        $foundIdentifiers    = $this->getFoundIdentifiers($match);
    
        if (count(array_diff_assoc($expectedIdentifiers, $foundIdentifiers)) == 0) {
            return true;
        }*/

        $this->error(self::ERROR_OBJECT_NOT_UNIQUE, $value);
        return false;
    }
}
