<?php

namespace Creonit\ContentBundle\Admin;

use Creonit\AdminBundle\Plugin;

class ContentPlugin extends Plugin
{

    public function configure()
    {
        $this->addJavascript('/bundles/creonitcontent/plugin.js');
        $this->addStylesheet('/bundles/creonitcontent/plugin.css');
    }
}