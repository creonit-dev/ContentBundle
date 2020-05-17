<?php

namespace Creonit\ContentBundle\Admin;

use Creonit\AdminBundle\Plugin;
use Creonit\ContentBundle\Admin\Component\Field\ContentField;

class ContentPlugin extends Plugin
{
    protected $types = [];

    public function configure()
    {
        $this->addJavascript('/bundles/creonitcontent/js/common.js');
        $this->addStylesheet('/bundles/creonitcontent/css/common.css');

        $this->addInjection('head_script', 'var CreonitContentTypes = ' . json_encode($this->types) . ';');

        $this->addFieldType(ContentField::class);
    }

    public function setType($name, $type)
    {
        $this->types[$name] = $type;
    }
}