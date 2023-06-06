<?php

namespace JosbertM\DocblocksWriter\Tests\Unit;

use Exception;
use Faker\Factory;
use JosbertM\DocblocksWriter\Writer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class WriterTest extends TestCase
{
    /**
     * Test for write properties to a class.
     *
     * @return void
     */
    public function test_for_write_properties(): void
    {
        $fileSystem = new Filesystem();
        $faker = Factory::create('en_US');
        $time = (string) time();
        $srcPath = realpath(__DIR__ . '/../ClassToWrite.php');
        $targetPath = realpath(__DIR__ . '/../../writable') . "/{$time}.php";

        try {
            $fileSystem->copy($srcPath, $targetPath);

            $contents = file_get_contents($targetPath);
            $contents = preg_replace("/(?<=class\s)ClassToWrite/m", "ClassToWrite{$time}", $contents);
            file_put_contents($targetPath, $contents);

            require $targetPath;

            $writer = new Writer("JosbertM\DocblocksWriter\Tests\ClassToWrite{$time}");

            $writer->setSummary($faker->words(5, true));
            $writer->setDescription($faker->words(25, true));

            $writer->addTag('property', "string|null \${$faker->unique()->word()}");
            $writer->addTag('property', "string|null \${$faker->unique()->word()}");
            $writer->addTag('method', "void {$faker->unique()->word()}(mixed \$any)");

            $result = $writer->write();

            $this->assertTrue($result);
        }
        catch (Exception $e) {
            $this->fail("{$e->getMessage()} [{$e->getFile()}:{$e->getLine()}]");
        }
    }
}
