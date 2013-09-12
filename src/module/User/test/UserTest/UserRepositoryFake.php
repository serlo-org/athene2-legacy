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
namespace UserTest;

use AtheneTest\Fake\EntityRepositoryFake;
use User\Entity\User;

class UserRepositoryFake extends EntityRepositoryFake
{
    protected $className = 'User\Entity\User';
    
    protected $data = array(
        array(
            'id' => 1,
            'username' => 'foobar',
            'email' => 'foo@bar.de',
        )
    );
}