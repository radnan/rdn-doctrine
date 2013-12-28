<?php

namespace RdnDoctrine\EntityManager;

use Doctrine\ORM\EntityManager;

interface AliasResolverInterface
{
	/**
	 * Resolve an entity short name (ShortName) to the full name with the alias prefix (Alias:ShortName).
	 *
	 * @param EntityManager $entities The entity manager
	 * @param string $name The short name to resolve
	 * @param array $preference Aliases in order of preference in case of duplicates
	 *
	 * @return string
	 */
	public function resolve(EntityManager $entities, $name, $preference = array());
}
