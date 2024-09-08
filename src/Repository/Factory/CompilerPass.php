<?php

namespace App\Repository\Factory;

// This solution is a gist from github (https://gist.github.com/docteurklein/9778800)
// slightly adjusted process()-method

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $factory = $container->findDefinition('app.doctrine.repository.factory');
        $repositories = [];

        foreach ($container->findTaggedServiceIds('app.repository') as $id => $params) {
            if (is_string($params[0]['class']) && is_string($id)) {
                    $repositories[$params[0]['class']] = $id;
            }
            $repository = $container->findDefinition($id);
            $repository->replaceArgument(0, new Reference('doctrine.orm.default_entity_manager'));

            $definition = new Definition;
            $definition->setClass('Doctrine\ORM\Mapping\ClassMetadata');
            $definition->setFactory('doctrine.orm.default_entity_manager');
            $definition->setArguments([$params[0]['class']]);
            $repository->replaceArgument(1, $definition);
        }
        $factory->replaceArgument(0, $repositories);

        $container->findDefinition('doctrine.orm.configuration')->addMethodCall('setRepositoryFactory', [$factory]);
    }
}