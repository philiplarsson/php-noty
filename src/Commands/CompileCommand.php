<?php

namespace philiplarsson\Noty\Commands;

use philiplarsson\Noty\Command;
use philiplarsson\Noty\Exceptions\FileNotFoundException;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompileCommand extends Command
{

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
        $inputFilename = $input->getArgument('name');

        $this->assertFileExists($inputFilename, $output);
        $this->validateOptions($input, $output, [
            'format' => ['pdf', 'html']
        ]);

        $outputFilename = sprintf("%s.%s",
                    $this->getFileRoot($inputFilename),
                    $input->getOption('format')
                );

        $this->checkIfOutputFileExists($outputFilename, $input, $output);

        $pandocCommand = $this->getPandocCommand($inputFilename, $outputFilename);

        $this->runCommand($pandocCommand);
        $output->writeln('<comment>Note compiled!</comment>');
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
}
