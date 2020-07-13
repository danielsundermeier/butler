<?php

namespace D15r\Butler\Wiki;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class SummaryCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('wiki:summary')
            ->setDescription('Creates or updates the SUMMARY.md files')
            ->addArgument('path', InputArgument::REQUIRED, 'path to the wiki');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');
        $files = $this->files($path);
        $summary = new \SplFileObject($path . '/SUMMARY.md', 'w');
        $lastDir = [];

        foreach ($files as $key => $file) {
            $url = str_replace($path, '', $file->getPathname());

            if (in_array($url, ['/SUMMARY.md', '/README.md'])) {
                continue;
            }

            $parts = explode('/', $url);
            $level = (count($parts) - 2);
            $dir = $parts[$level];
            $filename = ucfirst(str_replace('.md', '', $file->getFilename()));

            $lastDir[$level] = $lastDir[$level] ?? false;

            if ($dir != $lastDir[$level]) {
                $summary->fwrite(str_repeat(' ', ($level - 1) * 4) . '- [' . ucfirst($dir) . '](' . $dir . '.md)' . "\n");
                $lastDir[$level] = $dir;
            }

            if ($filename == $dir) {
                continue;
            }

            $bytes = $summary->fwrite(str_repeat(' ', $level * 4) . '- [' . $filename . '](' . $url . ')' . "\n");
        }

        return 0;
    }

    protected function files($path) : Finder
    {
        $finder = new Finder();
        $finder->files()
            ->name('*.md')
            ->in($path)
            ->sortByName();

        return $finder;
    }
}