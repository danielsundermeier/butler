<?php

namespace D15r\Butler\Wiki\Read;

use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ArchiveCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('wiki:read:archive')
            ->setDescription('Copies the contents of reading.md to its archive read/YEAR/WEEK.md')
            ->addArgument('path', InputArgument::OPTIONAL, 'path to the wiki', '/Users/daniel/code/danielsundermeier/knowledge');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $last_sunday = $this->getLastSunday();

        // Pfade festlegen
        $path = $input->getArgument('path');
        $file_path = $path . '/read/reading.md';
        $archive_directory_name = $path . '/read/' . $last_sunday->format('Y');
        $archive_file_path = $archive_directory_name . '/' . $last_sunday->format('W') . '.md';

        // Ordner erstellen, wenn nicht vorhanden
        $this->ensureDirectoryExists($archive_directory_name);

        // read/reading.md nicht vorhanden
        if (! file_exists($file_path)) {
            $output->writeln('<error>File not found: ' . $file_path . '</error>');
            return self::FAILURE;
        }

        // Aktuelle read/reading.md in Archiv kopieren
        $reading_content = trim(file_get_contents($file_path));

        // Keine Inhalte in read/reading.md
        if ($reading_content == '# Reading') {
            $output->writeln('<error>File is empty: ' . $file_path . '</error>');
            return self::FAILURE;
        }

        // Überschrift für Archiv Datei ersetzen, Beispiel: "Gelesen 2022 KW 22"
        $archive_content = str_replace('# Reading', '# Gelesen ' . $last_sunday->format('Y') . ' KW ' . $last_sunday->format('W'), $reading_content);
        // Archiv-Datei schreiben
        file_put_contents($archive_file_path, $archive_content);

        // Neue read/reading.md erstellen
        file_put_contents($file_path, '# Reading' . PHP_EOL . PHP_EOL);

        return self::SUCCESS;
    }

    /**
     * Creates the DateTime object for the last Sunday
     *
     * @return DateTime
     */
    private function getLastSunday(): DateTime
    {
        return DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d 00:00:00', strtotime('last sunday')));
    }

    /**
     * Ensures that the given directory exists.
     *
     * @param string $directory
     * @return bool
     */
    private function ensureDirectoryExists($directory): bool
    {
        if (is_dir($directory)) {
            return true;
        }

        return mkdir(dirname($directory), 0777, true);
    }
}