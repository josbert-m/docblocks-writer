<?php

namespace JosbertM\DocblocksWriter\Tests\Unit;

use Exception;
use JosbertM\DocblocksWriter\Mappers\ClassMapper;
use JosbertM\DocblocksWriter\Tests\ClassToRead;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ClassMapperTest extends TestCase
{
    /**
     * Test for a get a class summary.
     *
     * @return void
     */
    public function test_get_class_summary(): void
    {
        try {
            $reflection = new ReflectionClass(ClassToRead::class);
            $mapper = new ClassMapper($reflection);

            $this->assertIsString($mapper->getSummary());
            $this->assertEquals("This is a summary for this class.", $mapper->getSummary());
        }
        catch (Exception $e) {
            $this->fail("{$e->getMessage()} [{$e->getFile()}:{$e->getLine()}]");
        }
    }

    /**
     * Test for a get a class description.
     *
     * @return void
     */
    public function test_get_class_description(): void
    {
        try {
            $reflection = new ReflectionClass(ClassToRead::class);
            $mapper = new ClassMapper($reflection);

            $this->assertIsString($mapper->getDescription());
            $this->assertEquals("This is an example text for description in this class, ClassToRead.", $mapper->getDescription());
        }
        catch (Exception $e) {
            $this->fail("{$e->getMessage()} [{$e->getFile()}:{$e->getLine()}]");
        }
    }

    /**
     * Test for calculate declaring indentation.
     *
     * @return void
     */
    public function test_get_declaring_indentation(): void
    {
        try {
            $reflection = new ReflectionClass(ClassToRead::class);
            $mapper = new ClassMapper($reflection);

            $this->assertIsInt($mapper->getIndentation());
            $this->assertEquals(0, $mapper->getIndentation());
        }
        catch (Exception $e) {
            $this->fail("{$e->getMessage()} [{$e->getFile()}:{$e->getLine()}]");
        }
    }

    /**
     * Check file content as an array.
     *
     * @return void
     */
    public function test_check_file_lines_as_array(): void
    {
        try {
            $reflection = new ReflectionClass(ClassToRead::class);
            $startLine = $reflection->getStartLine();
            $mapper = new ClassMapper($reflection);

            $line = $mapper->getLines()[$startLine - 1];

            $this->assertIsArray($mapper->getLines());
            $this->assertCount(18, $mapper->getLines());
            $this->assertEquals('class ClassToRead', $line);
        }
        catch (Exception $e) {
            $this->fail("{$e->getMessage()} [{$e->getFile()}:{$e->getLine()}]");
        }
    }
}
