<?php

namespace App\Exceptions;

/**
 * LandmarkNotFoundException
 *
 * Thrown when a landmark cannot be found by ID, slug, or other identifier.
 * This includes missing landmark profiles, invalid landmark slugs, or non-existent landmark data.
 *
 * Examples:
 * - "Landmark not found"
 * - "Landmark with slug 'example' does not exist"
 *
 * Should result in a 404 Not Found HTTP response.
 */
class LandmarkNotFoundException extends ResourceNotFoundException {}
