<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExecutionListCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('jbnahan:execution:list')
            ->setDescription('List all workflow execution')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $repo = $this->getContainer()->get('doctrine')->getRepository('JbNahanWorkflowManagerBundle:Execution');
        $wfs = $repo->findAll();
        $table = $this->getHelperSet()->get('table');

        $table->setHeaders(array('ID', 'Name', 'Workflow','Parent','Started at','End at','Canceled at','Suspended at','Step'));
        $rows = array();
        foreach ($wfs as $wf) {
            $row = array();
            $row[]=$wf->getId();
            $row[]=$wf->getName();
            $row[]=$wf->getWorkflow();
            $row[]=$wf->getParent();
            $row[]=($wf->getStartedAt()===null)? '-':$wf->getStartedAt()->format('Y-m-d H:i:s');
            $row[]=($wf->getEndAt()===null)? '-':$wf->getEndAt()->format('Y-m-d H:i:s');
            $row[]=($wf->getCanceledAt()===null)? '-':$wf->getCanceledAt()->format('Y-m-d H:i:s');
            $row[]=($wf->getSuspendedAt()===null)? '-':$wf->getSuspendedAt()->format('Y-m-d H:i:s');
            $row[]=$wf->getSuspendedStep();
            $rows[]=$row;
        }
        $table->setRows($rows);
        $table->render($output);
        //$output->writeln($text);
    }
}