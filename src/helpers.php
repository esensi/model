<?php

if ( ! function_exists('class_uses_recursive'))
{
    /**
     * Return the traits used by the given class including
     * parent classes and traits.
     *
     * @todo   remove when Laravel supports this recursion
     * @see    http://www.php.net/manual/en/function.class-uses.php#112671
     * @param  mixed  $class
     * @param  bool   $autoload
     * @return mixed
     */
    function class_uses_recursive($class, $autoload = true)
    {
        $traits = [];

        // Get traits of all parent classes
        do {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        } while ($class = get_parent_class($class));

        // Get traits of all parent traits
        $traitsToSearch = $traits;
        while ( ! empty($traitsToSearch)) {
            $newTraits = class_uses(array_pop($traitsToSearch), $autoload);
            $traits = array_merge($newTraits, $traits);
            $traitsToSearch = array_merge($newTraits, $traitsToSearch);
        };

        foreach (array_keys($traits) as $trait) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }

        return array_unique($traits);
    }
}
