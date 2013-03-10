<?php
/*
 * This file is part of the CacheCache package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CacheCache\Backends;

/**
 * Dummy
 *
 * Does nothing. Can be used to disable caching
 */
class Dummy extends AbstractBackend implements BackendInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function get($id)
	{
		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set($id, $value, $ttl = null)
	{
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete($id)
	{
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function flushAll()
	{
		return true;
	}
}
