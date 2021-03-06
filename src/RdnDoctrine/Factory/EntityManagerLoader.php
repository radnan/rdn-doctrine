<?php

namespace RdnDoctrine\Factory;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use RdnDoctrine\DBAL\Logging\Profiler;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\ArrayUtils;

class EntityManagerLoader implements AbstractFactoryInterface
{
	public function canCreateServiceWithName(ServiceLocatorInterface $managers, $name, $rName)
	{
		if ($managers instanceof ServiceLocatorAwareInterface)
		{
			$services = $managers->getServiceLocator();
		}
		else
		{
			$services = $managers;
		}

		$config = $services->get('Config');
		return isset($config['rdn_entity_managers']['managers'][$rName]);
	}

	public function createServiceWithName(ServiceLocatorInterface $managers, $name, $rName)
	{
		if ($managers instanceof ServiceLocatorAwareInterface)
		{
			$services = $managers->getServiceLocator();
		}
		else
		{
			$services = $managers;
		}

		$config = $services->get('Config');
		$spec = $config['rdn_entity_managers']['managers'][$rName];

		$modules = $services->get('ModuleManager');
		$moduleNames = array_keys($config['rdn_entity_managers']['modules'], $rName);
		if (!in_array($rName, $moduleNames))
		{
			$moduleNames[] = $rName;
		}

		$defaultSpec = $config['rdn_entity_managers']['configs']['EntityManagerLoader'];
		foreach ($moduleNames as $moduleName)
		{
			$module = $modules->getModule($moduleName);
			if ($module)
			{
				$mName = strstr(get_class($module), '\\', true);

				if (!isset($spec['entity_namespaces'][$mName]))
				{
					$spec['entity_namespaces'][$mName] = $mName .'\\Entity';
				}

				if (!isset($spec['metadata_paths'][$mName]))
				{
					if (method_exists($module, 'getPath'))
					{
						$path = $module->getPath();
					}
					else
					{
						$ref = new \ReflectionClass($module);
						$path = dirname($ref->getFileName());
					}

					if (file_exists($path .'/Entity'))
					{
						$spec['metadata_paths'][$mName] = $path .'/Entity';
					}
				}
			}

			if (!isset($spec['proxy_namespace']))
			{
				$spec['proxy_namespace'] = $rName .'\\Entity\Proxy';
			}
		}
		$spec = ArrayUtils::merge($defaultSpec, $spec);

		return $this->createEntityManager($services, $spec, $rName);
	}

	public function createEntityManager(ServiceLocatorInterface $services, $spec, $name)
	{
		$config = new Configuration;

		// Set up custom stuff
		$this->setupCustomFunctions($config, $spec, $name);

		// Set up logging
		$this->setupLogging($services, $config, $spec, $name);

		// Set up caching
		$this->setupCaching($config, $spec, $name);

		// Set up proxies
		$this->setupProxies($config, $spec, $name);

		// Naming strategy
		$this->setupNamingStrategy($config, $spec, $name);

		// Set up entity/annotation paths
		$this->setupMetadata($config, $spec, $name);

		// Database connection
		$conn = $this->setupConnection($services, $spec, $name);

		// Event Manager
		$events = $this->setupEventManager($config, $spec, $name);

		$entities = EntityManager::create($conn, $config, $events);

		// Set up custom data types
		$this->setupPlatform($entities, $spec);

		return $entities;
	}

	protected function setupCustomFunctions(Configuration $config, $spec)
	{
		foreach ($spec['custom_hydration_modes'] as $name => $classname)
		{
			$config->addCustomHydrationMode($name, $classname);
		}
		foreach ($spec['custom_datetime_functions'] as $name => $classname)
		{
			$config->addCustomDatetimeFunction($name, $classname);
		}
		foreach ($spec['custom_numeric_functions'] as $name => $classname)
		{
			$config->addCustomNumericFunction($name, $classname);
		}
		foreach ($spec['custom_string_functions'] as $name => $classname)
		{
			$config->addCustomStringFunction($name, $classname);
		}
		foreach ($spec['filters'] as $name => $classname)
		{
			$config->addFilter($name, $classname);
		}
	}

	protected function setupLogging(ServiceLocatorInterface $services, Configuration $config, $spec, $name)
	{
		$profiler = new Profiler($name);
		$profiler->getEventManager()->setSharedManager($services->get('SharedEventManager'));
		$profiler->setIsLogging($spec['log_sql']);

		$config->setSQLLogger($profiler);
	}

	protected function setupCaching(Configuration $config, $spec)
	{
		$cacheClass = 'Doctrine\Common\Cache\\'. $spec['cache_provider'];
		if (is_subclass_of($cacheClass, 'Doctrine\Common\Cache\FileCache'))
		{
			$cacheImpl = new $cacheClass($spec['cache_dir']);
		}
		else
		{
			$cacheImpl = new $cacheClass;
		}
		$config->setMetadataCacheImpl($cacheImpl);
		$config->setQueryCacheImpl($cacheImpl);
	}

	protected function setupProxies(Configuration $config, $spec)
	{
		$config->setProxyDir($spec['proxy_dir']);
		$config->setProxyNamespace($spec['proxy_namespace']);
		$config->setAutoGenerateProxyClasses($spec['proxy_autogenerate']);
	}

	protected function setupNamingStrategy(Configuration $config, $spec)
	{
		$strategyClass = $spec['naming_strategy'];
		if (isset($strategyClass))
		{
			$strategy = new $strategyClass;
			$config->setNamingStrategy($strategy);

			if (isset($spec['table_prefixes']) && method_exists($strategy, 'setTablePrefixes'))
			{
				$strategy->setTablePrefixes($spec['table_prefixes']);
			}
		}
	}

	protected function setupMetadata(Configuration $config, $spec)
	{
		$driverImpl = $config->newDefaultAnnotationDriver($spec['metadata_paths'], $spec['simple_annotation']);
		$config->setMetadataDriverImpl($driverImpl);
		$config->setEntityNamespaces($spec['entity_namespaces']);
	}

	protected function setupConnection(ServiceLocatorInterface $services, $spec)
	{
		if (is_string($spec['connection']))
		{
			$adapters = $services->get('RdnDatabase\Adapter\AdapterManager');
			/** @var Adapter $adapter */
			$adapter = $adapters->get($spec['connection']);

			$connection = $adapter->getDriver()->getConnection();
			if (!$connection->isConnected())
			{
				$connection->connect();
			}

			$conn = array(
				'pdo' => $connection->getResource(),
			);
		}
		elseif (is_array($spec['connection']))
		{
			$conn = $spec['connection'];
		}
		else
		{
			throw new \RuntimeException("Must specify 'connection' parameters as a string or an array");
		}

		return $conn;
	}

	protected function setupEventManager(Configuration $config, $spec)
	{
		return new EventManager;
	}

	protected function setupPlatform(EntityManager $entities, $spec)
	{
		$platform = $entities->getConnection()->getDatabasePlatform();

		foreach ($spec['types'] as $name => $options)
		{
			if (is_string($options))
			{
				$className = $options;
				$dbType = $name;
			}
			else
			{
				$name = isset($options['name']) ? $options['name'] : $name;
				$className = isset($options['className']) ? $options['className'] : null;
				$dbType = $options['dbType'];
			}

			if (!Type::hasType($name) && isset($className))
			{
				Type::addType($name, $className);
			}

			$platform->registerDoctrineTypeMapping($dbType, $name);
		}
	}
}
