<?php


namespace Creonit\ContentBundle\Admin\ContentBlockModule;


use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\AdminBundle\Component\Response\ComponentResponse;
use Creonit\AdminBundle\Component\Scope\Scope;
use Creonit\AdminBundle\Component\TableComponent;
use Creonit\ContentBundle\Model\ContentQuery;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @property AbstractContentBlockModule $module
 */
class ContentBlockTable extends TableComponent
{
    const COPY_CONTENT_BLOCKS_SESSION_KEY = '_copy_content_blocks';

    protected $blockMenu = [];

    /** @var array */
    protected $blockTypes;

    /**
     * @title Список блоков
     * @cols Блок, Предпросмотр, .
     * @header
     * {% if copy_content_available %}
     *   <div class="pull-right">
     *     <div class="">
     *       {% if paste_content_available %}
     *         {{ button('Вставить', {size: 'sm', icon: 'clipboard', type: 'info'}) | action('paste', _query) }}
     *       {% endif %}
     *       {{ button('Скопировать', {size: 'sm', icon: 'files-o'}) | action('copy', _query) }}
     *     </div>
     *   </div>
     * {% endif %}
     *
     * <div class="btn-group">
     *   <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
     *     Добавить блок <span class="caret"></span>
     *   </button>
     *
     *   {{ (blockMenu) | raw }}
     * </div>
     *
     * @action clone(blockId, $event) {
     *   $($event.currentTarget).find('i.icon').addClass('fa-spin fa-spinner');
     *
     *   this.request('clone', this.query, {blockId: blockId}, function(response) {
     *     this.checkResponse(response);
     *   }.bind(this));
     *
     *   this.loadData();
     * }
     *
     * @action copy(query, $event) {
     *   $($event.currentTarget).find('i.icon').addClass('fa-spin fa-spinner');
     *
     *   this.request('copy', query, {}, function(response) {
     *     if (this.checkResponse(response)) {
     *       alert('Контент скопирован');
     *     }
     *   }.bind(this));
     *
     *   this.parent.node.find('[js-component^="ContentBlockType.ContentBlockTable"]').each(function(i, item){
     *     $(item).data('creonit-component').loadData();
     *   });
     * }
     *
     * @action paste(query, $event) {
     *   $($event.currentTarget).find('i.icon').addClass('fa-spin fa-spinner');
     *
     *   this.request('paste', query, {}, function(response) {
     *     if (this.checkResponse(response)) {
     *
     *     }
     *   }.bind(this));
     *
     *   this.parent.node.find('[js-component^="ContentBlockType.ContentBlockTable"]').each(function(i, item){
     *     $(item).data('creonit-component').loadData();
     *   });
     * }
     *
     * \ContentBlock
     * @relation parent_id > ContentBlock.id
     * @sortable true
     *
     * @field type
     *
     * @col
     * {% set blockType = _data.blockTypes[type] %}
     * {% set controls = [] %}
     *
     * {% if blockType.permitted_parents %}
     *   {% set controls = controls | merge([
     *     button('', {size: 'xs', icon: 'arrow-up'}) | tooltip('Переместить') | open('ChooseContentBlockTable', {block_id: _key})
     *   ]) %}
     * {% endif %}
     *
     * {% if _data.copy_content_available %}
     *   {% set controls = controls | merge([
     *     button('', {size: 'xs', icon: 'files-o'}) | tooltip('Дублировать') | action('clone', _key)
     *   ]) %}
     * {% endif %}
     *
     * {% if blockMenu %}
     *   {% set controls = controls | merge([
     *     '<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Добавить <span class="caret"></span></button>' ~ blockMenu
     *   ]) %}
     * {% endif %}
     *
     * <nobr>
     * {{ ((blockSize | raw) ~ blockTitle | icon(blockType.icon) | open(blockType.component, {key: _key})) | controls(buttons(controls | join)) }}
     * </nobr>
     *
     * @col <div style="overflow-y: auto; max-height: 5em;">{{ blockPreview | raw }}</div>
     * @col {{ buttons(_visible() ~ _delete()) }}
     *
     * \ContentBlockAdd
     * @data[{}]
     * @col
     * <div class="btn-group">
     *   <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
     *     Добавить блок <span class="caret"></span>
     *   </button>
     *
     *   {{ (_data.blockMenu) | raw }}
     * </div>
     *
     * @col
     * @col
     */
    public function schema()
    {
        $this->addHandler('clone', [$this, 'cloneBlock']);
        $this->addHandler('copy', [$this, 'copyBlocks']);
        $this->addHandler('paste', [$this, 'pasteBlocks']);
    }

