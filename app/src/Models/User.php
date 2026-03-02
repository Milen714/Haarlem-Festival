<?php 
namespace App\Models;
use App\Models\Enums\UserRole;
use DateTime;
class User {
    public ?int $id = null;
    public string $email;
    public string $password_hash;
    public ?string $fname = null;
    public ?string $lname = null;
    public UserRole $role;
    public ?string $address = null;
    public ?string $phone = null;
    public ?string $verification_token = null;
    public ?string $reset_token = null;
    public ?DateTime $reset_token_expiry = null;
    public bool $is_active = true;
    public bool $is_verified = false;
    public ?DateTime $created_at = null;

    public function __construct(){}

    public function fromPost(): User {
        $user = new User();
        $user->email = $_POST['email'] ?? '';
        $user->password_hash = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);
        $user->fname = $_POST['fname'] ?? null;
        $user->lname = $_POST['lname'] ?? null;
        $user->role = UserRole::CUSTOMER;
        $user->address = $_POST['address'] ?? null;
        $user->phone = $_POST['phone'] ?? null;
        $user->is_active = true;
        $user->is_verified = false;
        return $user;
    }
    public function fromArray($data): User{
        $user = new User();
        $user->email = $data['email'] ?? '';
        $user->password_hash = password_hash($data['password'] ?? '', PASSWORD_BCRYPT);
        $user->fname = $data['fname'] ?? null;
        $user->lname = $data['lname'] ?? null;
        $user->role = UserRole::CUSTOMER;
        $user->address = $data['address'] ?? null;
        $user->phone = $data['phone'] ?? null;
        $user->is_active = true;
        $user->is_verified = false;
        return $user;
    }
}