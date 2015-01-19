Using other UserBundles
=======================

By default, FOSMessageBundle depends on the UserToUsername data transformer provided by FOSUserBundle.
However, if you do not use FOSUserBundle, it is possible to implement your own version of this
transformer and tell to FOSMessageBundle to use it.

> **Note**: For many cases, just implementing your own UserToUsername transformer will be enough, but
> depending on how your users system works you may need to change other things.

The transformer is just a service that know how to transform usernames into User objects and vice-versa.
You can base your own on this one:

``` php
<?php

namespace Acme\UserBundle\Form\DataTransformer;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

use Acme\UserBundle\Entity\User;

/**
 * Transforms between a User instance and a username string
 */
class UserToUsernameTransformer implements DataTransformerInterface
{
    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->repository = $doctrine->getManager()->getRepository('AcmeUserBundle:User');
    }

    /**
     * Transforms a User instance into a username string.
     *
     * @param User|null $value User instance
     *
     * @return string|null Username
     *
     * @throws UnexpectedTypeException if the given value is not a User instance
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (! $value instanceof User) {
            throw new UnexpectedTypeException($value, 'Acme\UserBundle\Entity\User');
        }

        return $value->getUsername();
    }

    /**
     * Transforms a username string into a User instance.
     *
     * @param string $value Username
     *
     * @return User the corresponding User instance
     *
     * @throws UnexpectedTypeException if the given value is not a string
     */
    public function reverseTransform($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (! is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        return $this->repository->findOneByUsername($value);
    }
}
```

Once your transformer created, you still have to tell FOSMessageBundle to use it.
For the moment, there is no configuration key to do it so we will emulate the
FOSUserBundle transformer by using its name as an alias of our own service:

``` xml
<service id="acme_user.user_to_username_transformer" class="Acme\UserBundle\Form\DataTransformer\UserToUsernameTransformer">
    <argument type="service" id="doctrine" />
</service>

<service id="fos_user.user_to_username_transformer" alias="acme_user.user_to_username_transformer" />
```


### Other problems you may encounter

#### User identifier field is not `id`

If the identifier in your User entity is not named `id` (Drupal named it `uid` for instance),
you have to define your own `thread_manager` and `message_manager` to change the requests
made by the bundle.

You can copy the default ones (in `FOS\MessageBundle\EntityManager` if you use the Doctrine ORM)
into your bundle, change their queries and register them as services:

``` xml
<service id="acme_user.message_manager" class="Acme\UserBundle\EntityManager\MessageManager" public="false">
    <argument type="service" id="doctrine.orm.entity_manager" />
    <argument>%fos_message.message_class%</argument>
    <argument>%fos_message.message_meta_class%</argument>
</service>

<service id="acme_user.thread_manager" class="Acme\UserBundle\EntityManager\ThreadManager" public="false">
    <argument type="service" id="doctrine.orm.entity_manager" />
    <argument>%fos_message.thread_class%</argument>
    <argument>%fos_message.thread_meta_class%</argument>
    <argument type="service" id="acme_user.message_manager" />
</service>
```

Once done, tell FOSMessageBundle to use them in the configuration:

``` yaml
# Messages
fos_message:
	# ...
    thread_manager: acme_user.thread_manager
    message_manager: acme_user.message_manager
```
