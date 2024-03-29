<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Nelmio\Alice\Throwable\Exception\Generator\Resolver;

/**
 * @private
 */
final class CircularReferenceExceptionFactory
{
    public static function createForParameter(string $key, array $resolving): CircularReferenceException
    {
        return new CircularReferenceException(
            sprintf(
                'Circular reference detected for the parameter "%s" while resolving ["%s"].',
                $key,
                implode('", "', array_keys($resolving)),
            ),
        );
    }

    private function __construct()
    {
    }
}