    public function copyBlocks(ComponentRequest $request, ComponentResponse $response)
    {
        if (!$content = ContentQuery::create()->findPk($request->query->get('content'))) {
            $response->flushError('Контент не найден');
        }

        $this->container->get('session')->set(static::COPY_CONTENT_BLOCKS_SESSION_KEY, $content->getId());
    }

    public function cloneBlock(ComponentRequest $request, ComponentResponse $response)
    {
        if (!$content = ContentQuery::create()->findPk($request->query->get('content'))) {
            $response->flushError('Контент не найден');
        }

        if ($contentDuplicator = $this->container->get('creonit_content')->getContentDuplicator()) {
            $contentDuplicator->cloneContentBlockById($content, $request->data->get('blockId'));
        }
    }

    public function pasteBlocks(ComponentRequest $request, ComponentResponse $response)
    {
        if (!$content = ContentQuery::create()->findPk($request->query->get('content'))) {
            $response->flushError('Контент не найден');
        }

        if (!$sourceContent = $this->retrieveContentForCopy()) {
            $response->flushError('Контент для копирование отсутствует');
        }

        if ($contentDuplicator = $this->container->get('creonit_content')->getContentDuplicator()) {
            $contentDuplicator->copyContentBlocks($sourceContent, $content);
        }

        $this->container->get('session')->remove(static::COPY_CONTENT_BLOCKS_SESSION_KEY);
    }

    protected function prepareSchema()
    {
        if (!$this->hasScope('ContentBlock')) {
            if (null !== $schema = $this->parseClassSchemaAnnotations(__CLASS__)) {
                $this->applySchemaAnnotations($schema['__']);
            }
        }

        parent::prepareSchema();
    }

    protected function loadData(ComponentRequest $request, ComponentResponse $response)
    {
        $blockTypes = [];
        foreach ($this->module->getBlockTypes() as $blockType) {
            $blockTypes[$blockType['name']] = $blockType;
        }

        $response->data->set('blockTypes', $this->blockTypes = $blockTypes);
        $response->data->set('blockMenu', $this->buildBlockMenu($request->query->get('content')));

        if ($contentDuplicator = $this->container->get('creonit_content')->getContentDuplicator()) {
            $response->data->set('copy_content_available', true);

            if ($sourceContent = $this->retrieveContentForCopy()) {
                $response->data->set('paste_content_available', true);
            }
        }

        parent::loadData($request, $response);
    }

    protected function filter(ComponentRequest $request, ComponentResponse $response, $query, Scope $scope, $relation, $relationValue, $level)
    {
        if ($scope->getName() === 'ContentBlock') {
            $query->filterByContentId($request->query->get('content'));
        }
    }

    protected function decorate(ComponentRequest $request, ComponentResponse $response, ParameterBag $data, $entity, Scope $scope, $relation, $relationValue, $level)
    {
        if ($scope->getName() === 'ContentBlock') {
            if (isset($this->blockTypes[$data->get('type')])) {
                $blockType = $this->blockTypes[$data->get('type')];
                $blockComponent = $this->module->getComponent($blockType['component']);

                $data->set('blockTitle', $blockType['title']);

                if ($blockType['permitted_children'] === true) {
                    $data->set('blockMenu', $this->buildBlockMenu($request->query->get('content'), $blockType, $data->get('_key')));
                }

                if ($blockComponent instanceof AbstractContentBlockEditor) {
                    $contentBlockPreview = $blockComponent->getBlockPreview($entity);
                    $data->set('blockPreview', $contentBlockPreview->getContent());

                    if ($contentBlockPreview->getTitle()) {
                        $data->set('blockTitle', sprintf('%s (%s)', $blockType['title'], $contentBlockPreview->getTitle()));
                    }
                }
            }

            $data->set('blockSize', $this->getBlockSizeIndicator($entity));
        }
    }

