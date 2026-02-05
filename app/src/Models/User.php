<?php 
namespace App\Models;
use App\Models\Enums\UserRole;
use DateTime;
class User {
    public ?int $id = null;
    public string $fname;
    public string $lname;
    public UserRole $role;
    public string $email;
    public string $password_hash;
    public ?string $address;
    public ?string $post_code;
    public ?string $country;
    public ?string $state;
    public ?string $verification_token;
    public ?string $reset_token;
    public ?DateTime $reset_token_expiry;
    public ?DateTime $created_at;
    public ?bool $isActive;
    public ?bool $isVerified;

    public function __construct(){}

    public function fromPost(): User {
        $user = new User();
        $user->fname = $_POST['fname'] ?? '';
        $user->lname = $_POST['lname'] ?? '';
        $user->email = $_POST['email'] ?? '';
        $user->role = UserRole::CUSTOMER;
        $user->password_hash = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);
        $user->address = $_POST['address'] ?? null;
        $user->post_code = $_POST['post_code'] ?? null;
        $user->country = $_POST['country'] ?? null;
        $user->state = $_POST['state'] ?? null;
        $user->isActive = true;
        $user->isVerified = false;
        return $user;
    }
}