<?php

namespace Adeliom\EasyAdminUserBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * This class adds automatically the ManyToOne and OneToMany relations in Page and Category entities,
 * because it's normally impossible to do so in a mapped superclass.
 */
class DoctrineMappingListener implements EventSubscriber
{
    public function __construct(
        /**
         * @readonly
         */
        private string $userClass,
        /**
         * @readonly
         */
        private string $resetClass
    ) {
    }

    /**
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [Events::loadClassMetadata];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $isResetClass = is_a($classMetadata->getName(), $this->resetClass, true);

        if ($isResetClass) {
            $this->process($classMetadata, $this->userClass);
        }
    }

    private function process(ClassMetadata $classMetadata, string $class): void
    {
        if (!$classMetadata->hasAssociation('user')) {
            $classMetadata->mapManyToOne([
                'fieldName' => 'user',
                'targetEntity' => $class,
            ]);
        }
    }
}
