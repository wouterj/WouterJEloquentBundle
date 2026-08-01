<?php

if (!defined('base_path')) {
    /**
     * Function used by the SQLiteConnector.
     *
     * Symfony resolves base paths in the container, this function returns the argument verbatim.
     *
     * @see https://github.com/laravel/framework/blob/6e6ec058bd555cd70b33656bd7254f3667b73822/src/Illuminate/Foundation/helpers.php#L198-L208
     */
    function base_path($path = '') {
        return $path;
    }
}
