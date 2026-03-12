<?php

namespace App\Exceptions;

/**
 * ResourceNotFoundException
 * 
 * Thrown when a requested resource (artist, venue, schedule, etc.) cannot be found.
 * The exception message is user-safe and explains what was not found.
 * 
 * Examples:
 * - "Artist not found"
 * - "Jazz page not found"
 * - "Schedule does not exist"
 * 
 * Should result in a 404 Not Found HTTP response.
 */
class ResourceNotFoundException extends UserFacingException {}
