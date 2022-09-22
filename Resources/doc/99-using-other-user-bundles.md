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

namespace App\Form\DataTransformer;

use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Transforms between a User instance and a username string
 */
class UserToUsernameTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * UserToUsernameTransformer constructor.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms a UserInterface instance into a username string.
     *
     * @param UserInterface|null $value UserInterface instance
     *
     * @return string|null Username
     *
     * @throws UnexpectedTypeException if the given value is not a UserInterface instance
     */
    public function transform($value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof UserInterface) {
            throw new UnexpectedTypeException($value, 'Symfony\Component\Security\Core\User\UserInterface');
        }

        return $value->getUserIdentifier();
    }

    /**
     * Transforms a username string into a UserInterface instance.
     *
     * @param string $value Username
     *
     * @return UserInterface|null the corresponding UserInterface instance
     *
     * @throws UnexpectedTypeException if the given value is not a string
     */
    public function reverseTransform($value): ?UserInterface
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        return $this->em->getRepository(User::class)->findOneByIdentifier($value);
    }
}
```

Once your transformer created, you still have to tell FOSMessageBundle to use it.
For the moment, there is no configuration key to do it so we will emulate the
FOSUserBundle transformer by using its name as an alias of our own service:

``` xml
<!-- config/services.xml -->

<service id="app.user_to_username_transformer" class="App\Form\DataTransformer\UserToUsernameTransformer">
</service>

<service id="fos_user.user_to_username_transformer" alias="app.user_to_username_transformer" />
```

Or

``` yaml
# config/services.yaml
services:
    ...
    app.user_to_username_transformer:
        class: App\Form\DataTransformer\UserToUsernameTransformer
    
    fos_user.user_to_username_transformer:
        alias: app.user_to_username_transformer
```

As `NewThreadMessageFormType` used `FOS\UserBundle\Form\Type\UsernameFormType` for recipied field, you need to create
a new form type to replace it

```php
<?php
// src/Form/Type/NewThreadMessageFormType.php

namespace App\Form\Type;

use App\Form\Type\UsernameFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Message form type for starting a new conversation.
 */
class NewThreadMessageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('recipient', UsernameFormType::class, [
                'label' => 'recipient',
                'translation_domain' => 'FOSMessageBundle',
            ])
            ->add('subject', TextType::class, [
                'label' => 'subject',
                'translation_domain' => 'FOSMessageBundle',
            ])
            ->add('body', TextareaType::class, [
                'label' => 'body',
                'translation_domain' => 'FOSMessageBundle',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'intention' => 'message',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'new_thread_message';
    }
}
```

And create a new form type linked to `UserToUsernameTransformer`

```php
<?php
// src/Form/Type/UsernameFormType.php

namespace App\Form\Type;

use App\Form\DataTransformer\UserToUsernameTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form type for representing a UserInterface instance by its username string.
 */
class UsernameFormType extends AbstractType
{
    /**
     * @var UserToUsernameTransformer
     */
    protected $usernameTransformer;

    /**
     * Constructor.
     *
     * @param UserToUsernameTransformer $usernameTransformer
     */
    public function __construct(UserToUsernameTransformer $usernameTransformer)
    {
        $this->usernameTransformer = $usernameTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->usernameTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_username_type';
    }
}

```

### Problems you may encounter

#### User identifier field is not `id`

If the identifier in your User entity is not named `id` (Drupal named it `uid` for instance),
you have to define your own `thread_manager` and `message_manager` to change the requests
made by the bundle.

You can copy the default ones (in `FOS\MessageBundle\EntityManager` if you use the Doctrine ORM)
into your bundle, change their queries and register them as services:

``` yaml
# config/services.yaml

services:
    ...
    App\Manager\MessageManager:
        bind:
            $class: '%fos_message.message_class%'
            $metaClass: '%fos_message.message_meta_class%'

    App\Manager\ThreadManager:
        bind:
            $class: '%fos_message.thread_class%'
            $metaClass: '%fos_message.thread_meta_class%'
```

Once done, tell FOSMessageBundle to use them in the configuration:

``` yaml
# config/fos_message.yaml

fos_message:
	# ...
    thread_manager: App\Manager\ThreadManager
    message_manager: App\Manager\MessageManager
```

#### The default form does not work with my User entity

You have to redefine two things :
  - the form type `FOS\MessageBundle\FormType\NewThreadMessageFormType`
  - the form factory `FOS\MessageBundle\FormType\NewThreadMessageFormType`

You can copy and paste the bundle versions into your application and define them as services:

``` xml
<service id="app.new_thread_form_type" class="App\Form\NewThreadMessageFormType" public="false">
    <argument type="service" id="app.user_to_username_transformer" />
</service>

<service id="app.new_thread_form_factory" class="App\Form\NewThreadMessageFormFactory" public="false">
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
# config/fos_message.yaml

fos_message:
    # ...
    new_thread_form:
        type: app.new_thread_form_type
        factory: app.new_thread_form_factory
```

#### Another problem?

If you have another problem or if this documentation is not clear enough for you to implement your own user system with FOSMessageBundle, don't hesitate to create an issue in the [Github tracker](https://github.com/FriendsOfSymfony/FOSMessageBundle/issues).
