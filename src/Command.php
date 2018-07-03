<?php

namespace philiplarsson\Noty;

use philiplarsson\Noty\Exceptions\FileNotFoundException;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends SymfonyCommand
{
    /**
     * Return the file root.
     * For instance, /tmp/foo.bar returns foo
     */
    public function getFileRoot(string $filename):string
    {
        $fileInfo = pathInfo($filename);
        return $fileInfo['filename'];
    }

    public function assertFileExists($filename, OutputInterface $output)
    {
        if (!file_exists($filename)) {
            $output->writeln("<error>${filename} does not exist!</error>");
            exit(1);
        }
    }

    public function endsWith($needle, $haystack):bool
    {
        $length = strlen($needle);

        return substr($haystack, -$length) === $needle;
    }

    public function validateOptions(InputInterface $input, OutputInterface $output, $optionMap)
    {
        foreach ($optionMap as $option => $values) {
            if (!in_array($input->getOption($option), $values)) {
                $output->writeln("<error>Unsupported output format</error>");
                exit(1);
            }
        }
    }

    public function runCommand(string $command)
    {
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();
    }

    public function getPandocCommand(string $inputFilename, string $outputFilename): string
    {
        $cmd = "pandoc ${inputFilename} -o ${outputFilename}";
        if ($this->endsWith('html', $outputFilename)) {
            //TODO: check if css file exists
            $cmd .= " -s --css " . __DIR__  . '/../css/pandoc.css';
        } else if ($this->endsWith('pdf', $outputFilename)) {
            // TODO: check if eisvogel tex exists
            $cmd .= " --template " . __DIR__ . "/../tex-templates/eisvogel.tex --listings";
        }

        return $cmd;
    }
}
