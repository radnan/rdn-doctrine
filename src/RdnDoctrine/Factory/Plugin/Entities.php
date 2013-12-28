<?php

namespace RdnDoctrine\Factory\Plugin;

use Doctrine\ORM\EntityManager;
use RdnFactory\Plugin\AbstractPlugin;

class Entities extends AbstractPlugin
{
	/**
	 * Get the entity manager with the given name.
	 *
	 * @param string $managerName Entity manager name
	 *
	 * @return EntityManager
	 */
	public function __invoke($managerName = null)
	{
		if ($managerName === null)
		{
			$moduleName = strstr(get_class($this->factory), '\\', true);
			$managerName = $this->factory->config('rdn_entity_managers', 'modules', $moduleName) ?: $moduleName;
		}

		$managers = $this->factory->service('RdnDoctrine\EntityManagerManager');
		return $managers->get($managerName);
	}
}
