<?php

namespace App\Repository\Factory;

// This solution is a gist from github (https://gist.github.com/docteurklein/9778800)
// slightly adjusted

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $factory = $container->findDefinition('App\Repository\Factory\RepositoryCreator');
        $taggedServices = $container->findTaggedServiceIds('app.repository_service');
        $definition = new Definition();
        // only to initialize, will be overridden later
        $repositories = [];
        $param['class'] = $definition->getClass();
        foreach ($taggedServices as $id => $params) {
            foreach ($params as $param) {
                if (isset($param['class'])) {
                    $repositories[$param['class']] = $id;
                }
                $repository = $container->findDefinition($id);
                $repository->replaceArgument(0, new Reference('doctrine.orm.default_entity_manager'));
                $definition->setClass('Doctrine\ORM\Mapping\ClassMetadata');
                $definition->setFactory([new Reference('doctrine.orm.default_entity_manager'), 'getClassMetadata']);
                $repository->replaceArgument(1, $definition);
            }
            if (!empty($param['class'])) {
                $definition->setArguments([$param['class']]);
            }
        }
        if (!empty($repositories) || null !== $repositories) {
            $factory->replaceArgument(0, $repositories);
            $container->findDefinition('doctrine.orm.configuration')
                ->addMethodCall('setRepositoryFactory', [$factory]);
        }
    }
}