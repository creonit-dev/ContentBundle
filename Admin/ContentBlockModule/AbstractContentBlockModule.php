<?php


namespace Creonit\ContentBundle\Admin\ContentBlockModule;


use Creonit\AdminBundle\Module;

abstract class AbstractContentBlockModule extends Module
{
    private $blockMinSize = 1;
    private $blockMaxSize = 3;
    private $blockForbiddenSizes = [];
    protected $blockTypes = [];

    protected function addBlockType($className)
    {
        $component = substr($className, strrpos($className, '\\') + 1);
        $name = lcfirst(preg_replace('/BlockEditor$/', '', $component));
        $this->blockTypes[$className] = [
            'component' => $component,
            'name' => $name,
            'title' => $className::TITLE,
            'icon' => $className::ICON,
            'section' => $className::SECTION,
            'min_size' => $className::MIN_SIZE,
            'max_size' => $className::MAX_SIZE,
            'permitted_parents' => $className::PERMITTED_PARENTS,
            'forbidden_parents' => $className::FORBIDDEN_PARENTS,
            'permitted_children' => $className::PERMITTED_CHILDREN,
            'forbidden_children' => $className::FORBIDDEN_CHILDREN,
        ];
    }

    /**
     * @return array
     */
    public function getBlockTypes()
    {
        return $this->blockTypes;
    }

    /**
     * @return int
     */
    public function getBlockMinSize()
    {
        return $this->blockMinSize;
    }

    /**
     * @param int $blockMinSize
     * @return AbstractContentBlockModule|$this
     */
    public function setBlockMinSize($blockMinSize)
    {
        if ($blockMinSize < 1) {
            $blockMinSize = 1;
        }

        $this->blockMinSize = $blockMinSize;
        return $this;
    }

    /**
     * @return int
     */
    public function getBlockMaxSize()
    {
        return $this->blockMaxSize;
    }

    /**
     * @param int $blockMaxSize
     * @return AbstractContentBlockModule|$this
     */
    public function setBlockMaxSize($blockMaxSize)
    {
        $this->blockMaxSize = $blockMaxSize;
        return $this;
    }

    /**
     * @return array
     */
    public function getBlockForbiddenSizes()
    {
        return $this->blockForbiddenSizes;
    }

    /**
     * @param array $blockForbiddenSizes
     * @return AbstractContentBlockModule|$this
     */
    public function setBlockForbiddenSizes($blockForbiddenSizes)
    {
        $this->blockForbiddenSizes = $blockForbiddenSizes;
        return $this;
    }

    public function initialize()
    {
        $this->addComponent(new ContentBlockTable());
        $this->addComponent(new ChooseContentBlockTable());

        foreach (array_keys($this->blockTypes) as $blockClass) {
            $this->addComponent(new $blockClass);
        }
    }
}