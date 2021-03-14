<?php

namespace D15r\Butler\Laravel;

use D15r\Butler\Support\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends Command
{
    const SUBLIME_TEXT_PROJECTS_PATH = '/Users/daniel/Library/Application Support/Sublime Text 3/Packages/User/Projects/';

    protected $name;

    protected $packages = [
        'laravel/telescope',
        'laravel/tinker',
        'spatie/laravel-cookie-consent',
        'laravel/sanctum',
        'danielsundermeier/laravel-deploy',
        // 'danielsundermeier/laravel-contactform',
        // 'danielsundermeier/laravel-impressum',
        // 'danielsundermeier/laravel-isdeleteable',
        'danielsundermeier/laravel-model-path',
    ];
    protected $dev_packages = [
        'laravel/breeze',
        'barryvdh/laravel-debugbar',
        'danielsundermeier/laravel-make',
    ];

    protected function configure()
    {
        $this
            ->setName('laravel:new')
            ->setDescription('Creates an new laravel application.')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the app');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->name = $input->getArgument('name');

        // Update laravel/installer
        exec('composer global update laravel/installer');

        // laravel new
        exec('laravel new ' . $this->name);

        // change directory
        chdir($this->name);

        // valet link
        exec('valet link');

        // require default packages
        exec('COMPOSER_MEMORY_LIMIT=2G composer require ' . implode(' ', $this->packages));
        exec('COMPOSER_MEMORY_LIMIT=2G composer require --dev ' . implode(' ', $this->dev_packages));

        // VerifyCsrfTokenMiddleware add except deploy
        File::replaceContent('app/Http/Middleware/VerifyCsrfToken.php', ['//'], ["'deploy',"]);

        // pubish stubs
        exec('php artisan stub:publish');

        // link storage
        exec('php artisan storage:link');

        // install livewire
        exec('php artisan breeze:install');

        // replace stubs with my own
        foreach (glob(__DIR__ . '/../../stubs/laravel/stubs/*.stub') as $filename) {
            copy($filename, './stubs/' . basename($filename));
        }

        // .htaccess add RewriteBase /
        File::replaceContent('public/.htaccess', [
            'RewriteEngine On'
        ], [
            'RewriteEngine On' . "\n" . '    RewriteBase /'
        ]);

        // create database
        $this->createDatabase($this->name);

        // git init
        exec('git init');

        // npm install
        exec('npm install');

        // npm run dev
        exec('npm run dev');

        // migrate database
        exec('php artisan migrate');

        // create sublime text project
        File::makeFromStub(self::SUBLIME_TEXT_PROJECTS_PATH . $this->name . '.sublime-project', __DIR__ . '/../../stubs/laravel/stub.sublime-project', [
            '{{ name }}',
            '{{ path }}'
        ], [
            $this->name,
            getcwd()
        ]);

        // Sublime Text öffnen
        exec('sublime .');

        $output->writeln('Fertig');

        return Command::SUCCESS;
    }

    protected function createDatabase(string $name) : void
    {
        $database_name = str_replace('-', '_', $name);
        exec('mysql -uroot -e"CREATE DATABASE IF NOT EXISTS ' . $database_name . '"');
    }
}