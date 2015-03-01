<?php

namespace Face\Traits;

/**
 * Class ContextAwareTrait
 *
 * this trait helps to do create a context for a job in the whole class
 *
 * context are just a namespace appended to a named operation
 *
 */
trait ContextAwareTrait
{

    /**
     * the name context used to shortcut the call to other entities
     * view https://github.com/face-orm/face/issues/6
     * @var string
     */
    protected $__contextAwareContext;

    /**
     * Change the query context
     * if you specify "lemon", every dynamic name will be in the context "lemon" until you set context to null
     * e.g when you will use "~id" it wlil transform to "~lemon.id"
     * @param $context
     * @return $this
     */
    public function context($context = null)
    {
        if (is_null($context) || empty($context) || $context == "this") {
            $context = null;
        } else {
            $this->__contextAwareContext = $context;
        }
        return $this;
    }


    /**
     * @param $name
     * @return string
     */
    protected function getNameInContext($name)
    {
        if (null === $this->__contextAwareContext) {
            return $name;
        } else {
            if ("~" === $name[0]) {
                return "~" . $this->__contextAwareContext . "." . ltrim($name, "~");
            } else {
                return $this->__contextAwareContext. "." . $name;
            }
        }
    }

    public function getContext()
    {
        return $this->__contextAwareContext;
    }
}
