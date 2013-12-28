<?php

namespace RdnDoctrine\ORM\Mapping;

use Doctrine\ORM\Mapping\DefaultNamingStrategy as DoctrineNamingStrategy;

class DefaultNamingStrategy extends DoctrineNamingStrategy
{
	/**
	 * An array of module name to table prefix.
	 *
	 * @var array
	 */
	protected $tablePrefixes = array();

	/**
	 * @param array $tablePrefixes
	 */
	public function setTablePrefixes($tablePrefixes)
	{
		$this->tablePrefixes = $tablePrefixes;
	}

	/**
	 * @return array
	 */
	public function getTablePrefixes()
	{
		return $this->tablePrefixes;
	}

	protected function prependTablePrefix($tableName, $className)
	{
		$module = strstr($className, '\\', true);
		if (isset($this->tablePrefixes[$module]))
		{
			$prefix = $this->tablePrefixes[$module];
			return $prefix . $tableName;
		}
		return $tableName;
	}

	public function classToTableName($className)
	{
		$tableName = parent::classToTableName($className);
		return $this->prependTablePrefix($tableName, $className);
	}
}
