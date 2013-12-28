<?php

namespace RdnDoctrine\Factory\Plugin;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use RdnFactory\Plugin\AbstractPlugin;

class Entity extends AbstractPlugin
{
	/**
	 * Get an entity repository by the entity name and optionally the manager name.
	 *
	 * @param string $entity Entity name (ex: 'Entry', 'Module:Entry')
	 * @param string $managerName Entity manager name (defaults to current module name)
	 *
	 * @return EntityRepository
	 */
	public function __invoke($entity, $managerName = null)
	{
		if (strpos($entity, ':') !== false)
		{
			list($moduleName) = explode(':', $entity);
		}
		else
		{
			$moduleName = strstr(get_class($this->factory), '\\', true);
		}

		if (func_num_args() == 1)
		{
			$managerName = $this->factory->config('rdn_entity_managers', 'modules', $moduleName) ?: $moduleName;
		}

		/** @var EntityManager $entities */
		$entities = $this->factory->entities($managerName);

		if (strpos($entity, ':') === false)
		{
			$resolver = $this->factory->service('RdnDoctrine\EntityManager\AliasResolver');
			$entity = $resolver->resolve($entities, $entity, array($moduleName, $managerName));
		}

		return $entities->getRepository($entity);
	}
}
