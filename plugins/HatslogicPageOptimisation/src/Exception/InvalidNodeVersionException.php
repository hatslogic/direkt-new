<?php declare(strict_types=1);

namespace HatslogicPageOptimisation\Exception;

use Exception;
use Throwable;

class InvalidNodeVersionException extends Exception
{
    public function __construct(string $actual, string $expected, Throwable $previous = null)
    {
        parent::__construct("Invalid Node JS version detected. Excepted version $expected, actual version $actual.", 500, $previous);
    }
}
