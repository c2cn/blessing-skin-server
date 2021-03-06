<?php

use PHPUnit_Framework_Assert as PHPUnit;

/**
 * Add ability to interact with cache in tests.
 *
 * @see Illuminate\Foundation\Testing\Concerns\InteractsWithSession
 */
trait InteractsWithCache
{
    /**
     * Set the cache to the given array.
     *
     * @param  array  $data
     * @return $this
     */
    public function withCache(array $data, $minutes = 60)
    {
        $this->cache($data, $minutes);

        return $this;
    }

    /**
     * Set the cache to the given array.
     *
     * @param  array  $data
     * @return void
     */
    public function cache(array $data, $minutes = 60)
    {
        foreach ($data as $key => $value) {
            $this->app['cache']->put($key, $value, $minutes);
        }
    }

    /**
     * Flush all of the current cache data.
     *
     * @return void
     */
    public function flushCache()
    {
        $this->app['cache']->flush();
    }

    /**
     * Assert that the cache has a given value.
     *
     * @param  string|array  $key
     * @param  mixed  $value
     * @return void
     */
    public function seeInCache($key, $value = null)
    {
        $this->assertCacheHas($key, $value);

        return $this;
    }

    /**
     * Assert that the cache has a given value.
     *
     * @param  string|array  $key
     * @param  mixed  $value
     * @return void
     */
    public function assertCacheHas($key, $value = null)
    {
        if (is_array($key)) {
            return $this->assertCacheHasAll($key);
        }

        if (is_null($value)) {
            PHPUnit::assertTrue($this->app['cache.store']->has($key), "Cache missing key: $key");
        } else {
            PHPUnit::assertEquals($value, $this->app['cache.store']->get($key));
        }
    }

    /**
     * Assert that the cache has a given list of values.
     *
     * @param  array  $bindings
     * @return void
     */
    public function assertCacheHasAll(array $bindings)
    {
        foreach ($bindings as $key => $value) {
            if (is_int($key)) {
                $this->assertCacheHas($value);
            } else {
                $this->assertCacheHas($key, $value);
            }
        }
    }

    /**
     * Assert that the cache does not have a given key.
     *
     * @param  string|array  $key
     * @return void
     */
    public function assertCacheMissing($key)
    {
        if (is_array($key)) {
            foreach ($key as $k) {
                $this->assertCacheMissing($k);
            }
        } else {
            PHPUnit::assertFalse($this->app['cache.store']->has($key), "Cache has unexpected key: $key");
        }
    }
}
