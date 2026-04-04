<?php

namespace App\Exceptions;

/**
 * ArtistNotFoundException
 * 
 * Thrown when a dance artist cannot be found by ID, slug, or other identifier.
 * This includes missing artist profiles, invalid artist slugs, or non-existent artist data.
 * 
 * Examples:
 * - "Artist not found"
 * - "Artist with slug 'example' does not exist"
 * - "Artist profile unavailable"
 * 
 * Should result in a 404 Not Found HTTP response.
 */
class ArtistNotFoundException extends ResourceNotFoundException {}