    protected function buildBlockMenu($contentId, $blockType = null, $blockId = null)
    {
        $translator = $this->container->get('translator');

        $buildItems = function ($items) use (&$buildItems, $translator, $contentId, $blockId) {
            $markup = [];

            foreach ($items as $key => $item) {
                if (isset($item['component'])) {
                    $markup[] = sprintf(
                        '<li>
                            <a href="#" js-component-action data-name="openComponent" data-options="[&quot;%s&quot;,{&quot;content&quot;: %s%s},{}]">
                                <i class="icon fa fa-%s"></i>%s
                            </a>
                        </li>',
                        $item['component'],
                        $contentId,
                        $blockId !== null ? ', &quot;parent_block_id&quot;: ' . $blockId : '',
                        $item['icon'],
                        $item['title']
                    );

                } else {
                    $markup[] = sprintf(
                        '<li class="dropdown-submenu">
                            <a href="#">
                                <i class="icon fa fa-%s"></i>%s
                            </a>
                            <ul class="dropdown-menu">
                                %s
                            </ul>
                        </li>',
                        $translator->trans($key . '.icon', [], 'content-block-section'),
                        $translator->trans($key, [], 'content-block-section'),
                        $buildItems($item)
                    );
                }
            }

            return implode('', $markup);
        };

        $markup = [];
        $markup[] = '<ul class="dropdown-menu multi-level">';
        $markup[] = $buildItems($this->getBlockMenu($blockType));
        $markup[] = '</ul>';

        return implode('', $markup);
    }

    protected function getBlockMenu($parentBlockType = null)
    {
        $scope = $parentBlockType ? $parentBlockType['name'] : '_';

        if (array_key_exists($scope, $this->blockMenu)) {
            return $this->blockMenu[$scope];
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $sections = [];

        foreach ($this->module->getBlockTypes() as $blockType) {
            if ($parentBlockType) {
                if ($blockType['permitted_parents'] !== true || $blockType['name'] === $parentBlockType['name']) {
                    continue;
                }
            }

            if (!$blockType['section']) {
                $sections[] = $blockType;
                continue;
            }

            $path = array_map(
                function ($path) {
                    return "[{$path}]";
                },
                explode('/', $blockType['section'])
            );

            $path = implode('', $path);

            $propertyAccessor->setValue(
                $sections,
                $path,
                array_merge(
                    $propertyAccessor->getValue($sections, $path) ?: [],
                    [$blockType]
                )
            );
        }

        return $this->blockMenu[$scope] = $sections;
    }

    protected function getBlockSizeIndicator($entity, $limit = null)
    {
        $maxSize = $this->module->getBlockMaxSize();
        $size = $entity->getSize();

        if ($maxSize <= $this->module->getBlockMinSize()) {
            return '';
        }

        $markup = [];

        if ($maxSize > 4) {
            $markup[] = '<span style="display: inline-block; vertical-align: middle; padding-right: 8px; line-height: 1; position: relative; top: -1px;  color: #ccc; font-size: .75em" data-toggle="tooltip" data-placement="top" title="' . (round($size / $maxSize * 100, 1)) . '%">';
            $markup[] = '<span style="color: #999">' . $size . '</span>/' . $maxSize;
            $markup[] = '</span>';

        } else {
            $markup[] = '<span style="display: inline-block; vertical-align: middle; font-weight: bold; padding-right: 10px; text-align: right; position: relative; top: -2px; letter-spacing: -.6ex; color: #999">';

            if ($limit) {
                for ($i = 1; $i <= $maxSize; $i++) {
                    $color = $limit <= $size ? ($i <= $limit ? 'color:#4cae4c' : '') : ($i <= $limit ? 'color:#d43f3a' : '');
                    $markup[] = "<span style='$color'>" . ($size >= $i ? '◼' : '◻') . '</span>';
                }

            } else {
                $markup[] = str_repeat('◼', $size) . str_repeat('◻', $maxSize - $size);
            }

            $markup[] = '</span>';
        }


        return implode('', $markup);
    }

    protected function retrieveContentForCopy()
    {
        $sourceContentId = $this->container->get('session')->get(static::COPY_CONTENT_BLOCKS_SESSION_KEY);
        if ($sourceContentId and $sourceContent = ContentQuery::create()->findPk($sourceContentId)) {
            return $sourceContent;
        }

        return null;
    }
}