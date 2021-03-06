<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace User\Manager;

use Common\ObjectManager\Flushable;
use Doctrine\ORM\EntityRepository;
use User\Entity\UserInterface;
use Zend\Paginator\Paginator;

interface UserManagerInterface extends Flushable
{

    /**
     * @param array $data
     * @return UserInterface
     */
    public function createUser(array $data);

    /**
     * @param int $page
     * @param int $limit
     * @return Paginator|UserInterface[]
     */
    public function findAllUsers($page = 0, $limit = 50);

    /**
     * @param string $email
     * @return UserInterface
     */
    public function findUserByEmail($email);

    /**
     * @param string $token
     * @return UserInterface
     */
    public function findUserByToken($token);

    /**
     * @param string $username
     * @return UserInterface
     */
    public function findUserByUsername($username);

    /**
     * @param int $id
     * @return mixed
     */
    public function generateUserToken($id);

    /**
     * @param numeric $id
     * @return UserInterface
     */
    public function getUser($id);

    /**
     * @return UserInterface
     */
    public function getUserFromAuthenticator();

    /**
     * @param int    $id
     * @param string $password
     * @return mixed
     */
    public function updateUserPassword($id, $password);
}
