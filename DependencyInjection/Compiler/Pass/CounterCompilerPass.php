<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\DependencyInjection\Compiler\Pass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CounterCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('jb_nahan_workflow_manager.workflow_execution_database_factory')) {
            return;
        }

        $definition = $container->getDefinition(
            'jb_nahan_workflow_manager.workflow_execution_database_factory'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'jbnahan.countermanager'
        );

        if (1 < count($taggedServices)) {
            throw new \Exception("Unable to set many counter manager");
        }

        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'setCounterManager',
                array(new Reference($id))
            );
        }
    }
}
