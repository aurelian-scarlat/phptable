<?php

namespace Ascarlat\PhpTable\Traits;

use Ascarlat\PhpTable\Exceptions\TableException;

/**
 * Trait to make all method in a class callable both static and non-static
 * It only works for protected or private methods that start with _ and return $this
 * The point is to have them chainable in whatever order
 * e.g. Class::foo()->bar() or Class::bar()->foo()
 */
trait StaticCallerTrait
{
    /**
     * This method is called when another method was called in a static context
     *
     * @param string $name Method name
     * @param array $arguments Method arguments
     *
     * @return self $instance
     * @throws TableException
     */
    public static function __callStatic(string $name, array $arguments): self
    {
        $instance = new static();
        return $instance->__call($name, $arguments);
    }

    /**
     * This method is called when another method was called in a non-static context
     * To prevent calls to all protected/private methods, their name needs to start
     * with _ and MUST return an instance of the object
     *
     * @param string $name Method name
     * @param array $arguments Method arguments
     *
     * @return $this
     * @throws TableException
     */
    public function __call(string $name, array $arguments): self
    {
        $name = '_' . $name;
        if (!method_exists($this, $name)) {
            throw new TableException(
                "Cannot call " . static::class . "::" . $name . "() because it does not exist",
                101
            );
        }
        return $this->$name(...$arguments);
    }
}
