<?php

namespace JbNahan\Bundle\WorkflowManagerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use JbNahan\Bundle\WorkflowManagerBundle\DependencyInjection\Compiler\Pass\CounterCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class JbNahanWorkflowManagerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CounterCompilerPass());
    }
}
