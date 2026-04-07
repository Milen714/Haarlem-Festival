<?php

namespace App\Exceptions;

/**
 * RestaurantNotFoundException
 *
 * Thrown when a restaurant cannot be found by ID, slug, or other identifier.
 * This includes missing restaurant profiles, invalid restaurant slugs, or non-existent restaurant data.
 *
 * Examples:
 * - "Restaurant not found"
 * - "Restaurant with slug 'example' does not exist"
 *
 * Should result in a 404 Not Found HTTP response.
 */
class RestaurantNotFoundException extends ResourceNotFoundException {}
