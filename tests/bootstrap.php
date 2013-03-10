<?php

set_include_path(implode(PATH_SEPARATOR, array(
	__DIR__,
	__DIR__ . '/../src',
	__DIR__ . '/../../php_vendors/monolog/src',
	get_include_path()
)));

spl_autoload_register(function($className) {
	if (substr($className, 0, 10) === 'CacheCache') {
		$filename = str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, trim($className, '\\_')) . '.php';
		require_once $filename;
	}
});


spl_autoload_register(function($className) {
	if (substr($className, 0, 7) === 'Monolog') {
		$filename = str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, trim($className, '\\_')) . '.php';
		require_once $filename;
	}
});
