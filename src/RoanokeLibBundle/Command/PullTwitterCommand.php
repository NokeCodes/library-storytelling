<?php

namespace RoanokeLibBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Description of PullTwitterCommand
 *
 * @author cwilkes
 */
class PullTwitterCommand extends ContainerAwareCommand
{
    
    
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('roalib:pull:twitter')
            ->setDescription('Pull stories from twitter')
                ->setDefinition(array(
            new InputArgument(
                'hashtag', InputArgument::REQUIRED, 'Hashtag of stories t pull'
            )
        ))
            ->setHelp(<<<EOF
The <info>%command.name%</info> command pulls stories from twitter
EOF
            );
    }
    
    
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hashtag = $input->getArgument('hashtag');
        $pullTwitter = $this->getContainer()->get('roanokelib.pull_twitter');
        $pullTwitter->pullTwitterStories($hashtag);
    }
}
