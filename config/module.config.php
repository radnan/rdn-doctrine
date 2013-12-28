<?php

return array(
	'controller_plugins' => array(
		'aliases' => array(
			'Entity' => 'RdnDoctrine:Entity',
		),

		'factories' => array(
			'RdnDoctrine:Entity' => 'RdnDoctrine\Factory\Controller\Plugin\Entity',
		),
	),

	'rdn_console' => array(
		'commands' => array(
			'RdnDoctrine:ProxiesGenerate',
			'RdnDoctrine:SchemaCreate',
			'RdnDoctrine:SchemaDrop',
			'RdnDoctrine:SchemaUpdate',
		),
	),

	'rdn_console_commands' => array(
		'factories' => array(
			'RdnDoctrine:ProxiesGenerate' => 'RdnDoctrine\Factory\Console\Command\ProxiesGenerate',
			'RdnDoctrine:SchemaCreate' => 'RdnDoctrine\Factory\Console\Command\SchemaCreate',
			'RdnDoctrine:SchemaDrop' => 'RdnDoctrine\Factory\Console\Command\SchemaDrop',
			'RdnDoctrine:SchemaUpdate' => 'RdnDoctrine\Factory\Console\Command\SchemaUpdate',
		),
	),

	'rdn_entity_managers' => array(
		'abstract_factories' => array(
			'EntityManagerLoader' => 'RdnDoctrine\Factory\EntityManagerLoader',
		),
		'managers' => array(),
		'modules' => array(),
	),

	'service_manager' => array(
		'factories' => array(
			'RdnDoctrine\EntityManagerManager' => 'RdnDoctrine\Factory\EntityManagerManager',
		),

		'invokables' => array(
			'RdnDoctrine\EntityManager\AliasResolver' => 'RdnDoctrine\EntityManager\AliasResolver',
		),
	),
);
