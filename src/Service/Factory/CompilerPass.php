<?php

namespace App\Service\Factory;

// This solution is a gist from github (https://gist.github.com/docteurklein/9778800)
// adjusted

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

class CompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $factory = $container->findDefinition('app.factory.service_creator');

        $counter = 0;
        $services = [];

        foreach ($container->findTaggedServiceIds('app.custom_service') as $id => $params) {
            foreach ($params as $param) {
                $param['class'] = $counter;
                $services[$param['class']] = $id;
                $repository = $container->findDefinition($id);
                $repository->setArgument(0, new Reference('doctrine.orm.default_entity_manager'));

                $definition = new Definition();
                $definition->setClass('Doctrine\ORM\Mapping\ClassMetadata');
                $definition->setFactory([new Reference('doctrine.orm.default_entity_manager'), 'getClassMetadata']);

                $definition->addArgument($param['class']);
                $repository->setArgument(1, $definition);
            }
            $counter++;
        }
        $factory->setArgument(0, $services);
//        $container->findDefinition('doctrine.orm.configuration')
//            ->addMethodCall('setFactory', [$factory]);
    }
}