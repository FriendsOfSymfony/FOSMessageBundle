<?php

namespace FOS\MessageBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;
use PHPUnit\Framework\MockObject\MockBuilder;

class WebTestCase extends BaseWebTestCase
{
    protected static function getKernelClass()
    {
        return TestKernel::class;
    }
}
