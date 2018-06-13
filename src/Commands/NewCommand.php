<?php

namespace philiplarsson\Notes\Commands;

use philiplarsson\Notes\Exceptions\FileNotFoundException;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends Command
{
    protected $stubDir;
    private $stub;

    public function __construct()
    {
        $this->stubDir = __DIR__ . '/../stubs/';

        parent::__construct();
    }

    public function configure()
    {
        $this->setName('new')
             ->setDescription('Create a new note')
             ->addArgument('name', InputArgument::REQUIRED, "Name of the note")
             ->addOption('title', null, InputOption::VALUE_OPTIONAL, 'Title of the note', 'New note');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileName = getcwd() . '/' . $input->getArgument('name') . '.md';

        $this->assertFileDoesNotExist($fileName, $output);

        try {
            $this->getStub('note')
                 ->replacePlaceholders($input)
                 ->writeToDisk($fileName);
        } catch (FileNotFoundException $e) {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
            exit(1);
        }

        $output->writeln('<comment>Note created!</comment>');
    }

    private function assertFileDoesNotExist($fileName, OutputInterface $output)
    {
        if (file_exists($fileName)) {
            $output->writeln("<error>File already exists!</error>");
            exit(1);
        }
    }

    private function getStub(string $stubName)
    {
        $stubFile = $this->stubDir . $stubName . ".stub";
        if (!file_exists($stubFile)) {
            throw new FileNotFoundException("Stub file: ${stubFile} could not be found.");
        }

        $stub = file_get_contents($stubFile);
        $this->stub = $stub;

        return $this;
    }

    private function replacePlaceholders(InputInterface $input)
    {
        $this->stub = str_replace('{{title}}', $input->getOption('title'), $this->stub);
        $this->stub = str_replace('{{date}}', date("Y-m-d"), $this->stub);

        return $this;
    }

    private function writeToDisk(string $fileName)
    {
        file_put_contents($fileName, $this->stub);
    }
}
