<?php

namespace D15r\Butler\Package;

use D15r\Butler\Support\File;
use D15r\Butler\Support\Composer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('package:remove')
            ->setDescription('Description')
            ->addArgument('package_name', InputArgument::REQUIRED, 'Name of the package')
            ->addOption('require', null, InputOption::VALUE_NONE, 'Require package after it is removed?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $package_name = $input->getArgument('package_name');
        $package_path = 'packages/' . $package_name;
        $require = $input->getOption('require');

        Composer::uninstall($package_name);
        File::deleteDirectory($package_path);

        // Symlink löschen
        File::delete('vendor/' . $package_name);

        if ($require) {
            Composer::require($package_name);
        }

        $output->writeln('Fertig');

        return Command::SUCCESS;
    }
}