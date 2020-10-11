<?php

namespace D15r\Butler\Make;

use D15r\Butler\Support\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class CommandCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('make:command')
            ->setDescription('Makes a new command')
            ->addArgument('name', InputArgument::REQUIRED, 'name of the command "make:command"');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $filename = $this->getFilename($name);
        $namespace = $this->getNamespace($name);

        $full_path = __DIR__ . '/../' . $this->getPath($namespace);
        $file_path = $full_path . '/' . $filename . '.php';

        if (file_exists($file_path)) {
            $output->writeln('Command ' . $file_path . ' exists.');
            return Command::FAILURE;
        }

        $created = $this->make($file_path, $this->buildFile([
            $namespace,
            $filename,
            $name,
        ]));

        if ($created === false) {
            $output->writeln('Command ' . $file_path . ' could not be created.');
            return Command::FAILURE;
        }

        $this->discoverCommand('D15r\\Butler\\' . $namespace . '\\' . $filename);

        $output->writeln('Command "' . $name . '" erstellt');

        return Command::SUCCESS;
    }

    protected function getNamespace(string $name) : string
    {
        return trim(implode('\\', array_map('ucfirst', array_slice(explode(':', $name), 0, -1))), '\\');
    }

    protected function getPath(string $namespace) : string
    {
        return str_replace('\\', '/', $namespace);
    }

    protected function getFilename(string $name) : string
    {
        return ucfirst(substr($name, strrpos($name, ':') + 1)) . 'Command';
    }

    protected function buildFile(array $replace) : string
    {
        $stub = file_get_contents(__DIR__ . '/../../stubs/command.stub');
        return str_replace([
            '{{ namespace }}',
            '{{ filename }}',
            '{{ name }}',
        ], $replace, $stub);
    }

    protected function make(string $file_path, string $stub)
    {
        File::makeDirectory(dirname($file_path));
        return file_put_contents($file_path, $stub);
    }

    protected function discoverCommand(string $full_namespace) : void
    {
        $json = file_get_contents(__DIR__ . '/../../commands.json');
        $commands = json_decode($json);
        $commands[] = $full_namespace;
        sort($commands);
        file_put_contents(__DIR__ . '/../../commands.json', json_encode($commands, JSON_PRETTY_PRINT));

    }
}