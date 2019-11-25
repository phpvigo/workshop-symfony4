<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\Uuidable;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Controller\ResetPasswordAction;
use App\Controller\CurrentLoggedUserAction;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}},
 *     denormalizationContext={"groups"={"write"}},
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_ADMIN') or object == user"
 *          },
 *          "get-current-user": {
 *              "security"="is_granted('ROLE_USER')",
 *              "path"="/me",
 *              "method"="GET",
 *              "controller"=CurrentLoggedUserAction::class,
 *              "openapi_context"= {
 *                  "summary"="Obtain current logged user",
 *                  "parameters"={}
 *              },
 *              "read"=false
 *          },
 *          "put"= {
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "validation_groups"={"write"}
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
 *                  "summary"="Resets user password"
 *              },
 *              "validation_groups"={"put-reset-password"}
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_ADMIN') and object != user"
 *          }
 *     },
 *     collectionOperations={
 *          "get": {
 *              "security"="is_granted('ROLE_ADMIN')",
 *          },
 *          "post": {
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "validation_groups"={"write"}
 *          }
 *     }
 * )
 * @UniqueEntity("username", groups={"write"})
 */
class User implements UserInterface
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const DEFAULT_ROLES = [self::ROLE_USER];

    use Uuidable;

    /**
     * @Groups({"read", "write"})
     * @Assert\NotBlank(groups={"write"})
     * @Assert\Length(min="5", max="100")
     */
    private $username;

    /**
     * @Groups({"write"})
     * @Assert\NotBlank(groups={"write"})
     * @Assert\Regex(
     *     pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *     message="Password must be seven characters long and contain at least one digit, one upper case letter and one lowercase letter",
     *     groups={"write"}
     * )
     */
    private $password;

    /**
     * @Groups({"write"})
     * @Assert\NotBlank(groups={"write"})
     * @Assert\Expression(
     *     "this.getPassword() === this.getRetypedPassword()",
     *     message="Passwords does not match",
     *     groups={"write"}
     * )
     */
    private $retypedPassword;

    /**
     * @Groups({"put-reset-password"})
     * @Assert\NotBlank(groups={"put-reset-password"})
     * @Assert\Expression("", groups={"put-reset-password"})
     */
    private $oldPassword;

    /**
     * @Groups({"put-reset-password"})
     * @Assert\NotBlank(groups={"put-reset-password"})
     * @UserPassword
     */
    private $newPassword;

    /**
     * @Groups({"put-reset-password"})
     * @Assert\NotBlank(groups={"put-reset-password"})
     */
    private $retypeNewPassword;
    /**
     * @Groups({"read", "write"})
     */
    private $roles;

    public function __construct()
    {
        $this->roles = self::DEFAULT_ROLES;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getRetypedPassword()
    {
        return $this->retypedPassword;
    }

    public function setRetypedPassword($retypedPassword)
    {
        $this->retypedPassword = $retypedPassword;

        return $this;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

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

    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword()
    {
        return $this->newPassword;
    }

    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getRetypeNewPassword()
    {
        return $this->retypeNewPassword;
    }

    public function setRetypeNewPassword($retypeNewPassword)
    {
        $this->retypeNewPassword = $retypeNewPassword;

        return $this;
    }
}
