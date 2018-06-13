<?php

namespace philiplarsson\Notes\Commands;

use philiplarsson\Notes\Exceptions\FileNotFoundException;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompileCommand extends Command
{

    public function __construct()
    {

        parent::__construct();
    }

    public function configure()
    {
        $this->setName('compile')
             ->setDescription('Compile a note to pdf or html using pandoc')
             ->addArgument('name', InputArgument::REQUIRED, "Name of the note. ")
             ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'Output format, pdf or html', 'pdf')
             ->addOption('always-overwrite', 'a', InputOption::VALUE_OPTIONAL, 'Do not ask if output file should be overwritten', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->validateOptions($input, $output);

        $inputFilename = $input->getArgument('name');
        $fileInfo = pathInfo($inputFilename);
        $outputFilename = $fileInfo['filename'] . "." . $input->getOption('format');

        $this->checkIfOutputFileExists($outputFilename, $input, $output);

        $pandocCommand = $this->getPandocCommand($inputFilename, $outputFilename);

        $process = new Process($pandocCommand);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();
        $output->writeln('<comment>Note compiled!</comment>');
    }

    private function getPandocCommand(string $inputFilename, string $outputFilename): string
    {
        return "pandoc ${inputFilename} -o ${outputFilename}";
    }

    private function checkIfOutputFileExists(string $outputFile, InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $alwaysOverwriteValue = $input->getOption('always-overwrite');
        $alwaysOverwrite = ($alwaysOverwriteValue !== false);

        if (!$alwaysOverwrite && file_exists(getcwd() . '/' . $outputFile)) {
            $overwrite = $io->confirm("${outputFile} exists, overwrite?", true);
            if (!$overwrite) {
                $output->writeln("<info>Exiting...</info>");
                exit(1);
            }
        }
    }
    private function validateOptions(InputInterface $input, OutputInterface $output)
    {
        if (!in_array($input->getOption('format'), ['pdf', 'html'])) {
            $output->writeln("<error>Unsupported output format</error>");
            exit(1);
        }
    }
}
