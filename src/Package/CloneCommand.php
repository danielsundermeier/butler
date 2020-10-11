<?php

namespace D15r\Butler\Package;

use D15r\Butler\Support\File;
use D15r\Butler\Support\Git;
use D15r\Butler\Support\Composer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CloneCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('package:clone')
            ->setDescription('Description')
            ->addArgument('package_name', InputArgument::REQUIRED, 'Name of the package')
            ->addArgument('url', InputArgument::REQUIRED, 'Url of the repository');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $package_name = $input->getArgument('package_name');
        $package_path = 'packages/' . $package_name;

        File::makeDirectory($package_path);
        Git::clone($package_path, $input->getArgument('url'));
        Composer::install($package_name, $package_path);

        $output->writeln('Fertig');

        return Command::SUCCESS;
    }
}