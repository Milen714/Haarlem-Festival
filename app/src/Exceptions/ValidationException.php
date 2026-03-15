<?php

namespace App\Exceptions;

/**
 * ValidationException
 * 
 * Thrown when user input fails validation.
 * The exception message contains validation errors safe to show to users.
 * 
 * Examples:
 * - "Artist name is required"
 * - "Email address is invalid"
 * - "Password must be at least 8 characters"
 * 
 * Should result in a redirect with the error message displayed in the form.
 */
class ValidationException extends UserFacingException {}
