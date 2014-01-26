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

		'configs' => array(
			'EntityManagerLoader' => array(
				'cache_provider' => 'ArrayCache',
				'cache_dir' => 'data/cache/doctrine',

				'connection' => 'default',

				'custom_hydration_modes' => array(),
				'custom_datetime_functions' => array(),
				'custom_numeric_functions' => array(),
				'custom_string_functions' => array(),
				'filters' => array(),
				'types' => array(
					/*
					 * Doctrine doesn't yet support enums. So we just use the
					 * `string` type instead.
					 */
					'string' => array(
						'dbType' => 'enum',
					),
				),

				'entity_namespaces' => array(),
				'metadata_paths' => array(),
				'simple_annotation' => false,

				'proxy_autogenerate' => \Doctrine\ORM\Proxy\ProxyFactory::AUTOGENERATE_ALWAYS,
				'proxy_namespace' => null,
				'proxy_path' => 'data/proxies',

				'log_sql' => true,
			),
		),
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
