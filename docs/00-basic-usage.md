Basic usage
===========

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
