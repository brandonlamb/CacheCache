<?php
/*
 * This file is part of the CacheCache package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CacheCache;

use Monolog\Logger;

/**
 * Manages multiple instances of Cache objects
 */
class CacheManager
{
	const DEFAULT_NAME = 'default';

	/** @var Logger */
	public static $logger;

	/** @var int */
	public static $logLevel;

	/** @var array */
	public static $defaults = array(
		'backend'		=> null,
		'backend_args'	=> null,
		'namespace'		=> '',
		'ttl'			=> null,
		'variation'		=> 0,
	);

	/** @var array */
	private static $caches = array();

	/**
	 * Setups the cache manager.
	 *
	 * If $caches is the class name of a backend, a {@see Backend} instance,
	 * a {@see Cache} instance will be created under the default name.
	 *
	 * $caches can also be an array to define multiple cache instances an once.
	 * Keys will be used as cache names and values must be compatible with the
	 * {@see factory()} method $options argument.
	 *
	 * <code>
	 *      CacheManager::setup(array(
	 *          'default' => 'CacheCache\Backend\File'
	 *      ));
	 * </code>
	 *
	 * If $logger is not null, all Backend instances will be wrapped in a
	 * {@see LoggingBackend} object.
	 *
	 * @see factory()
	 * @param array $caches
	 * @param Logger $logger
	 * @param int $logLevel
	 */
	public static function setup($caches, Logger $logger = null, $logLevel = null)
	{
		if (!is_array($caches)) {
			$caches = array(static::DEFAULT_NAME => array('backend' => $caches));
		}

		static::$logger = $logger;
		static::$logLevel = $logLevel;

		foreach ($caches as $name => $options) {
			static::$caches[$name] = static::factory($options);
		}
	}

	/**
	 * Creates a {@see Cache} object
	 *
	 * $options can either be the class name of a backend, a {@see Backend}
	 * instance or an array.
	 *
	 * Possible array values:
	 *  - backend: backend class name or {@see Backend} instance
	 *  - backend_args: an array of constructor arguments for the backend
	 *  - namespace
	 *  - ttl
	 *  - variation
	 *
	 * Default values for these options can be defined in the $defaults static
	 * property.
	 *
	 * @param array $options
	 * @return Cache
	 */
	public static function factory($options)
	{
		if (is_string($options) || $options instanceof BackendInterface) {
			$options = array('backend' => $options);
		} else if (!is_array($options)) {
			throw new CacheException("Options for '$name' in CacheManager::create() must be an array");
		}

		$options = array_merge(static::$defaults, $options);
		if (!isset($options['backend'])) {
			throw new CacheException("No backend specified for '$name' in CacheManager::create()");
		}

		$backend = $options['backend'];
		if (is_string($backend)) {
			if (isset($options['backend_args'])) {
				$backendClass = new \ReflectionClass($backend);
				$backend = $backendClass->newInstanceArgs($options['backend_args']);
			} else {
				$backend = new $backend();
			}
		}

		if (static::$logger !== null) {
			$backend = new LoggingBackend($backend, static::$logger, static::$logLevel);
		}

		$cache = new Cache($backend, $options['namespace'], $options['ttl'], $options['variation']);
		return $cache;
	}

	/**
	 * Makes a {@see Cache} instance available through $name
	 *
	 * @param string $name
	 * @param Cache $cache
	 */
	public static function set($name, Cache $cache)
	{
		static::$caches[$name] = $cache;
	}

	/**
	 * Returns the {@see Cache} instance under $name
	 *
	 * @param string $name If null will used the instance named CacheManager::DEFAULT_NAME
	 * @return Cache
	 */
	public function get($name = null)
	{
		$name = $name ?: static::DEFAULT_NAME;
		if (!isset(static::$caches[$name])) {
			throw new CacheException("Cache '$name' not found");
		}
		return static::$caches[$name];
	}

	/**
	 * Shorcut to static::get()->ns()
	 *
	 * @see Cache::ns()
	 * @param string $namespace
	 * @param int $defaultTTL
	 * @return Cache
	 */
	public function ns($namespace, $defaultTTL = null)
	{
		return static::get()->ns($namespace, $defaultTTL);
	}
}
