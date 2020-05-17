<?php


namespace Creonit\ContentBundle\Content\ContentDuplication;


use Creonit\ContentBundle\Model\Content;

interface ContentDuplicatorInterface
{
    public function copyContentBlocks(Content $sourceContent, Content $targetContent);
    public function cloneContentBlockById(Content $content, $blockId);
}