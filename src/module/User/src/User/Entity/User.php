<?php
/**
 *
 * @author Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @copyright 2013 by www.serlo.org
 * @license LGPL
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
namespace User\Entity;

use Authorization\Entity\RoleInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Uuid\Entity\UuidEntity;

/**
 * A user.
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="user")
 */
class User extends UuidEntity implements UserInterface
{

    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Uuid\Entity\Uuid", inversedBy="user", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     * @ORM\JoinTable(name="role_user")
     */
    protected $roles;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $username;

    /**
     * @ORM\Column(type="string")
     * *
     */
    protected $password;

    /**
     * @ORM\Column(type="integer")
     * *
     */
    protected $logins;

    /**
     * @ORM\Column(type="string")
     */
    protected $token;

    /**
     * @ORM\Column(type="datetime",
     * nullable=true)
     */
    protected $last_login;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $date;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->ads_enabled = true;
        $this->removed = false;
        $this->logins = 0;
        $this->generateToken();
    }

    public function getToken()
    {
        return $this->token;
    }

    public function generateToken()
    {
        $this->token = hash('crc32b', uniqid('user.token.', true));
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getLogins()
    {
        return $this->logins;
    }

    public function getLastLogin()
    {
        return $this->last_login;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function setLogins($logins)
    {
        $this->logins = $logins;
        return $this;
    }

    public function setLastLogin(DateTime $last_login)
    {
        $this->last_login = $last_login;
        return $this;
    }

    public function setDate(DateTime $date)
    {
        $this->date = $date;
        return $this;
    }

    public function addRole(RoleInterface $role)
    {
        $this->roles->add($role);
        return $this;
    }

    public function removeRole(RoleInterface $role)
    {
        $this->roles->removeElement($role);
        return $this;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function populate(array $data = array())
    {
        $this->injectArray('email', $data);
        $this->injectArray('password', $data);
        $this->injectArray('username', $data);
        $this->injectArray('logins', $data);
        $this->injectArray('ads_enabled', $data);
        $this->injectArray('removed', $data);
        $this->injectArray('lastname', $data);
        $this->injectArray('givenname', $data);
        $this->injectArray('gender', $data);
        return $this;
    }

    public function updateLoginData()
    {
        $this->setLogins($this->getLogins() + 1);
        $this->setLastLogin(new DateTime());
        return $this;
    }

    public function hasRole(RoleInterface $role)
    {
        return $this->getRoles()->contains($role);
    }
}