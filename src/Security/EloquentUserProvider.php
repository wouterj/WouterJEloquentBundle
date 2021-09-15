<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2020 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Security;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @template TUserObject of Model&UserInterface
 *
 * @final
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class EloquentUserProvider implements UserProviderInterface
{
    private $modelFqcn;
    private $usernameAttribute;

    /**
     * @psalm-param class-string<TUserObject> $modelFqcn
     */
    public function __construct(string $modelFqcn, string $usernameAttribute)
    {
        $this->modelFqcn = $modelFqcn;
        $this->usernameAttribute = $usernameAttribute;
    }

    /** @psalm-return TUserObject */
    public function loadUserByUsername($username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    /** @psalm-return TUserObject */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->createModel()->newQuery()
            ->where($this->usernameAttribute, $identifier)
            ->first();
        if (null === $user) {
            if (class_exists(UserNotFoundException::class)) {
                $e = new UserNotFoundException();
                $e->setUserIdentifier($identifier);
            } else {
                // BC with symfony/security-core <5.3
                $e = new UsernameNotFoundException();
                /** @psalm-suppress UndefinedMethod https://github.com/vimeo/psalm/issues/5750 */
                $e->setUsername($identifier);
            }

            throw $e;
        }

        return $user;
    }

    /** @psalm-return TUserObject */
    public function refreshUser(UserInterface $user): UserInterface
    {
        /** @psalm-var TUserObject $user */
        if (!$this->supportsClass(\get_class($user))) {
            throw new UnsupportedUserException();
        }

        $refreshedUser = $this->createModel()->newQuery()->where($user->getKeyName(), $user->getKey())->first();
        if (null === $refreshedUser) {
            $userIdentifier = $user->getAttribute($this->usernameAttribute);
            if (class_exists(UserNotFoundException::class)) {
                $e = new UserNotFoundException();
                $e->setUserIdentifier($userIdentifier);
            } else {
                // BC with symfony/security-core <5.3
                $e = new UsernameNotFoundException();
                /** @psalm-suppress UndefinedMethod https://github.com/vimeo/psalm/issues/5750 */
                $e->setUsername($userIdentifier);
            }

            throw $e;
        }

        return $refreshedUser;
    }

    public function supportsClass($class): bool
    {
        return $class === $this->modelFqcn || is_a($class, $this->modelFqcn, true);
    }

    /** @psalm-return TUserObject */
    private function createModel(): Model
    {
        $class = '\\'.ltrim($this->modelFqcn, '\\');

        return new $class;
    }
}
