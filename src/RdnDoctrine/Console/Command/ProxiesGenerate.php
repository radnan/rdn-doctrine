<?php

namespace RdnDoctrine\Console\Command;

use Doctrine\ORM\EntityManager;
use RdnConsole\Command\AbstractCommand;
use RdnDoctrine\EntityManagerManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command line utility to generate all doctrine proxies.
 */
class ProxiesGenerate extends AbstractCommand
{
	protected $managers;

	protected $managerNames;

	public function __construct(EntityManagerManager $managers, $managerNames)
	{
		$this->managers = $managers;
		$this->managerNames = $managerNames;
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$name = $input->getOption('em');
		if ($name)
		{
			$names = array($name);
		}
		else
		{
			$names = $this->managerNames;
		}

		$output->writeln('<info>Generating entity proxies</info>');
		$nothing = true;

		foreach ($names as $name)
		{
			/** @var EntityManager $manager */
			$manager = $this->managers->get($name);
			$metadata = $manager->getMetadataFactory()->getAllMetadata();
			$manager->getProxyFactory()->generateProxyClasses($metadata);
			$output->writeln(" - <comment>{$name}</comment>");

			$nothing = false;
		}

		if ($nothing)
		{
			$output->writeln('Nothing to generate');
		}
	}
}
