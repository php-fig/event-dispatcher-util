<?php
declare(strict_types=1);

namespace Fig\EventDispatcher;

/**
 * Utility trait to derive the type of event an event listener is for.
 */
trait ParameterDeriverTrait
{
    /**
     * Derives the class type of the first argument of a callable.
     *
     * If the callable is an array, referring to a method or static method, of an object
     * that is not yet defined then it may fail a callable check even if it will be callable later.
     * We therefore have to skip type hinting the parameter to avoid it failing in some edge cases.
     *
     * @param callable $callable
     *   The callable for which we want the parameter type.
     * @return string
     *   The class the parameter is type hinted on.
     */
    protected function getParameterType($callable): string
    {
        // This try-catch is only here to keep OCD linters happy about uncaught reflection exceptions.
        try {
            // See the docblock of isClassCallable() for why this needs to come first.
            if ($this->isClassCallable($callable)) {
                $reflect = new \ReflectionClass($callable[0]);
                $params = $reflect->getMethod($callable[1])->getParameters();
            } else {
                $reflect = new \ReflectionFunction(\Closure::fromCallable($callable));
                $params = $reflect->getParameters();
            }

            $rType = $params[0]->getType();
            if ($rType === null) {
                throw new \InvalidArgumentException('Listeners must declare an object type they can accept.');
            }
            $type = $rType->getName();
        }
        catch (\ReflectionException $e) {
            throw new \RuntimeException('Type error registering listener.', 0, $e);
        }

        return $type;
    }

    /**
     * Determines if a callable represents a function.
     *
     * Or at least a reasonable approximation, since a function name may not be defined yet.
     *
     * @deprecated No longer necessary so will be removed at some point in the future.
     *
     * @param callable $callable
     * @return bool
     *   True if the callable represents a function, false otherwise.
     */
    protected function isFunctionCallable(callable $callable): bool
    {
        // We can't check for function_exists() because it may be included later by the time it matters.
        return is_string($callable);
    }

    /**
     * Determines if a callable represents a closure/anonymous function.
     *
     * @deprecated No longer necessary so will be removed at some point in the future.
     *
     * @param callable $callable
     * @return bool
     *   True if the callable represents a closure object, false otherwise.
     */
    protected function isClosureCallable(callable $callable): bool
    {
        return $callable instanceof \Closure;
    }

    /**
     * Determines if a callable represents a method on an object.
     *
     * @deprecated No longer necessary so will be removed at some point in the future.
     *
     * @param callable $callable
     * @return bool
     *   True if the callable represents a method object, false otherwise.
     */
    protected function isObjectCallable(callable $callable): bool
    {
        return is_array($callable) && is_object($callable[0]);
    }

    /**
     * Determines if a callable represents a static class method.
     *
     * The parameter here is untyped so that this method may be called with an
     * array that represents a class name and a non-static method.  The routine
     * to determine the parameter type is identical to a static method, but such
     * an array is still not technically callable.  Omitting the parameter type here
     * allows us to use this method to handle both cases.
     *
     * This method must therefore be called first above, as the array is not actually
     * an `is_callable()` and will fail `Closure::fromCallable()`.  Because PHP.
     *
     * @param callable $callable
     * @return bool
     *   True if the callable represents a static method, false otherwise.
     */
    protected function isClassCallable($callable): bool
    {
        return is_array($callable) && is_string($callable[0]) && class_exists($callable[0]);
    }

    /**
     * Determines if a callable is a class that has __invoke() method
     *
     * @deprecated No longer necessary so will be removed at some point in the future.
     *
     * @param callable $callable
     * @return bool
     *   True if the callable represents an invokable object, false otherwise.
     */
    private function isInvokable(callable $callable): bool
    {
        return is_object($callable);
    }
}
