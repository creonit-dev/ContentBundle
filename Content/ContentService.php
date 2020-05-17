<?php

namespace Creonit\ContentBundle\Content;

use Creonit\ContentBundle\Content\ContentDuplication\ContentDuplicatorInterface;

class ContentService
{
    /**
     * @var ContentDuplicatorInterface|null
     */
    protected $contentDuplicator;

    /**
     * @return ContentDuplicatorInterface|null
     */
    public function getContentDuplicator(): ?ContentDuplicatorInterface
    {
        return $this->contentDuplicator;
    }

    /**
     * @param ContentDuplicatorInterface|null $contentDuplicator
     * @return ContentService
     */
    public function setContentDuplicator(?ContentDuplicatorInterface $contentDuplicator): ContentService
    {
        $this->contentDuplicator = $contentDuplicator;
        return $this;
    }
}