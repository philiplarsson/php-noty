<?php

namespace philiplarsson\Noty\Commands;

use philiplarsson\Noty\Command;
use philiplarsson\Noty\Exceptions\FileNotFoundException;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WatchCommand extends Command
{

    private $oldinputfileMTime;
    private $oldinputfilemd5hash;

    public function configure()
    {
        $this->setName('watch')
             ->setDescription('Recompiles if file is saved. ')
             ->addArgument('name', InputArgument::REQUIRED, "Name of the note")
             ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'Output format, pdf or html', 'pdf');
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

        $pandocCommand = $this->getPandocCommand($inputFilename, $outputFilename);


        while (true) {
            if ($this->fileHasChanged($inputFilename)) {
                $this->runCommand($pandocCommand);
                $output->writeln("<comment>" . date("H:i:s") . " Note compiled!</comment>");
            }
            sleep(1);
        }
    }

    private function fileHasChanged(string $filename):bool
    {
        clearstatcache();
        $this->oldinputfileMTime = $this->oldinputfileMTime ?? filemtime($filename);
        $this->oldinputfilemd5hash = $this->oldinputfilemd5hash ?? md5_file($filename);

        if (filemtime($filename) !== $this->oldinputfileMTime) {
            if (md5_file($filename) !== $this->oldinputfilemd5hash) {
                $this->oldinputfileMTime = filemtime($filename);
                $this->oldinputfilemd5hash = md5_file($filename);
                return true;
            }
        }

        return false;
    }

}
