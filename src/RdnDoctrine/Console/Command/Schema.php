<?php

namespace RdnDoctrine\Console\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\SchemaTool;
use RdnConsole\Command\AbstractCommand;
use RdnDoctrine\EntityManagerManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command line utility to create schema for doctrine entities.
 */
class Schema extends AbstractCommand
{
	protected $name;

	protected $verbs = array();

	protected $managers;

	public function __construct($name, $verbs, EntityManagerManager $managers)
	{
		$this->name = $name;
		$this->verbs = $verbs;
		$this->managers = $managers;
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$name = $input->getArgument('manager');
		if (!$this->managers->has($name))
		{
			throw new \InvalidArgumentException("Could not find entity manager ($name)");
		}

		/** @var EntityManager $manager */
		$manager = $this->managers->get($name);

		$force = $input->getOption('force');
		$dumpSql = $input->getOption('dump-sql');

		$tool = new SchemaTool($manager);
		$metadata = $manager->getMetadataFactory()->getAllMetadata();
		$metadata = array_filter($metadata, function(ClassMetadata $metadata)
		{
			return !$metadata->isReadOnly;
		});
		$sql = $tool->{'get'. ucfirst($this->name) .'SchemaSql'}($metadata, true);
		$database = $manager->getConnection()->getDatabase();

		if (empty($sql))
		{
			$output->writeln('Nothing to do - your database is already in sync with the current entity metadata');
			return;
		}

		if ($force)
		{
			$output->writeln('<info>'. $this->verbs['participle'] .' database schema...</info>');
			$tool->{$this->name .'Schema'}($metadata, true);
		}

		if (!$force && !$dumpSql)
		{
			$output->writeln(array(
				sprintf(
					'<info>%s</info> statements will be executed on the database <info>%s</info>'
					, count($sql)
					, $database
				),
				'',
				'Please run the operation by passing one of the following options:',
				sprintf('    <info>%s -f|--force</info> to execute the SQL', $this->adapter->getName()),
				sprintf('    <info>%s -d|--dump-sql</info> to dump the SQL to the screen', $this->adapter->getName()),
				'',
				'<comment>ATTENTION:</comment> This action is final! Be absolutely sure you want to execute the SQL.',
			));
		}

		if ($dumpSql)
		{
			$output->writeln(implode(";\n", $sql));
			if (!$force)
			{
				$output->writeln(array(
					'',
					sprintf(
						'<info>%s</info> statements will be executed on the database <info>%s</info>'
						, count($sql)
						, $database
					),
					'',
					sprintf('Please run <info>%s -f|--force</info> to execute the SQL', $this->adapter->getName()),
					'',
					'<comment>ATTENTION:</comment> This action is final! Be absolutely sure you want to execute the SQL.',
				));
			}
		}
	}
}
