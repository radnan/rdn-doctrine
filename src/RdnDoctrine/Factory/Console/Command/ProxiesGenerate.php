<?php

namespace RdnDoctrine\Factory\Console\Command;

use RdnConsole\Factory\Command\AbstractCommandFactory;
use RdnDoctrine\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class ProxiesGenerate extends AbstractCommandFactory
{
	public function configure()
	{
		$this->adapter
			->setName('doctrine:proxies:generate')
			->setDescription('Generate proxy classes for all entities')
			->addOption(
				'em',
				null,
				InputOption::VALUE_REQUIRED,
				'The entity manager to use for this command.'
			)
		;
	}

	protected function create()
	{
		$managers = $this->service('RdnDoctrine\EntityManagerManager');
		$names = array_keys($this->config('rdn_entity_managers', 'managers'));

		return new Command\ProxiesGenerate($managers, $names);
	}
}
