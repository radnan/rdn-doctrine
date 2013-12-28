<?php

namespace RdnDoctrine\Controller\Plugin;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use RdnDoctrine\EntityManager\AliasResolverInterface;
use RdnDoctrine\EntityManagerManager;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Fetch the entity manager or a repository for a given entity.
 */
class Entity extends AbstractPlugin
{
	/**
	 * @var EntityManagerManager
	 */
	protected $managers;

	/**
	 * Map of module names to entity manager names
	 *
	 * @var array
	 */
	protected $modules = array();

	/**
	 * @var AliasResolverInterface
	 */
	protected $resolver;

	/**
	 * @param EntityManagerManager $managers
	 * @param array $modules
	 * @param AliasResolverInterface $resolver
	 */
	public function __construct(EntityManagerManager $managers, array $modules = array(), AliasResolverInterface $resolver)
	{
		$this->managers = $managers;
		$this->modules = $modules;
		$this->resolver = $resolver;
	}

	/**
	 * Get an entity manager or repository instance
	 *
	 * @param string $name Entity short name
	 *
	 * @throws \RuntimeException if no entity manager is found
	 * @return EntityRepository|EntityManager
	 */
	public function __invoke($name = null)
	{
		if (strpos($name, ':') !== false)
		{
			list($module) = explode(':', $name);
		}
		else
		{
			$module = strstr(get_class($this->controller), '\\', true);
		}
		if (isset($this->modules[$module]))
		{
			$managerName = $this->modules[$module];
		}
		else
		{
			$managerName = $module;
		}

		/** @var EntityManager $entities */
		$entities = $this->managers->get($managerName);

		if (func_num_args() == 0)
		{
			return $entities;
		}

		if (strpos($name, ':') === false)
		{
			$name = $this->resolver->resolve($entities, $name, array($module, $managerName));
		}

		return $entities->getRepository($name);
	}
}
