<?php

namespace App\Exceptions;

/**
 * UnauthorizedException
 * 
 * Thrown when a user does not have permission to access a protected resource.
 * Used for authorization failures distinct from authentication (401 vs 403).
 * 
 * Examples:
 * - "You do not have permission to access this order"
 * - "Only admins can perform this action"
 * 
 * Should result in a 403 Forbidden HTTP response.
 */
class UnauthorizedException extends UserFacingException {}
