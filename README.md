RdnDoctrine
===========

The **RdnDoctrine** ZF2 module is a simple bridge to the Doctrine ORM library.

## How to install

1. Use `composer` to require the `radnan/rdn-doctrine` package:

   ~~~bash
   $ composer require radnan/rdn-doctrine:1.*
   ~~~

2. Activate the module by including it in your `application.config.php` file:

   ~~~php
   <?php

   return array(
       'modules' => array(
           'RdnDoctrine',
           // ...
       ),
   );
   ~~~

### Dependencies

This module relies on the modules: [RdnConsole](/radnan/rdn-console), [RdnDatabase](/radnan/rdn-database), and [RdnFactory](/radnan/rdn-factory). I've submitted a [pull request](https://github.com/zendframework/zf2/pull/5651) to automate this step, but for now you have to specify them manually.
~~~php
<?php

return array(
    'modules' => array(
        'RdnConsole',
        'RdnDatabase',
        'RdnFactory',
        'RdnDoctrine',
        // ...
    ),
);
~~~

## How to use

Entity managers can be registered with the `RdnDoctrine\EntityManagerManager` service locator using the `rdn_entity_managers` configuration option.

~~~php
<?php

return array(
	'rdn_entity_managers' => array(
		'factories' => array(),
		'invokables' => array(),
	),
);
~~~

You can also use the **managers** key to quickly generate an entity manager using simple configuration options:

~~~php
<?php

return array(
	'rdn_entity_managers' => array(
		'managers' => array(
			'App' => array(),
		),
	),
);
~~~

Configuring an entity manager is as simple as that! Here we've configured an entity manager with the name **App**. Assuming our module name is also **App** the library will set you up with some sane defaults, all of which you can override.

By default, the manager will expect your entities to live inside the `App\Entity` namespace (or more generally `<MANAGER-NAME>\Entity`).

[Configuration documentation](docs/01-config.md)

### Controller plugin

Once an entity manager has been configured, you can access both the entity manager or entity repositories from your controller using the `entity()` plugin.

Since you can register multiple entity managers with different names, by default the plugin will fetch the entity manager with the same name as the module name:

~~~php
namespace App\Controller;

use App\Entity;

class User
{
	public function createAction()
	{
		$user = new Entity\User;
		$user->setEmail('pot@example.com');

		$this->entity()->persist($user);
		$this->entity()->flush();
	}
}
~~~

In order to access an entity repository we call the same `entity($name)` plugin, only this time we provide an entity name:

~~~php
namespace App\Controller;

use App\Entity;

class User
{
	public function editAction()
	{
		$id = $this->params('user-id');

		$user = $this->entity('User')->find($id);

		/**
		 * Alternatively we can be more explicit and request the
		 * User entity within the App module
		 */
		$user = $this->entity('App:User')->find($id);
	}
}
~~~

### Console commands

The module also comes with a set of console commands to manage the database schema and generate proxies.

You can run `vendor/bin/console` to run and get help on the console commands in the `doctrine:` namespace.

### Shared entities

Usually you will have one module that will contain all your common entities such as User entities etc. You will also register a single entity manager for your application with the same name as this module.

You will then create separate modules for each section of your site. Each module will depend on the entities provided by the common module in addition to providing its own. But all of the modules will use the single shared entity manager.

Let's say our common module is called **App** and we have another module called **Foo**. In this case the configuration for the **Foo** module would look like this:

~~~php
<?php

return array(
	'rdn_entity_managers' => array(
		'managers' => array(
			'App' => array(
				'table_prefixes' => array(
					'Foo' => 'foo__',
				),
			),
		),

		'modules' => array(
			'Foo' => 'App',
		),
	),
);
~~~

This will add the entities provided by **Foo** (in the `Foo\Entity` namespace) to the **App** entity manager and instruct all plugins to use the **App** entity manager from within the **Foo** module.

~~~php
namespace Foo\Controller;

class Bar
{
	public function editAction()
	{
		// We can now access the Foo entity repositories
		$bar = $this->entity('Bar')->find($id);
		// - OR - more explicitly
		$bar = $this->entity('Foo:Bar')->find($id);

		// We can also access the App entity repositories
		$user = $this->entity('User')->find($id);
		// - OR - more explicitly
		$user = $this->entity('App:User')->find($id);

		// We have access to the shared entity manager
		$this->entity()->flush();
	}
}
~~~
