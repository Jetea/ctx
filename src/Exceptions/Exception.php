<?php

namespace Jetea\Ctx\Exceptions;

/**
 * ctx exception
 *
 * @copyright sh7ning 2016.1
 * @author    sh7ning
 * @version   0.0.1
 */
class Exception extends \RuntimeException
{
    /**
     * Exception constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct($message = '', $code = 0)
    {
        parent::__construct($message, $code);
    }
}
