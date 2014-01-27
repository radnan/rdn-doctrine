Controller plugin
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

The `RdnDoctrine\EntityManager\AliasResolver` service is used to resolve aliases when one is not provided. For example, if `User` is given instead of `App:User`.

## Code completion

If you'd like to have code completion for this plugin, include the following in your <code>AbstractController</code> class:

~~~php
namespace App\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * @method \Doctrine\ORM\EntityRepository|\Doctrine\ORM\EntityManager entity(\string $name = null) Get the entity manager or a repository for given entity name.
 */
abstract class AbstractController extends AbstractActionController
{
}

~~~

Then, simply extend your controllers off of this abstract controller.
