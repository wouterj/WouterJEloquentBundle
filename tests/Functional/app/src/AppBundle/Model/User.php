<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Model;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class LegacyUser extends Model implements UserInterface
{
    public $fillable = ['name', 'email', 'password'];

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
    }
}

if (interface_exists(PasswordAuthenticatedUserInterface::class)) {
    class User extends LegacyUser implements PasswordAuthenticatedUserInterface
    {
    }
} else {
    // BC with symfony/security-core <5.3
    class User extends LegacyUser
    {
    }
}
