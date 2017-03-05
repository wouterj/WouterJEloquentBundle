<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2017 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Model;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class UserObserver
{
    public function creating(User $user)
    {
        echo 'creating '.$user->name."\n";
    }

    public function created(User $user)
    {
        echo 'created '.$user->name."\n";
    }

    public function updating(User $user)
    {
        echo 'updating '.$user->name."\n";
    }

    public function updated(User $user)
    {
        echo 'updated '.$user->name."\n";
    }

    public function saving(User $user)
    {
        echo 'saving '.$user->name."\n";
    }

    public function saved(User $user)
    {
        echo 'saved '.$user->name."\n";
    }

    public function deleting(User $user)
    {
        echo 'deleting '.$user->name."\n";
    }

    public function deleted(User $user)
    {
        echo 'deleted '.$user->name."\n";
    }

    public function restoring(User $user)
    {
        echo 'restoring '.$user->name."\n";
    }

    public function restored(User $user)
    {
        echo 'restored '.$user->name."\n";
    }
}
