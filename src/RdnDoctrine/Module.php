<?php

namespace RdnDoctrine;

use Zend\ModuleManager\Feature\DependencyIndicatorInterface;
use Zend\ModuleManager\ModuleManager;

class Module implements DependencyIndicatorInterface
{
	public function getConfig()
	{
		return include __DIR__ .'/../../config/module.config.php';
	}

	public function init(ModuleManager $modules)
	{
		$modules->loadModule('RdnConsole');
		$modules->loadModule('RdnDatabase');
		$modules->loadModule('RdnFactory');
	}

	/**
	 * Expected to return an array of modules on which the current one depends on
	 *
	 * @return array
	 */
	public function getModuleDependencies()
	{
		return array(
			'RdnConsole',
			'RdnDatabase',
			'RdnFactory',
		);
	}
}
