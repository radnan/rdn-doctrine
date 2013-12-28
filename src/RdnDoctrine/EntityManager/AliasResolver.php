<?php

namespace RdnDoctrine\EntityManager;

use Doctrine\ORM\EntityManager;

/**
 * Resolve full name of entity (Alias:ShortName).
 */
class AliasResolver implements AliasResolverInterface
{
	/**
	 * Cache class look-ups
	 *
	 * @var boolean[]
	 */
	protected static $cache = array();

	public function resolve(EntityManager $entities, $name, $preference = array())
	{
		if (strpos($name, ':') !== false)
		{
			return $name;
		}

		// Fetch all registered aliases and sort by given preference
		$namespaces = $entities->getConfiguration()->getEntityNamespaces();
		$namespaces = array_filter(array_merge(array_fill_keys($preference, null), $namespaces));

		foreach ($namespaces as $alias => $namespace)
		{
			$className = $namespace .'\\'. $name;
			if (!isset(static::$cache[$className]))
			{
				static::$cache[$className] = class_exists($className);
			}

			if (static::$cache[$className])
			{
				return $alias .':'. $name;
			}
		}

		return $name;
	}
}
