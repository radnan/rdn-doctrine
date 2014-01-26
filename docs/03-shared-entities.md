Shared Entities
===============

Usually you will have one module that will contain all your common entities such as `User` entities etc. You will also register a single entity manager for your application with the same name as this module.

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
