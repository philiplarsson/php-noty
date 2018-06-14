<?php

namespace philiplarsson\Noty\Tests;

use philiplarsson\Noty\Command;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase
{

    protected $command;

    protected function setUp()
    {
        $this->command = new Command();
    }

    /**
     * @dataProvider getFileRootProvider
     */
    public function testGetFileRoot($filename, $expected)
    {
        $actualOutput = $this->command->getFileRoot($filename);
        $this->assertEquals($expected, $actualOutput);
    }

    public function getFileRootProvider()
    {
        return [
            ['/tmp/foo.bar', 'foo'],
            ['/tmp/foo/bar.vis.php', 'bar.vis'],
            ['/a/b/c/d/ef.ab/g/myphoto.png', 'myphoto']
        ];
    }

    /**
     * @dataProvider endsWithProvider
     */
    public function testEndsWith($needle, $haystack, $expected)
    {
        $actualOutput = $this->command->endsWith($needle, $haystack);
        $this->assertEquals($expected, $actualOutput);
    }

    public function endsWithProvider()
    {
        return [
            ['html', 'foo/bar.html', true],
            ['html', 'foo/bar.css', false],
            ['pdf', 'foo/bar/baz/my.fancy.file.pdf', true]
        ];
    }
}
