<?php

namespace App\Tests\Support;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as BaseKernelTestCase;

abstract class KernelTestCase extends BaseKernelTestCase
{
    protected function getContainerService(string $id) : mixed 
    {
        return self::getContainer()->get($id);
    }
}