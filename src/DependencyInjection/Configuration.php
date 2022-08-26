<?php

namespace Adeliom\EasyAdminUserBundle\DependencyInjection;

use Adeliom\EasyAdminUserBundle\Entity\ResetPasswordRequest;
use Adeliom\EasyAdminUserBundle\Entity\User;
use Adeliom\EasyAdminUserBundle\Repository\ResetPasswordRequestRepository;
use Adeliom\EasyAdminUserBundle\Repository\UserRepository;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('easy_admin_user');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('user_class')
                    ->defaultValue(User::class)
                    ->validate()
                        ->ifString()
                        ->then(static function ($value): string {
                            if (!class_exists($value) || !is_a($value, User::class, true)) {
                                throw new InvalidConfigurationException(sprintf('User class must be a valid class extending %s. "%s" given.', User::class, $value));
                            }

                            return $value;
                        })
                    ->end()
                ->end()
                ->scalarNode('user_repository')
                    ->defaultValue(UserRepository::class)
                    ->validate()
                        ->ifString()
                        ->then(static function ($value): string {
                            if (!class_exists($value) || !is_a($value, UserRepository::class, true)) {
                                throw new InvalidConfigurationException(sprintf('User repository must be a valid class extending %s. "%s" given.', UserRepository::class, $value));
                            }

                            return $value;
                        })
                    ->end()
                ->end()
                ->scalarNode('reset_password_class')
                    ->defaultValue(ResetPasswordRequest::class)
                    ->validate()
                        ->ifString()
                        ->then(static function ($value): string {
                            if (!class_exists($value) || !is_a($value, ResetPasswordRequest::class, true)) {
                                throw new InvalidConfigurationException(sprintf('Reset password class must be a valid class extending %s. "%s" given.', ResetPasswordRequest::class, $value));
                            }

                            return $value;
                        })
                    ->end()
                ->end()
                ->scalarNode('reset_password_repository')
                    ->defaultValue(ResetPasswordRequestRepository::class)
                    ->validate()
                        ->ifString()
                        ->then(static function ($value): string {
                            if (!class_exists($value) || !is_a($value, ResetPasswordRequestRepository::class, true)) {
                                throw new InvalidConfigurationException(sprintf('Reset password repository must be a valid class extending %s. "%s" given.', ResetPasswordRequestRepository::class, $value));
                            }

                            return $value;
                        })
                    ->end()
                ->end()
                ->arrayNode('reset_password')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('from_address')->defaultValue('no-reply@example.com')->end()
                        ->scalarNode('from_name')->defaultValue('John Doe')->end()
                    ->end()
                ->end()
                ->scalarNode('title')->defaultValue('ACME')->end()
            ->end();

        return $treeBuilder;
    }
}
