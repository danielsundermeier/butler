<?php

namespace D15r\Butler\Package;

use D15r\Butler\Support\Git;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PushCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('package:push')
            ->setDescription('Description')
            ->addArgument('package_name', InputArgument::REQUIRED, 'Name of the package')
            ->addArgument('message', InputArgument::OPTIONAL, 'Message for the commit', 'update')
            ->addOption('release', null, InputOption::VALUE_REQUIRED, 'Create a release?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $package_name = $input->getArgument('package_name');
        $package_path = 'packages/' . $package_name;
        $release = $input->getOption('release');

        Git::push($package_path, $input->getArgument('message'));

        if ($release) {
            Git::release($package_path, $release);
        }

        $output->writeln('Fertig');

        return Command::SUCCESS;
    }
}