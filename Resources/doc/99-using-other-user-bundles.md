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
// src/AppBundle/Form/DataTransformer/UserToUsernameTransformer.php

namespace AppBundle\Form\DataTransformer;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

use AppBundle\Entity\User;

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
        $this->repository = $doctrine->getManager()->getRepository('AppBundle:User');
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
            throw new UnexpectedTypeException($value, 'AppBundle\Entity\User');
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
<!-- app/config/services.xml -->

<service id="app.user_to_username_transformer" class="AppBundle\Form\DataTransformer\UserToUsernameTransformer">
    <argument type="service" id="doctrine" />
</service>

<service id="fos_user.user_to_username_transformer" alias="app.user_to_username_transformer" />
```

### Problems you may encounter

#### User identifier field is not `id`

If the identifier in your User entity is not named `id` (Drupal named it `uid` for instance),
you have to define your own `thread_manager` and `message_manager` to change the requests
made by the bundle.

You can copy the default ones (in `FOS\MessageBundle\EntityManager` if you use the Doctrine ORM)
into your bundle, change their queries and register them as services:

``` xml
<!-- app/config/services.xml -->

<service id="app.message_manager" class="AppBundle\EntityManager\MessageManager" public="false">
    <argument type="service" id="doctrine.orm.entity_manager" />
    <argument>%fos_message.message_class%</argument>
    <argument>%fos_message.message_meta_class%</argument>
</service>

<service id="app.thread_manager" class="AppBundle\EntityManager\ThreadManager" public="false">
    <argument type="service" id="doctrine.orm.entity_manager" />
    <argument>%fos_message.thread_class%</argument>
    <argument>%fos_message.thread_meta_class%</argument>
    <argument type="service" id="app.message_manager" />
</service>
```

Once done, tell FOSMessageBundle to use them in the configuration:

``` yaml
# app/config/config.yml

fos_message:
	# ...
    thread_manager: app.thread_manager
    message_manager: app.message_manager
```

#### The default form does not work with my User entity

You have to redefine two things :
  - the form type `FOS\MessageBundle\FormType\NewThreadMessageFormType`
  - the form factory `FOS\MessageBundle\FormType\NewThreadMessageFormType`

You can copy and paste the bundle versions into your application and define them as services:

``` xml
<service id="app.new_thread_form_type" class="AppBundle\Form\NewThreadMessageFormType" public="false">
    <argument type="service" id="app.user_to_username_transformer" />
</service>

<service id="app.new_thread_form_factory" class="AppBundle\Form\NewThreadMessageFormFactory" public="false">
    <argument type="service" id="form.factory" />
    <argument type="service" id="fos_message.new_thread_form.type" />
    <argument>%fos_message.new_thread_form.name%</argument>
    <argument>%fos_message.new_thread_form.model%</argument>
    <argument type="service" id="doctrine" />
    <argument type="service" id="request" />
</service>
```

And configure the bundle to use your services:

``` yaml
# app/config/config.yml

fos_message:
    # ...
    new_thread_form:
        type: app.new_thread_form_type
        factory: app.new_thread_form_factory
```

#### Another problem?

If you have another problem or if this documentation is not clear enough for you to implement your own user system with FOSMessageBundle, don't hesitate to create an issue in the [Github tracker](https://github.com/FriendsOfSymfony/FOSMessageBundle/issues).
