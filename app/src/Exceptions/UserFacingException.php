<?php

namespace App\Exceptions;

/**
 * UserFacingException
 * 
 * Base exception class for errors that are safe to show to end users.
 * These represent client errors (validation failures, not found, etc.) rather than
 * internal application faults.
 * 
 * The exception message is safe to display directly in the UI.
 * Extend this class when the error message contains information meant for the user.
 */
class UserFacingException extends \RuntimeException {}
