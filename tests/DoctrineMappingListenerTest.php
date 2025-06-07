<?php
declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests;

use Adeliom\EasyAdminUserBundle\Entity\ResetPasswordRequest;
use Adeliom\EasyAdminUserBundle\Entity\User;
use Adeliom\EasyAdminUserBundle\EventListener\DoctrineMappingListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;

class DoctrineMappingListenerTest extends TestCase
{
    public function testMappingIsAdded(): void
    {
        $listener = new DoctrineMappingListener(User::class, ResetPasswordRequest::class);
        $metadata = new ClassMetadata(ResetPasswordRequest::class);
        $args = new LoadClassMetadataEventArgs($metadata, $this->createMock(EntityManagerInterface::class));

        $listener->loadClassMetadata($args);

        self::assertTrue($metadata->hasAssociation('user'));
    }
}
