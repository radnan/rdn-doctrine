Controller Plugin
=================

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
		 * `User` entity within the `App` module
		 */
		$user = $this->entity('App:User')->find($id);
	}
}
~~~
