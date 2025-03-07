<?php

namespace Goteo\Benzina\Pump;

trait ArrayPumpTrait
{
    /**
     * Determine if the data array has all the necessary keys.
     *
     * @param array $sample A sample of the data
     * @param array $keys   The keys that the data should have
     */
    public function hasAllKeys(array $sample, array $keys): bool
    {
        if (0 === count(\array_diff($keys, \array_keys($sample)))) {
            return true;
        }

        return false;
    }
}
