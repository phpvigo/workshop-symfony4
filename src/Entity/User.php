<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\Uuidable;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Controller\ResetPasswordAction;

/**
 * @ApiResource(
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_ADMIN') or object == user"
 *          },
 *          "put"= {
 *              "security"="is_granted('ROLE_ADMIN')"
 *          },
 *          "put_reset_password"= {
 *              "security"="is_granted('ROLE_USER') and object == user",
 *              "path"="/users/{id}/reset-password",
 *              "method"="PUT",
 *              "controller"=ResetPasswordAction::class,
 *              "denormalization_context"={
 *                  "groups"={"put-reset-password"}
 *              },
 *              "openapi_context"= {
 *                  "summary"="Reset password"
 *              },
 *              "validation_groups"={"put-reset-password"}
 *          }
 *     },
 *     collectionOperations={
 *          "get": {
 *              "security"="is_granted('ROLE_ADMIN')",
 *          },
 *          "post": {
 *              "security"="is_granted('ROLE_ADMIN')"
 *          }
 *     }
 * )
 * @UniqueEntity("username")
 */
class User implements UserInterface
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const DEFAULT_ROLES = [self::ROLE_USER];

    use Uuidable;

    /**
     * @Groups({"read"})
     * @Assert\NotBlank
     * @Assert\Length(min="5", max="100")
     */
    private $username;

    /**
     * @Assert\NotBlank
     * @Assert\Regex(
     *     pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *     message="Password must be seven characters long and contain at least one digit, one upper case letter and one lowercase letter"
     * )
     */
    private $password;

    /**
     * @Assert\NotBlank()
     * @Assert\Expression(
     *     "this.getPassword() === this.getRetypedPassword()",
     *     message="Passwords does not match"
     * )
     */
    private $retypedPassword;

    /**
     * @Groups({"put-reset-password"})
     * @Assert\NotBlank(groups={"put-reset-password"})
     */
    private $oldPassword;

    /**
     * @Groups({"put-reset-password"})
     * @Assert\NotBlank(groups={"put-reset-password"})
     */
    private $newPassword;

    /**
     * @Groups({"put-reset-password"})
     * @Assert\NotBlank(groups={"put-reset-password"})
     */
    private $retypeNewPassword;

    private $roles;

    public function __construct()
    {
        $this->roles = self::DEFAULT_ROLES;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRetypedPassword()
    {
        return $this->retypedPassword;
    }

    /**
     * @param mixed $retypedPassword
     * @return User
     */
    public function setRetypedPassword($retypedPassword)
    {
        $this->retypedPassword = $retypedPassword;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param mixed $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

    /**
     * @return mixed
     */
    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    /**
     * @param mixed $oldPassword
     * @return User
     */
    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * @param mixed $newPassword
     * @return User
     */
    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRetypeNewPassword()
    {
        return $this->retypeNewPassword;
    }

    /**
     * @param mixed $retypeNewPassword
     * @return User
     */
    public function setRetypeNewPassword($retypeNewPassword)
    {
        $this->retypeNewPassword = $retypeNewPassword;

        return $this;
    }

}
