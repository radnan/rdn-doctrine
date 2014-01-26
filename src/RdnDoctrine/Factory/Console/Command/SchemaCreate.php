<?php

namespace RdnDoctrine\Factory\Console\Command;

use RdnConsole\Factory\Command\AbstractCommandFactory;
use RdnDoctrine\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SchemaCreate extends AbstractCommandFactory
{
	protected $name = 'create';

	protected $verbs = array(
		'simple' => 'Create',
		'participle' => 'Creating',
	);

	public function configure()
	{
		$this->adapter
			->setName('doctrine:schema:'. $this->name)
			->setDescription($this->verbs['simple'] .' schema for entities or generate the SQL output')
			->addArgument(
				'manager',
				InputArgument::OPTIONAL,
				'The entity manager to use for this command.',
				'App'
			)
			->addOption(
				'force',
				'f',
				InputOption::VALUE_NONE,
				'Execute the statements against the entity manager database connection.'
			)
			->addOption(
				'dump-sql',
				'd',
				InputOption::VALUE_NONE,
				'Display the generated SQL statements.'
			)
		;
	}

	public function create()
	{
		$managers = $this->services->get('RdnDoctrine\EntityManagerManager');
		return new Command\Schema($this->name, $this->verbs, $managers);
	}
}
