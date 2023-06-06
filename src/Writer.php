<?php

namespace JosbertM\DocblocksWriter;

use JosbertM\DocblocksWriter\Contracts\Mapper;
use JosbertM\DocblocksWriter\Exceptions\ClassIsNotDeclared;
use JosbertM\DocblocksWriter\Exceptions\ClassNotMappeable;
use JosbertM\DocblocksWriter\Mappers\ClassMapper;
use ReflectionClass;

class Writer
{
    /**
     * @var Mapper|ClassMapper
     */
    protected Mapper $mapper;

    /**
     * @var Array<int, Array<string, string>>
     */
    protected array $tags = [];

    /**
     * @var string|null
     */
    protected ?string $summary = null;

    /**
     * @var string|null
     */
    protected ?string $description = null;

    /**
     * @param string|object $class
     * @throws ClassNotMappeable
     * @throws ClassIsNotDeclared
     */
    public function __construct($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (!class_exists($class)) {
            throw new ClassIsNotDeclared($class);
        }

        $reflection = new ReflectionClass($class);
        $this->mapper = new ClassMapper($reflection);
    }

    /**
     * Add new tag to write.
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function addTag(string $name, string $value): void
    {
        $this->tags[] = compact('name', 'value');
    }

    /**
     * Set the summary to write.
     *
     * Pass the summary param without line breaks.
     *
     * @param string $summary
     * @return void
     */
    public function setSummary(string $summary): void
    {
        $summary = trim($summary);

        if (strlen($summary) > 0) {
            $this->summary = $summary;
        }
    }

    /**
     * Set the description to write.
     *
     * Pass the description param without line breaks.
     *
     * @param string $description
     * @return void
     */
    public function setDescription(string $description): void
    {
        $description = trim($description);

        if (strlen($description) > 0) {
            $this->description = $description;
        }
    }

    /**
     * Write the DocComment to class.
     *
     * If the truncate params is true, Comments
     * will be cleaned before writing.
     *
     * @param bool $sort
     * @param bool $truncate
     * @return bool
     */
    public function write(bool $truncate = false, bool $sort = true): bool
    {
        $indent = str_repeat(' ', $this->mapper->getIndentation());

        $summary = !$truncate && !is_null($this->summary) ?
            $this->summary :
            (
                !$truncate && is_null($this->summary) ?
                    $this->mapper->getSummary() :
                    $this->summary
            );

        $description = !$truncate && !is_null($this->description) ?
            $this->description :
            (
            !$truncate && is_null($this->description) ?
                $this->mapper->getDescription() :
                $this->description
            );

        $currentTags = $truncate ?
            [] :
            $this->mapper->getTags();

        $tags = array_merge(
            $currentTags,
            array_map(fn(array $tag) => "{$tag['name']} {$tag['value']}", $this->tags)
        );

        if ($sort) {
            natsort($tags);
        }

        if (!is_null($summary)) {
            preg_match_all("/\S.{1,80}\s/m", "{$summary} ", $match_summary);

            if (!is_null($description)) {
                preg_match_all("/\S.{1,80}\s/m", "{$description} ", $match_description);
            }
        }

        $summaryAndDescription = !is_null($summary) && !is_null($description) ?
            [
                ...$match_summary[0] ?? null,
                '',
                ...$match_description[0] ?? null,
                ''
            ] :
            array_filter([
                ...$match_summary[0] ?? null,
                ''
            ], fn($item) => !is_null($item));

        $docComment = array_merge(
            ["{$indent}/**"],
            array_map(fn($item) => "{$indent} * {$item}", $summaryAndDescription),
            array_map(fn($tag) => "{$indent} * @{$tag}", $tags),
            ["{$indent} */"]
        );

        $templates = [
            'header' => implode(
                "\n",
                array_slice($this->mapper->getLinesWithoutDocComment(), 0, $this->mapper->getStartLine() - 1)
            ),
            'docComment' => implode("\n", $docComment),
            'body' => implode(
                "\n",
                array_slice($this->mapper->getLinesWithoutDocComment(), ($this->mapper->getStartLine() - 1))
            )
        ];

        return !!file_put_contents(
            $this->mapper->getFilename(),
            implode("\n", [
                $templates['header'],
                $templates['docComment'],
                $templates['body']
            ])
        );
    }
}
