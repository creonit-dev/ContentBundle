<?php

namespace Creonit\ContentBundle\Behavior;

use Propel\Generator\Behavior\Sortable\SortableBehavior;
use Propel\Generator\Model\Behavior;
use Propel\Generator\Model\ForeignKey;

class ContentBlockBehavior extends Behavior
{
    protected $contentBlockTableName = 'content_block';

    public function modifyDatabase()
    {
        foreach ($this->getTables() as $table) {
            if ($table->hasBehavior($this->getId())) {
                // don't add the same behavior twice
                continue;
            }

            if ($table->getName() !== $this->contentBlockTableName) {
                // don't add the same behavior twice
                continue;
            }
            $behavior = clone $this;
            $table->addBehavior($behavior);

            if (!$table->hasBehavior('tree')) {
                $table->addBehavior(['name' => 'tree']);
            }

            if (!$table->hasBehavior('sortable_rank')) {
                $sortableBehavior = new SortableBehavior();
                $sortableBehavior->addParameter(['name' => 'scope_column', 'value' => 'content_id,parent_id']);
                $table->addBehavior($sortableBehavior);
            }
        }
    }

    public function modifyTable()
    {
        $table = $this->getTable();

        if (!$table->hasColumn('content_id')) {
            $table->addColumn([
                'name' => 'content_id',
                'type' => 'integer',
                'required' => true
            ]);

            $fk = new ForeignKey();
            $fk->setForeignTableCommonName('content');
            $fk->setForeignSchemaName($table->getSchema());
            $fk->setDefaultJoin('INNER JOIN');
            $fk->setOnDelete(ForeignKey::CASCADE);
            $fk->setOnUpdate(ForeignKey::CASCADE);
            $fk->addReference('content_id', 'id');
            $table->addForeignKey($fk);
        }

        if (!$table->hasColumn('type')) {
            $table->addColumn([
                'name' => 'type',
                'type' => 'varchar',
                'size' => 20,
                'required' => true
            ]);
        }

        if (!$table->hasColumn('size')) {
            $table->addColumn([
                'name' => 'size',
                'type' => 'tinyint',
                'required' => true
            ]);
        }

        if (!$table->hasColumn('visible')) {
            $table->addColumn([
                'name' => 'visible',
                'type' => 'boolean',
                'default' => 1,
                'required' => true
            ]);
        }
    }
}