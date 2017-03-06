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
    /** Used to test Symfony service observers */
    public $fired = [];
    /** Used to test class observers */
    public static $logs = [];

    public function creating(User $user)
    {
        $m = 'creating '.$user->name;

        self::$logs[] = $m;
        $this->fired[] = $m;
    }

    public function created(User $user)
    {
        $m = 'created '.$user->name;

        self::$logs[] = $m;
        $this->fired[] = $m;
    }

    public function updating(User $user)
    {
        $m = 'updating '.$user->name;

        self::$logs[] = $m;
        $this->fired[] = $m;
    }

    public function updated(User $user)
    {
        $m = 'updated '.$user->name;

        self::$logs[] = $m;
        $this->fired[] = $m;
    }

    public function saving(User $user)
    {
        $m = 'saving '.$user->name;

        self::$logs[] = $m;
        $this->fired[] = $m;
    }

    public function saved(User $user)
    {
        $m = 'saved '.$user->name;

        self::$logs[] = $m;
        $this->fired[] = $m;
    }

    public function deleting(User $user)
    {
        $m = 'deleting '.$user->name;

        self::$logs[] = $m;
        $this->fired[] = $m;
    }

    public function deleted(User $user)
    {
        $m = 'deleted '.$user->name;

        self::$logs[] = $m;
        $this->fired[] = $m;
    }

    public function restoring(User $user)
    {
        $m = 'restoring '.$user->name;

        self::$logs[] = $m;
        $this->fired[] = $m;
    }

    public function restored(User $user)
    {
        $m = 'restored '.$user->name;

        self::$logs[] = $m;
        $this->fired[] = $m;
    }
}
