<?php

namespace JosbertM\DocblocksWriter\Contracts;

use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlockFactory;

abstract class Mapper
{
    /**
     * @var DocBlockFactory
     */
    protected DocBlockFactory $docBlockFactory;

    /**
     * @var string
     */
    protected string $filename;

    /**
     * @var Array<int, string>
     */
    protected array $lines;

    /**
     * @var string|null
     */
    protected ?string $rawCommentBlock;

    /**
     * @var Array<int, string>
     */
    protected array $currentTags = [];

    /**
     * @var string|null
     */
    protected ?string $description = null;

    /**
     * @var string|null
     */
    protected ?string $summary = null;

    /**
     * @var int
     */
    protected int $indentation;

    /**
     * Boot this mapper.
     *
     * @return void
     */
    abstract protected function boot(): void;

    /**
     * Get current class tags.
     *
     * @return array
     */
    abstract public function getTags(): array;

    /**
     * Get declaring indentation.
     *
     * @return int
     */
    abstract public function getIndentation(): int;

    /**
     * Get file lines as an array.
     *
     * @return Array<int, string|null>
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    /**
     * Determine if exists DocComment.
     *
     * @return bool
     */
    public function hasDocComment(): bool
    {
        return !is_null($this->rawCommentBlock);
    }

    /**
     * Get a Tag instance as string.
     *
     * @param Tag $tag
     * @return string
     */
    protected function resolveTag(Tag $tag): string
    {
        $value = strval($tag);

        return "{$tag->getName()} $value";
    }
}
