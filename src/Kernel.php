<?php

namespace App;

use App\Repository\CategoryRepository;
use App\Service\Factory\CompilerPass;
use App\Repository\FolderRepository;
use App\Repository\NoteRepository;
use App\Repository\PromptRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->registerForAutoconfiguration(CategoryRepository::class)
            ->addTag('app.custom_category_service');
        $container->registerForAutoconfiguration(FolderRepository::class)
            ->addTag('app.custom_folder_service');
        $container->registerForAutoconfiguration(NoteRepository::class)
            ->addTag('app.custom_note_service');
        $container->registerForAutoconfiguration(PromptRepository::class)
            ->addTag('app.custom_prompt_service');
        $container->registerForAutoconfiguration(TagRepository::class)
            ->addTag('app.custom_tag_service');

        $container->addCompilerPass(new CompilerPass());
    }
}
