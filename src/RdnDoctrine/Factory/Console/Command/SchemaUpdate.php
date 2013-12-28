<?php

namespace RdnDoctrine\Factory\Console\Command;

/**
 * Command line utility to update schema for doctrine entities.
 */
class SchemaUpdate extends SchemaCreate
{
	protected $name = 'update';

	protected $verbs = array(
		'simple' => 'Update',
		'participle' => 'Updating',
	);
}
