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

use Predis,
	CacheCache\BackendInterface;

/**
 * Redis
 */
class Redis implements BackendInterface
{
	/** @var Predis\Client */
	protected $redis;

	/**
	 * Constructor
	 *
	 * If $options contains a single key named redis with
	 * a Predis\Client instance, it will be used.
	 * Otherwise, creates a Predis\Client using the $options
	 * array as the constructor argument.
	 *
	 * @param array $options
	 */
	public function __construct(array $options)
	{
		if (isset($options['redis'])) {
			$this->redis = $options['redis'];
		} else {
			$this->redis = new Predis\Client($options);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function exists($id)
	{
		return $this->redis->exists($id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get($id)
	{
		return $this->redis->get($id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMulti(array $ids)
	{
		$pipe = $this->redis->pipeline();
		array_map(array($pipe, 'get'), $ids);
		return $pipe->execute();
	}

	/**
	 * {@inheritDoc}
	 */
	public function add($id, $value, $ttl = null)
	{
		try {
			$this->redis->setnx($id, $value);
			if ($ttl) {
				$this->redis->expire($id, $ttl);
			}
			return true;
		} catch (Predis\PredisException $e) {
			return false;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function set($id, $value, $ttl = null)
	{
		try {
			$this->redis->set($id, $value);
			if ($ttl) {
				$this->redis->expire($id, $ttl);
			}
			return true;
		} catch (Predis\PredisException $e) {
			return false;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function setMulti(array $items, $ttl = null)
	{
		$pipe = $this->redis->pipeline();
		foreach ($items as $id => $value) {
			$pipe->set($id, $value);
			if ($ttl) {
				$pipe->expire($id, $ttl);
			}
		}
		$pipe->execute();
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete($id)
	{
		return $this->redis->del($id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function flushAll()
	{
		$this->redis->flushdb();
	}

	/**
	 * {@inheritDoc}
	 */
	public function supportsPipelines()
	{
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function createPipeline()
	{
		return $this->redis->pipeline();
	}
}
