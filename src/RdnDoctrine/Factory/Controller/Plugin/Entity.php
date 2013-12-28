<?php

namespace RdnDoctrine\Factory\Controller\Plugin;

use RdnDoctrine\Controller\Plugin;
use RdnFactory\AbstractFactory;

class Entity extends AbstractFactory
{
	protected function create()
	{
		$managers = $this->service('RdnDoctrine\EntityManagerManager');
		$modules = $this->config('entity_managers', 'modules');
		$resolver = $this->service('RdnDoctrine\EntityManager\AliasResolver');
		return new Plugin\Entity($managers, $modules, $resolver);
	}
}
