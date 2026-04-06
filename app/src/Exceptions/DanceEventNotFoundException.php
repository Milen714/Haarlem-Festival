<?php

namespace App\Exceptions;

/**
 * DanceEventNotFoundException
 * 
 * Thrown when a dance event page or related content cannot be found.
 * This includes missing pages, invalid slugs, or non-existent dance event data.
 * 
 * Examples:
 * - "Dance event page not found"
 * - "Invalid dance event slug"
 * - "Dance event content not available"
 * 
 * Should result in a 404 Not Found HTTP response.
 */
class DanceEventNotFoundException extends ResourceNotFoundException {}
