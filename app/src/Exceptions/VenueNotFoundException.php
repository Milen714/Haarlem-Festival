<?php

namespace App\Exceptions;

/**
 * VenueNotFoundException
 * 
 * Thrown when a dance venue cannot be found by ID, name, or other identifier.
 * This includes missing venue information or invalid venue references.
 * 
 * Examples:
 * - "Venue not found"
 * - "Dance venue does not exist"
 * - "Venue information unavailable"
 * 
 * Should result in a 404 Not Found HTTP response.
 */
class VenueNotFoundException extends ResourceNotFoundException {}
