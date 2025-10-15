<?php declare(strict_types=1);

namespace HatslogicPageOptimisation\Exception;

use Exception;
use Throwable;

class InvalidNpmVersionException extends Exception
{
    public function __construct(string $actual, string $expected, Throwable $previous = null)
    {
        parent::__construct("Invalid NPM version detected. Expected version $expected, actual version $actual.", 500, $previous);
    }
}
