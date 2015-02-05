<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DefinitionListCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('jbnahan:definition:list')
            ->setDescription('List all workflow définition')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $repo = $this->getContainer()->get('doctrine')->getRepository('JbNahanWorkflowManagerBundle:Definition');
        $wfs = $repo->findAll();
        $table = $this->getHelperSet()->get('table');

        $table->setHeaders(array('ID', 'Name', 'Version','Parent','Created at','Published at','Archived at'));
        $rows = array();
        foreach ($wfs as $wf) {
            $row = array();
            $row[]=$wf->getId();
            $row[]=$wf->getName();
            $row[]=$wf->getVersion();
            $row[]=$wf->getParent();
            $row[]=($wf->getCreatedAt()===null)? '-':$wf->getCreatedAt()->format('Y-m-d H:i:s');
            $row[]=($wf->getPublishedAt()===null)? '-':$wf->getPublishedAt()->format('Y-m-d H:i:s');
            $row[]=($wf->getArchivedAt()===null)? '-':$wf->getArchivedAt()->format('Y-m-d H:i:s');
            $rows[]=$row;
        }
        $table->setRows($rows);
        $table->render($output);
        //$output->writeln($text);
    }
}
