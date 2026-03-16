<?php

declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests\EventListener;

use Adeliom\EasyAdminUserBundle\Entity\ResetPasswordRequest;
use Adeliom\EasyAdminUserBundle\Entity\User;
use Adeliom\EasyAdminUserBundle\EventListener\DoctrineMappingListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Adeliom\EasyAdminUserBundle\EventListener\DoctrineMappingListener::class)]
final class DoctrineMappingListenerTest extends TestCase
{
    public function testLoadClassMetadataMapsUserAssociationForResetPasswordClass(): void
    {
        $metadata = new ClassMetadata(ResetPasswordRequest::class);
        $listener = new DoctrineMappingListener(User::class, ResetPasswordRequest::class);
        $event = new LoadClassMetadataEventArgs($metadata, $this->createMock(EntityManagerInterface::class));

        $listener->loadClassMetadata($event);

        $mapping = $metadata->getAssociationMapping('user');

        self::assertSame('user', $mapping->fieldName);
        self::assertSame(User::class, $mapping->targetEntity);
    }

    public function testLoadClassMetadataSkipsUnrelatedClassesAndExistingAssociation(): void
    {
        $listener = new DoctrineMappingListener(User::class, ResetPasswordRequest::class);
        $manager = $this->createMock(EntityManagerInterface::class);

        $userMetadata = new ClassMetadata(User::class);
        $listener->loadClassMetadata(new LoadClassMetadataEventArgs($userMetadata, $manager));
        self::assertFalse($userMetadata->hasAssociation('user'));

        $resetMetadata = new ClassMetadata(ResetPasswordRequest::class);
        $event = new LoadClassMetadataEventArgs($resetMetadata, $manager);

        $listener->loadClassMetadata($event);
        $listener->loadClassMetadata($event);

        self::assertCount(1, $resetMetadata->getAssociationMappings());
    }
}
