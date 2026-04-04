<?php

namespace App\Exceptions;

/**
 * ScheduleNotFoundException
 * 
 * Thrown when a dance schedule or performance time cannot be found.
 * This includes missing schedule data, invalid schedule IDs, or non-existent performance times.
 * 
 * Examples:
 * - "Schedule not found"
 * - "Performance schedule does not exist"
 * - "Artist schedule unavailable"
 * 
 * Should result in a 404 Not Found HTTP response.
 */
class ScheduleNotFoundException extends ResourceNotFoundException {}
