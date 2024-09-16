<?php

namespace App;

use App\Repository\Factory\CompilerPass;
use App\Repository\Factory\IRepository;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->registerForAutoconfiguration(IRepository::class)
            ->addTag('app.repository_service');

        $container->addCompilerPass(new CompilerPass());
    }
}
