<?php
/**
 *
 *
 *
 *
 *
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license LGPL-3.0
 * @license http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace UserTest;

use User\Manager\UserManager;

/**
 * @codeCoverageIgnore
 */
abstract class UserManagerTest extends \PHPUnit_Framework_TestCase
{

    protected $userManager, $uuidManagerMock;

    public function setUp()
    {
        $this->userManager = new UserManager();
        $classResolverMock = $this->getMock('ClassResolver\ClassResolver');
        $entityManagerMock = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);
        $serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceManager');
        $repositoryMock = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->userMock = $this->getMock('User\Entity\User');
        $userServiceMock = $this->getMock('User\Service\UserService');
        $authServiceMock = $this->getMock('Zend\Authentication\AuthenticationService');
        $this->uuidManagerMock = $this->getMock('Uuid\Manager\UuidManager');
        
        $classResolverMock->expects($this->any())
            ->method('resolveClassName')
            ->will($this->returnValue('User\Entity\User'));
        $serviceLocatorMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($userServiceMock));
        $repositoryMock->expects($this->any())
            ->method('findOneBy')
            ->will($this->returnValue($this->userMock));
        $entityManagerMock->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repositoryMock));
        $userServiceMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $authServiceMock->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue('foo@bar.de'));
        $this->userMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $authServiceMock->expects($this->any())
            ->method('hasIdentity')
            ->will($this->returnValue(1));
        
        $this->userManager->setAuthenticationService($authServiceMock);
        $this->userManager->setCheckClassInheritance(false);
        $this->userManager->setClassResolver($classResolverMock);
        $this->userManager->setObjectManager($entityManagerMock);
        $this->userManager->setServiceLocator($serviceLocatorMock);
        $this->userManager->setUuidManager($this->uuidManagerMock);
        
        $this->userServiceMock = $userServiceMock;
        $this->authServiceMock = $authServiceMock;
    }

    public function testGetUser()
    {
        $this->userManager->getObjectManager()->expects($this->once())
            ->method('find')
            ->will($this->returnValue($this->userMock));
        
        $this->assertEquals(1, $this->userManager->getUser(1)
            ->getId());
        
        // consecutive calls
        $this->assertEquals(1, $this->userManager->getUser(1)
            ->getId());
    }
    
    public function testTrashUser()
    {
        $this->userServiceMock->expects($this->once())
            ->method('setTrashed');
        $this->userManager->trashUser(1);
    }

    public function testCreateUser()
    {
        $this->userManager->getClassResolver()
            ->expects($this->once())
            ->method('resolve')
            ->will($this->returnValue($this->getMock('User\Entity\User')));
        $this->assertInstanceOf('User\Service\UserServiceInterface', $this->userManager->createUser(array()));
    }

    public function testFindUserByEmail()
    {
        $this->userManager->getObjectManager()->getRepository('User\Entity\User')->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($this->userMock));
        $user = $this->userManager->findUserByEmail('foo@bar.de');
        $this->assertEquals(1, $user->getId());
    }

    public function testFindUserByUsername()
    {
        $user = $this->userManager->findUserByUsername('foo@bar.de');
        $this->assertEquals(1, $user->getId());
    }

    public function testGetUserFromAuthenticator()
    {
        $this->userServiceMock->expects($this->once())
            ->method('isTrashed')
            ->will($this->returnValue(false));
        
        $this->userServiceMock->expects($this->once())
            ->method('hasRole')
            ->will($this->returnValue(true));
        
        $this->authServiceMock->expects($this->never())
            ->method('clearIdentity');
        $user = $this->userManager->getUserFromAuthenticator();
        
        $this->assertEquals(1, $user->getId());
    }

    public function testGetUserFromAuthenticatorFailsBecauseRemoved()
    {
        $this->userServiceMock->expects($this->once())
            ->method('isTrashed')
            ->will($this->returnValue(true));
        $this->authServiceMock->expects($this->once())
            ->method('clearIdentity');
        $user = $this->userManager->getUserFromAuthenticator();
        $this->assertEquals(null, $user);
    }


}