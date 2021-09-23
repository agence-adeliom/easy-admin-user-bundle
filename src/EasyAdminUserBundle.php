<?php

namespace Adeliom\EasyAdminUserBundle;

use Adeliom\EasyAdminUserBundle\DependencyInjection\EasyAdminUserExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EasyAdminUserBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new EasyAdminUserExtension();
    }
}
