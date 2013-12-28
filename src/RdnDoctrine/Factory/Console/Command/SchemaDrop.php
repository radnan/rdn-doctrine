<?php

namespace RdnDoctrine\Factory\Console\Command;

/**
 * Command line utility to update schema for doctrine entities.
 */
class SchemaDrop extends SchemaCreate
{
	protected $name = 'drop';

	protected $verbs = array(
		'simple' => 'Drop',
		'participle' => 'Dropping',
	);
}
