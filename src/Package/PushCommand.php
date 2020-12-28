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
            ->addOption('release', null, InputOption::VALUE_REQUIRED, 'Create a release?')
            ->addOption('major', null, InputOption::VALUE_NONE, 'Create a major release?')
            ->addOption('minor', null, InputOption::VALUE_NONE, 'Create a minor release?')
            ->addOption('patch', null, InputOption::VALUE_NONE, 'Create a patchrelease?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $package_name = $input->getArgument('package_name');
        $package_path = 'packages/' . $package_name;
        $release = $input->getOption('release') ?: $this->getNextRelease($input, $package_path);

        Git::push($package_path, $input->getArgument('message'));

        if ($release) {
            Git::release($package_path, $release);
        }

        $output->writeln('Fertig');

        return Command::SUCCESS;
    }

    protected function getNextRelease(InputInterface $input, string $package_path)
    {
        $patch = $input->getOption('patch');
        $minor = $input->getOption('minor');
        $major = $input->getOption('major');

        if (! $patch && ! $minor && !$major) {
            return '';
        }

        $last_release = Git::lastRelease($package_path);
        var_dump($last_release);
        $next_release = $last_release;
        foreach ($next_release as $key => $part) {
            var_dump($key, $$key, (int) $$key);
            $next_release[$key] += (int) $$key;
        }

        return implode('.', $next_release);
    }
}