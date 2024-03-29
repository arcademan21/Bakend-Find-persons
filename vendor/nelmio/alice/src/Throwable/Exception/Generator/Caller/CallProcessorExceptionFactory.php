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

namespace Nelmio\Alice\Throwable\Exception\Generator\Caller;

use Nelmio\Alice\Definition\MethodCallInterface;

/**
 * @private
 */
final class CallProcessorExceptionFactory
{
    public static function createForNoProcessorFoundForMethodCall(MethodCallInterface $methodCall): ProcessorNotFoundException
    {
        return new ProcessorNotFoundException(
            sprintf(
                'No suitable processor found to handle the method call "%s".',
                $methodCall,
            ),
        );
    }

    private function __construct()
    {
    }
}
