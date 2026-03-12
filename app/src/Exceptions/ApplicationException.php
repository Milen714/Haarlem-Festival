<?php

namespace App\Exceptions;

/**
 * ApplicationException
 * 
 * Thrown when an internal application error occurs that should never be shown to users.
 * This includes configuration errors, database failures, file system errors, etc.
 * 
 * Should always result in a 500 Internal Server Error response.
 * Error details are logged but not exposed to end users.
 */
class ApplicationException extends \RuntimeException {}
