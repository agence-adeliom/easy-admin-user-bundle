<?php

namespace Adeliom\EasyAdminUserBundle;

use Adeliom\EasyAdminUserBundle\DependencyInjection\EasyAdminUserExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EasyAdminUserBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new EasyAdminUserExtension();
    }
}
