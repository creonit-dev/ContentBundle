<?php


namespace Creonit\ContentBundle\DependencyInjection\Compiler;


use Creonit\ContentBundle\Content\ContentDuplication\ContentDuplicatorInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ContentDuplicatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasAlias(ContentDuplicatorInterface::class)) {
            $contentDuplicator = $container->findDefinition(ContentDuplicatorInterface::class);
            $contentService = $container->findDefinition('creonit_content');
            $contentService->addMethodCall('setContentDuplicator', [$contentDuplicator]);
        }
    }
}