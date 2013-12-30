<?php
/**
 * 
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author	    Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license	    LGPL-3.0
 * @license	    http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link		https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright	Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Authorization\Service;

use ZfcRbac\Exception\UnauthorizedException;
trait AuthorizationAssertionTrait
{
    use AuthorizationServiceAwareTrait;
    
    /**
     * Assert that access is granted
     * 
     * @param string $permission
     * @param object $assertionOrObject
     * @throws UnauthorizedException
     * @return boolean
     */
    protected function assertGranted($permission, $assertionOrObject = NULL){
        if(! $this->getAuthorizationService()->isGranted($permission, $assertionOrObject)){
            throw new UnauthorizedException();
        }
        
        return true;
    }
}