<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use JbNahan\Bundle\WorkflowManagerBundle\Visitor\WorkflowVisitorVisualization;

class DefinitionDotGraphCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('jbnahan:definition:display')
            ->setDescription('Export dot representation for GraphViz')
            ->addArgument('id', InputArgument::REQUIRED, 'Id of workflow definition')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$id = $input->getArgument('id');
		$storage = $this->getContainer()->get('jb_nahan.definition_manager');
		$workflow = $storage->loadById($id);

		$visitor = new WorkflowVisitorVisualization();
		$workflow->accept($visitor);
		//print $visitor;
        $output->writeln($visitor->__toString());
    }
}