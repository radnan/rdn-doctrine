<?php

namespace RdnDoctrine\Factory;

use RdnDoctrine;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EntityManagerManager implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $services)
	{
		$config = $services->get('Config');
		$config = new Config($config['rdn_entity_managers']);

		$manager = new RdnDoctrine\EntityManagerManager($config);
		$manager->setServiceLocator($services);

		return $manager;
	}
}
