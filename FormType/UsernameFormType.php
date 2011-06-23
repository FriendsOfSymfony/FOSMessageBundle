<?php

namespace Ornicar\MessageBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use FOS\UserBundle\Model\UserManagerInterface;
use Ornicar\MessageBundle\DataTransformer\UsernameToUserTransformer;

class UsernameFormType extends AbstractType
{
    /**
     * @var UsernameToUserTransformer
     */
    protected $usernameTransformer;

    public function __construct(UsernameToUserTransformer $usernameTransformer)
    {
        $this->usernameTransformer = $usernameTransformer;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->appendClientTransformer($this->usernameTransformer);
    }

    public function getParent(array $options)
    {
        return 'text';
    }

    public function getName()
    {
        return 'ornicar_message.username';
    }
}
