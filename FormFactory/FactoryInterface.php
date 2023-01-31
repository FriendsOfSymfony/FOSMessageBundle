<?php

namespace FOS\MessageBundle\FormFactory;

use FOS\MessageBundle\Model\ThreadInterface;
use Symfony\Component\Form\FormInterface;

interface FactoryInterface
{
    /**
     * @return FormInterface
     */
    public function create(?ThreadInterface $thread = null);
}
