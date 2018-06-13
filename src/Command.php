<?php

namespace philiplarsson\Notes;

use philiplarsson\Notes\Exceptions\FileNotFoundException;

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
    public function getFileRoot(string $fileName):string
    {
        $fileInfo = pathInfo($fileName);
        return $fileInfo['filename'];
    }


}
