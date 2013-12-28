<?php

namespace RdnDoctrine;

use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

class EntityManagerManager extends AbstractPluginManager
{
	/**
	 * Validate the plugin
	 *
	 * Checks that the plugin loaded is an instance of EntityManager.
	 *
	 * @param  mixed $plugin
	 * @return void
	 * @throws Exception\RuntimeException if invalid
	 */
	public function validatePlugin($plugin)
	{
		if ($plugin instanceof EntityManager)
		{
			return;
		}

		throw new Exception\RuntimeException(sprintf(
			'Plugin of type %s is invalid; must be Doctrine\ORM\EntityManager'
			, is_object($plugin) ? get_class($plugin) : gettype($plugin)
		));
	}
}
