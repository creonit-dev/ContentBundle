<?php


namespace Creonit\ContentBundle\Admin\ContentBlockModule;


use Creonit\AdminBundle\Component\EditorComponent;
use Creonit\AdminBundle\Component\Field\Field;
use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\AdminBundle\Component\Response\ComponentResponse;

/**
 * @property AbstractContentBlockModule $module
 */
abstract class AbstractContentBlockEditor extends EditorComponent
{
    /**
     * Название блока
     */
    const TITLE = '';

    /**
     * Иконка
     */
    const ICON = '';

    /**
     * Расположение блока в меню
     */
    const SECTION = '';

    /**
     * Минимальный допустимый размер
     */
    const MIN_SIZE = 0;

    /**
     * Максимальный допустимый размер
     */
    const MAX_SIZE = 0;

    /**
     * Если массив, то можно прикреплять только к перечисленным блокам @todo
     * Если TRUE – можно прикреплять к любым блокам
     * Если FALSE – нельзя прикреплять
     *
     * @var array|bool
     */
    const PERMITTED_PARENTS = true;

    /**
     * Нельзя прикреплять блок к перечисленным блокам
     * @var array
     */
    const FORBIDDEN_PARENTS = [];

    /**
     * Если массив, то к этому блоку можно прикреплять только перечисленные блоки @todo
     * Если TRUE – можно прикреплять любые дочерние блоки
     * Если FALSE – нельзя прикреплять дочерние блоки
     * @var array|null
     */
    const PERMITTED_CHILDREN = false;

    /**
     * К этому блока нельзя прикреплять перечисленные блоки
     * @var array
     */
    const FORBIDDEN_CHILDREN = [];

    /**
     * @var Field
     */
    protected $sizeField;

    /**
     * @var string
     */
    protected $sizeFieldName = 'size';

    protected $parentBlock;

    public function schema()
    {
    }

    protected function prepareSchema()
    {
        $this->setTitle(static::TITLE);
        $this->setEntity('ContentBlock');

        $minSize = $this->module->getBlockMinSize();
        $maxSize = $this->module->getBlockMaxSize();

        if ($maxSize > $minSize) {
            $this->sizeField = $this->createField($this->sizeFieldName);
            $this->addField($this->sizeField);
            $this->setTemplate($this->getBlockSizeTemplate($this->sizeField) . $this->getTemplate());
        }

        parent::prepareSchema();
    }

    protected function getBlockSizeTemplate(Field $field)
    {
        $maxSize = $this->module->getBlockMaxSize();
        $fieldName = $field->getName();

        return sprintf('
            <div class="form-group">
                <label>Размер блока</label>
                <div class="content-block-size-control">
                    {%% for i in 1..%d %%}
                        <input type="radio" name="%s" value="{{ i }}" id="component-block-size-{{ i }}" {{ (i < min_%s or i > max_%s or (i in forbidden_sizes))  ? \'disabled\' : \'\' }} {{ %s == i ? \'checked\' : \'\' }}><label for="component-block-size-{{ i }}"></label>
                    {%% endfor %%}}
                </div>
            </div>
        ', $maxSize, $fieldName, $fieldName, $fieldName, $fieldName);
    }

    protected function getBlockMinSize()
    {
        $minSize = static::MIN_SIZE ?: $this->module->getBlockMinSize();

        if ($minSize < $this->module->getBlockMinSize()) {
            $minSize = $this->module->getBlockMinSize();
        }

        return $minSize;
    }

    protected function getBlockMaxSize()
    {
        $maxSize = static::MAX_SIZE ?: $this->module->getBlockMaxSize();

        if ($maxSize > $this->module->getBlockMaxSize()) {
            $maxSize = $this->module->getBlockMaxSize();
        }

        return $maxSize;
    }

    protected function retrieveEntity(ComponentRequest $request, ComponentResponse $response)
    {
        $entity = parent::retrieveEntity($request, $response);

        if ($entity->isNew()) {
            $entity->setContentId($request->query->get('content'));
            $component = substr(get_class($this), strrpos(get_class($this), '\\') + 1);
            $type = lcfirst(preg_replace('/BlockEditor$/', '', $component));
            $entity->setType($type);

            if ($request->query->get('parent_block_id') and $this->parentBlock = $parentBlock = $this->createQuery()->findPk($request->query->get('parent_block_id'))) {
                $entity->setParentId($request->query->get('parent_block_id'));
            }

            if ($this->sizeField) {
                $blockMaxSize = $this->getBlockMaxSize();

                if ($this->parentBlock) {
                    if ($blockMaxSize > $this->parentBlock->getSize()) {
                        $blockMaxSize = $this->parentBlock->getSize();
                    }
                }

                $this->sizeField->save($entity, $blockMaxSize);
            }
        } else {
            $this->parentBlock = $entity->getParent();
        }

        return $entity;
    }


    public function decorate(ComponentRequest $request, ComponentResponse $response, $entity)
    {
        if ($this->sizeField) {
            $blockMaxSize = $this->getBlockMaxSize();
            if ($this->parentBlock and $blockMaxSize > $this->parentBlock->getSize()) {
                $blockMaxSize = $this->parentBlock->getSize();
            }

            $response->data->set('min_' . $this->sizeField->getName(), $this->getBlockMinSize());
            $response->data->set('max_' . $this->sizeField->getName(), $blockMaxSize);
            $response->data->set('forbidden_sizes', $this->module->getBlockForbiddenSizes());
        }
    }

    /**
     * @param $block
     * @return ContentBlockPreview
     */
    public function getBlockPreview($block)
    {
        return new ContentBlockPreview();
    }
}