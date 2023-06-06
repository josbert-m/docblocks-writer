<?php

namespace JosbertM\DocblocksWriter\Mappers;

use JosbertM\DocblocksWriter\Contracts\Mapper;
use JosbertM\DocblocksWriter\Exceptions\ClassNotMappeable;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;

class ClassMapper extends Mapper
{
    /**
     * @var ReflectionClass
     */
    protected ReflectionClass $reflection;

    /**
     * @param ReflectionClass $reflection
     * @throws ClassNotMappeable
     */
    public function __construct(ReflectionClass $reflection)
    {
        $this->reflection = $reflection;
        $this->docBlockFactory = DocBlockFactory::createInstance();

        $this->boot();
    }

    /**
     * Boot this mapper.
     *
     * @return void
     * @throws ClassNotMappeable
     */
    protected function boot(): void
    {
        if ($this->reflection->getFileName() === false) {
            throw new ClassNotMappeable($this->reflection->getName());
        }

        $this->filename = $this->reflection
            ->getFileName();

        $rawContent = file_get_contents($this->filename);
        $this->lines = preg_split("/\n/m", $rawContent);

        $rawCommentBlock = $this->reflection
            ->getDocComment();

        $this->rawCommentBlock = is_string($rawCommentBlock) ?
            $rawCommentBlock :
            null;
    }

    /**
     * Get current class tags.
     *
     * @return array
     */
    public function getTags(): array
    {
        if (count($this->currentTags) < 1 && isset($this->rawCommentBlock)) {
            $docBlock = $this->docBlockFactory
                ->create($this->rawCommentBlock);

            $this->currentTags = array_map(
                fn(Tag $tag) => $this->resolveTag($tag),
                $docBlock->getTags()
            );
        }

        return $this->currentTags;
    }

    /**
     * Get the current class description.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        if (is_null($this->description) && isset($this->rawCommentBlock)) {
            $docBlock = $this->docBlockFactory
                ->create($this->rawCommentBlock);

            $description = (string) $docBlock->getDescription();
            $this->description = strlen($description) > 0 ?
                preg_replace("/ ?\n/m", ' ', $description) :
                null;
        }

        return $this->description;
    }

    /**
     * Get the current class summary.
     *
     * @return string|null
     */
    public function getSummary(): ?string
    {
        if (is_null($this->description) && isset($this->rawCommentBlock)) {
            $docBlock = $this->docBlockFactory
                ->create($this->rawCommentBlock);

            $summary = $docBlock->getSummary();

            $this->summary = strlen($summary) > 0 ?
                preg_replace("/ ?\n/m", ' ', $summary) :
                null;
        }

        return $this->summary;
    }

    /**
     * Get declaring indentation.
     *
     * @return int
     */
    public function getIndentation(): int
    {
        if (!isset($this->indentation)) {
            $startLine = $this->reflection
                ->getStartLine();

            $line = $this->lines[$startLine - 1];

            preg_match("/^\s+(?=\S)/", $line, $match);
            $substr = count($match) > 0 ?
                preg_replace("/\t/", '    ', $match[0]) :
                '';

            $this->indentation = strlen($substr);
        }

        return $this->indentation;
    }

    /**
     * Get file lines as an array, without DocComments lines.
     *
     * Array keys are preserved.
     *
     * @return Array<int, string>
     */
    public function getLinesWithoutDocComment(): array
    {
        if (!$this->hasDocComment()) {
            return $this->lines;
        }

        $commentLines = preg_split("/\n/", $this->rawCommentBlock);

        return array_filter(
            $this->lines,
            function ($value, $key) use ($commentLines) {
                foreach ($commentLines as $commentLine) {
                    $regex = '/^\s*' . preg_quote($commentLine, '/') . '\s*$/';

                    if (preg_match($regex, $value) === 1 && $key < ($this->reflection->getStartLine() - 1)) {
                        return false;
                    }
                }

                return true;
            },
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * Get real class start line.
     *
     * @return int
     */
    public function getStartLine(): int
    {
        $header = array_slice($this->lines, 0, ($this->reflection->getStartLine() + 1));
        $hasAttributes = [];

        foreach ($header as $index => $item) {
            if (preg_match("/#\[.+]/", $item) === 1) {
                $hasAttributes[] = $index;
            }
        }

        return count($hasAttributes) > 0 ?
            min($hasAttributes) + 1 :
            $this->reflection->getStartLine();
    }
}
