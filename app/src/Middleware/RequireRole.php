<?php 
namespace App\Middleware;
use App\Models\UserRole;
use App\Models\User;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class RequireRole {
    public array $roles;

    public function __construct(UserRole|array $roles) {
        $this->roles = is_array($roles) ? $roles : [$roles];
    }
}