<?php

namespace philiplarsson\Noty;

use philiplarsson\Noty\Exceptions\FileNotFoundException;

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

    public function endsWith($needle, $haystack):bool
    {
        $length = strlen($needle);

        return substr($haystack, -$length) === $needle;
    }
}
