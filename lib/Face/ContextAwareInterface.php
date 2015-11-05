<?php

namespace Face;


/**
 * @see \Face\Traits\ContextAwareTrait
 */
interface ContextAwareInterface {

    public function context($context = null);
    public function getNameInContext($name);
    public function getContext();

}
