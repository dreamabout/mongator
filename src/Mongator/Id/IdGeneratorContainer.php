<?php

/*
 * This file is part of Mongator.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mongator\Id;

/**
 * Container of id generators.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class IdGeneratorContainer
{
    private static $map = array(
        'none'     => 'Mongator\Id\NoneIdGenerator',
        'native'   => 'Mongator\Id\NativeIdGenerator',
        'sequence' => 'Mongator\Id\SequenceIdGenerator',
    );

    private static $idGenerators = array();

    /**
     * Returns whether or not an id generator exists.
     *
     * @param string $name The name.
     *
     * @return Boolean Whether or not the id generator exists.
     */
    public static function has($name)
    {
        return isset(static::$map[$name]);
    }

    /**
     * Add an id generator.
     *
     * @param string $name  The name.
     * @param string $class The class.
     *
     * @throws \InvalidArgumentException If the id generator already exists.
     * @throws \InvalidArgumentException If the class is not a subclass of Mongator\Id\IdGenerator.
     */
    public static function add($name, $class)
    {
        if (static::has($name)) {
            throw new \InvalidArgumentException(sprintf('The id generator "%s" already exists.', $name));
        }

        $r = new \ReflectionClass($class);
        if (!$r->isSubclassOf('Mongator\Id\BaseIdGenerator')) {
            throw new \InvalidArgumentException(sprintf('The class "%s" is not a subclass of Mongator\Id\BaseIdGenerator.', $class));
        }

        static::$map[$name] = $class;
    }

    /**
     * Returns an id generator.
     *
     * @param string $name The name.
     *
     * @return \Mongator\Id\BaseIdGenerator The id generator.
     *
     * @throws \InvalidArgumentException If the id generator does not exists.
     */
    public static function get($name)
    {
        if (!isset(static::$idGenerators[$name])) {
            if (!static::has($name)) {
                throw new \InvalidArgumentException(sprintf('The id generator "%s" does not exists.', $name));
            }

            static::$idGenerators[$name] = new static::$map[$name];
        }

        return static::$idGenerators[$name];
    }

    /**
     * Remove an id generator.
     *
     * @param string $name The name.
     *
     * @throws \InvalidArgumentException If the id generator does not exists.
     */
    public static function remove($name)
    {
        if (!static::has($name)) {
            throw new \InvalidArgumentException(sprintf('The id generator "%s" does not exists.', $name));
        }

        unset(static::$map[$name], static::$idGenerators[$name]);
    }

    /**
     * Reset the id generators.
     */
    public static function reset()
    {
        static::$map = array(
            'none'     => 'Mongator\Id\NoneIdGenerator',
            'native'   => 'Mongator\Id\NativeIdGenerator',
            'sequence' => 'Mongator\Id\SequenceIdGenerator',
        );

        static::$idGenerators = array();
    }
}
