<?php

namespace Wurstpress\CoreBundle\Common;

trait ParentTrait
{
    public function getParent()
    {
        return $this->parent;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }
}
