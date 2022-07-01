<?php

namespace Adeliom\EasyAdminUserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


class EasyAdminUserExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @param string[] $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        foreach ($config as $key => $value) {
            $container->setParameter('easy_admin_user.'.$key, $value);
        }

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        $container->prependExtensionConfig('easy_admin_user', $config);

        $twigConfig = [];

        $twigConfig['globals']['easy_admin_user'] = [];
        foreach ($config as $k=>$v){
            $twigConfig['globals']['easy_admin_user'][$k] = $v;
        }
        $container->prependExtensionConfig('twig', $twigConfig);
    }

    public function getAlias(): string
    {
        return 'easy_admin_user';
    }
}
