<?php

namespace App\Exceptions;

/**
 * TicketNotAvailableException
 * 
 * Thrown when dance tickets are not available for a schedule, venue, or event.
 * This includes sold-out performances, unavailable ticket types, or capacity limits.
 * 
 * Examples:
 * - "Tickets not available for this performance"
 * - "All tickets sold out"
 * - "Ticket sales have ended"
 * - "Venue capacity reached"
 * 
 * Should result in a user-friendly message and possibly alternative suggestions.
 */
class TicketNotAvailableException extends UserFacingException {}
