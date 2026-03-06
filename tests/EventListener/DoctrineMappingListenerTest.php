<?php

declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests\EventListener;

use Adeliom\EasyAdminUserBundle\Entity\ResetPasswordRequest;
use Adeliom\EasyAdminUserBundle\Entity\User;
use Adeliom\EasyAdminUserBundle\EventListener\DoctrineMappingListener;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Adeliom\EasyAdminUserBundle\EventListener\DoctrineMappingListener::class)]
final class DoctrineMappingListenerTest extends TestCase
{
    public function testLoadClassMetadataMapsUserAssociationForResetPasswordClass(): void
    {
        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->expects(self::once())
            ->method('getName')
            ->willReturn(ResetPasswordRequest::class);
        $metadata->expects(self::once())
            ->method('hasAssociation')
            ->with('user')
            ->willReturn(false);
        $metadata->expects(self::once())
            ->method('mapManyToOne')
            ->with([
                'fieldName' => 'user',
                'targetEntity' => User::class,
            ]);

        $event = $this->createMock(LoadClassMetadataEventArgs::class);
        $event->expects(self::once())
            ->method('getClassMetadata')
            ->willReturn($metadata);

        $listener = new DoctrineMappingListener(User::class, ResetPasswordRequest::class);

        $listener->loadClassMetadata($event);
    }

    public function testLoadClassMetadataSkipsUnrelatedClassesAndExistingAssociation(): void
    {
        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->expects(self::exactly(2))
            ->method('getName')
            ->willReturnOnConsecutiveCalls(User::class, ResetPasswordRequest::class);
        $metadata->expects(self::once())
            ->method('hasAssociation')
            ->with('user')
            ->willReturn(true);
        $metadata->expects(self::never())->method('mapManyToOne');

        $event = $this->createMock(LoadClassMetadataEventArgs::class);
        $event->expects(self::exactly(2))
            ->method('getClassMetadata')
            ->willReturn($metadata);

        $listener = new DoctrineMappingListener(User::class, ResetPasswordRequest::class);

        $listener->loadClassMetadata($event);
        $listener->loadClassMetadata($event);
    }
}
