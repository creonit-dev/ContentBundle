<?php

namespace Creonit\ContentBundle\Admin\ContentBlockModule;

use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\AdminBundle\Component\Response\ComponentResponse;
use Creonit\AdminBundle\Component\Scope\Scope;
use Symfony\Component\HttpFoundation\ParameterBag;

class ChooseContentBlockTable extends ContentBlockTable
{
    protected $block;

    /**
     * @action choose(blockId, parentBlockId){
     *   this.request(parentBlockId ? 'choose' : 'chooseEmpty', {block_id: blockId, parent_block_id: parentBlockId}, {}, function(response){
     *     if(this.checkResponse(response)){
     *       this.close();
     *     }
     *   });
     *   this.parent.loadData();
     * }
     *
     * \ContentBlockEmpty
     * @data [{}]
     * @col {{ button('', {icon: 'check', size: 'xs'}) | action('choose', _query.block_id) }}
     * @col <nobr>{{ 'Без родителя' | icon('times') }}</nobr>
     * @col
     *
     */
    public function schema()
    {
        $this->setTitle('Выберите родительский блок');
        $this->setHeader('');

        $columns = $this->getColumns();
        array_pop($columns);
        array_unshift($columns, ['value' => '', 'width' => '1%']);
        $this->columns = $columns;

        $contentBlockScope = $this->getScope('ContentBlock');
        $contentBlockScope->setSortable(false);
        $contentBlockColumns = $contentBlockScope->getColumns();
        $contentBlockColumns[0] = '
            <nobr>
                {{ ((blockSize | raw) ~ blockTitle | icon(_data.blockTypes[type].icon)) | controls }}
            </nobr>
        ';
        array_pop($contentBlockColumns);
        array_unshift($contentBlockColumns, "{% if not forbidden %}{{ button('', {icon: 'check', size: 'xs'}) | action('choose', _query.block_id, _key) }}{% endif %}");
        $contentBlockScope->setColumns($contentBlockColumns);


        $this->setHandler('choose', function (ComponentRequest $request, ComponentResponse $response) {
            $block = $this->getScope('ContentBlock')->createQuery()->findPk($request->query->get('block_id'));
            $parentBlock = $this->getScope('ContentBlock')->createQuery()->findPk($request->query->get('parent_block_id'));
            if ($block && $parentBlock) {
                if ($parentBlock->getSize() < $block->getSize()) {
                    $response->flushError('Выбираемая группа имеет меньший размер чем у блока');
                }
                $block->setParentId($parentBlock->getId())->save();

            } else {
                $response->flushError('Ошибка переноса блока');
            }
        });

        $this->setHandler('chooseEmpty', function (ComponentRequest $request, ComponentResponse $response) {
            if ($block = $this->getScope('ContentBlock')->createQuery()->findPk($request->query->get('block_id'))) {
                $block->setParentId(null)->save();
            } else {
                $response->flushError('Блок не найден');
            }
        });
    }


    protected function loadData(ComponentRequest $request, ComponentResponse $response)
    {
        if (!$this->block = $this->getScope('ContentBlock')->createQuery()->findPk($request->query->get('block_id'))) {
            $response->flushError('Блок не найден');
        }

        parent::loadData($request, $response);
    }

    protected function filter(ComponentRequest $request, ComponentResponse $response, $query, Scope $scope, $relation, $relationValue, $level)
    {
        if ($scope->getName() === 'ContentBlock') {
            $query->filterByContentId($this->block->getContentId());
            if ($relationValue === $this->block->getId()) {
                $query->where('1<>1');
            }
        }
    }

    protected function decorate(ComponentRequest $request, ComponentResponse $response, ParameterBag $data, $entity, Scope $scope, $relation, $relationValue, $level)
    {
        parent::decorate($request, $response, $data, $entity, $scope, $relation, $relationValue, $level);

        if ($scope->getName() === 'ContentBlock') {
            $data->set('blockSize', $this->getBlockSizeIndicator($entity, $this->block->getSize()));

            if (isset($this->blockTypes[$data->get('type')])) {
                $blockType = $this->blockTypes[$data->get('type')];

                if ($blockType['permitted_children'] !== true) {
                    $data->set('forbidden', true);

                    if($entity !== $this->block->getParent()){
                        $data->set('_row_class', 'deactive');
                    }
                }
            }

            if ($entity === $this->block) {
                $data->set('_row_class', 'warning');
                $data->set('forbidden', true);

            } else if ($entity === $this->block->getParent()) {
                $data->set('_row_class', 'success');
            }
        }
    }
}