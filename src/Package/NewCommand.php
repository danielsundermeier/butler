<?php

namespace D15r\Butler\Package;

use D15r\Butler\Support\File;
use D15r\Butler\Support\Composer;
use D15r\Butler\Support\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class NewCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('package:new')
            ->setDescription('Creates a new package.')
            ->addArgument('package_name', InputArgument::REQUIRED, 'package name vendor/name')
            ->addArgument('namespace', InputArgument::OPTIONAL, 'package namespace');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $package_name = $input->getArgument('package_name');

        $output->writeln('creating package ' . $package_name . '.');

        $parts = explode('/', $package_name);
        $vendor = $parts[0];
        $name = $parts[1];

        if (is_null($input->getArgument('namespace'))) {
            $vendor_namespace = Str::studly($vendor);
            $name_namespace = Str::studly($name);
            $namespace = $vendor_namespace . '\\' . $name_namespace;
        }
        else {
            $namespace = $input->getArgument('namespace');
            $vendor_namespace = trim(implode('\\', array_slice(explode('\\', $namespace), 0, -1)), '\\');
            $name_namespace = str_replace($vendor_namespace . '\\', '', $namespace);
        }
        $package_path = 'packages/' . $vendor . '/' . $name;

        $output->writeln('creating package path.');
        File::makeDirectory($package_path);

        $output->writeln('creating composer.json.');
        Composer::makeJsonFile($package_path, [
            $vendor_namespace,
            $package_name
        ]);

        $output->writeln('installing package.');
        Composer::install($package_name, $package_path);

        $output->writeln('Fertig');

        return Command::SUCCESS;
    }
}