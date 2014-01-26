Configuration
=============

An `abstract_factory` is used to dynamically load an entity manager. The factory is defined in the following way:

~~~php
<?php

return array(
	'rdn_entity_managers' => array(
		'abstract_factories' => array(
			'EntityManagerLoader' => 'RdnDoctrine\Factory\EntityManagerLoader',
		),
	),
);
~~~

Options
-------

The following configuration options are available when creating an entity manager using this abstract factory:

### `cache_provider`

Specify the short name of a cache provider class inside the `Doctrine\Common\Cache` namespace.

[Doctrine documentation on cache providers](http://docs.doctrine-project.org/en/latest/reference/caching.html)

**Default:** `'ArrayCache'`

### `cache_dir`

Specify the directory where cache files will be stored if the `FilesystemCache` provider is used.

**Default:** `'data/cache/doctrine'`

### `connection`

Specify the database connection using **strings** or **arrays**.

If case of a **string**, the loader will use the `RdnDatabase\Adapter\AdapterManager` service to fetch a database connection using the given string.

If case of an **array**, the loader will simply pass it as the first argument to the `Doctrine\ORM\EntityManager::create()` method.

**Default:** `'default'`

### `custom_hydration_modes`

Specify custom hydration modes as an array of **names** to **class names**.

[Doctrine documentation on hydration modes](http://docs.doctrine-project.org/en/2.1/reference/dql-doctrine-query-language.html#custom-hydration-modes)

**Default:** `array()`

### `custom_datetime_functions`

Specify custom DQL datetime functions as an array of **names** to **class names**.

[Doctrine documentation on datetime functions](http://docs.doctrine-project.org/en/2.1/reference/dql-doctrine-query-language.html#adding-your-own-functions-to-the-dql-language)

**Default:** `array()`

### `custom_numeric_functions`

Specify custom DQL numeric functions as an array of **names** to **class names**.

[Doctrine documentation on numeric functions](http://docs.doctrine-project.org/en/2.1/reference/dql-doctrine-query-language.html#adding-your-own-functions-to-the-dql-language)

**Default:** `array()`

### `custom_string_functions`

Specify custom DQL string functions as an array of **names** to **class names**.

[Doctrine documentation on string functions](http://docs.doctrine-project.org/en/2.1/reference/dql-doctrine-query-language.html#adding-your-own-functions-to-the-dql-language)

**Default:** `array()`

### `filters`

Specify custom filters as an array of **names** to **class names**.

[Doctrine documentation on filters](http://docs.doctrine-project.org/en/latest/reference/filters.html)

**Default:** `array()`

### `types`

Specify custom types as an array of **names** to **options** where options can be a **string** or an **array**.

If a **string**, the option is used as the class name for the type and the name is used as both the db type and the doctrine type name.

If an **array**, the array is expected to have the `dbType` key. You can also optionally provide the `name` and `className` key.

[Doctrine documentation on types](http://docs.doctrine-project.org/en/latest/cookbook/custom-mapping-types.html)

**Default:** `array('string' => array('dbType' => 'enum'))`

### `entity_namespaces`

Specify mappings between an **alias** and the **namespace** containing entities for that alias.

The default is created dynamically by using the **ModuleName** as the key and **ModuleName\Entity** as the namespace. First we assume there is a module with the same name as the entity manager. Then we collect all the modules that point to this entity manager as their default manager.

The `RdnDoctrine\EntityManager\AliasResolver` service is used to resolve aliases when one is not provided.

**Default:** `array('ModuleName' => 'ModuleName\\Entity')`

### `metadata_paths`

Specify an array of filesystem locations where entity classes can be located.

The default is created dynamically by using the **ModuleName** as the key and **/path/to/module/src/ModuleName/Entity** as the namespace. If the module implements a `getPath()` method then `"/Entity"` is appended to the output of that and used instead. We use the same strategy as `entity_namespaces` to select the default modules.

[Doctrine documentation on metadata paths](http://docs.doctrine-project.org/en/latest/reference/advanced-configuration.html#metadata-driver-required)

**Default:** `array('ModuleName' => 'ModuleName\\Entity')`

### `table_prefixes`

An array of **ModuleName** to **table prefix**.  This is used by the default naming strategy class `RdnDoctrine\ORM\Mapping\DefaultNamingStrategy` to prepend a prefix to entity table names.

**Default:** `null`

### `simple_annotation`

Whether to use simple annotations. You must enable to this to use annotations without namespaces like `@Entity` (instead of `@ORM\Entity`).

**Default:** `false`

### `proxy_autogenerate`

Whether to auto generate proxy classes each time a proxy class is requested.

[Doctrine documentation on proxy auto generation](http://docs.doctrine-project.org/en/latest/reference/advanced-configuration.html#auto-generating-proxy-classes-optional)

**Default:** `Doctrine\ORM\Proxy\ProxyFactory::AUTOGENERATE_ALWAYS`

### `proxy_namespace`

Namespace for generated entity classes.

[Doctrine documentation on proxy namespace](http://docs.doctrine-project.org/en/latest/reference/advanced-configuration.html#proxy-namespace-required)

**Default:** `'<ManagerName>\\Entity\\Proxy'`

### `proxy_dir`

The filepath where generated proxy classes will be stored.

[Doctrine documentation on proxy dir](http://docs.doctrine-project.org/en/latest/reference/advanced-configuration.html#proxy-directory-required)

**Default:** `'data/proxies'`

### `log_sql`

Whether to log SQL queries using the `RdnDoctrine\DBAL\Logging\Profiler` class.

This profiler has an event manager which is connected to the shared event manager using the identifiers: `__CLASS__`, and `get_called_class()`. The profiler will trigger the `RdnDoctrine\DBAL\Logging\Profiler::EVENT_LOG_QUERY` event if logging is turned on.

The event will contain the parameters:

#### `manager`

`string` entity manager name.

#### `query`

`array` query details containing the following keys:

~~~php
array(
	'sql' => '...', // SQL query
	'parameters' => [], // query parameters
	'types' => $types, // query parameter types
	'start' => 123.12, // query start time in micro-seconds
	'stop' => 123.12, // query stop time in micro-seconds
	'elapse' => 123.12, // total query time in micro-seconds
	'logged' => true, // whether this query was logged or not
)
~~~

**Default:** `false`

Defaults
--------

We can configure the above defaults for our abstract factory using the <code>rdn_entity_managers.configs.EntityManagerLoader</code> configuration option:

~~~php
<?php

/*
 * Let's assume our front controller defines an `APPLICATION_ENV` constant.
 * Depending on our application environment we can enable/disable proxy auto
 * generation.
 */

return array(
	'rdn_entity_managers' => array(
		'configs' => array(
			'EntityManagerLoader' => array(
				'proxy_autogenerate' => APPLICATION_ENV === 'development',
			),
		),
	),
);
~~~
