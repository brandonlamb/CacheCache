<?php
/*
 * This file is part of the CacheCache package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpCache\Backends;

use PhpCache\CacheException;

/**
 * Memory
 *
 * Caches data in memory for the time of the script's execution
 */
class Session extends Memory
{
    public function __construct()
    {
        if (!isset($_SESSION)) {
            throw new CacheException("Session must be started in order to use 'PhpCache\Backends\Session'");
        }
        $this->cache = &$_SESSION;
    }
}
