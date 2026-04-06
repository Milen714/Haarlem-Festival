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
    public ?string $share_token = null;


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

    public function fromPDOData(array $data): void
    {
        $this->id = isset($data['user_id']) ? (int)$data['user_id'] : null;
        $this->email = $data['user_email'] ?? $data['email'] ?? '';
        $this->password_hash = $data['password_hash'] ?? '';
        $this->fname = $data['user_fname'] ?? $data['fname'] ?? null;
        $this->lname = $data['user_lname'] ?? $data['lname'] ?? null;
        $this->role = isset($data['user_role']) ? UserRole::from($data['user_role']) : UserRole::CUSTOMER;
        $this->address = $data['user_address'] ?? $data['address'] ?? null;
        $this->phone = $data['user_phone'] ?? $data['phone'] ?? null;
        $this->verification_token = $data['verification_token'] ?? null;
        $this->reset_token = $data['reset_token'] ?? null;
        $this->reset_token_expiry = isset($data['reset_token_expiry']) ? new DateTime($data['reset_token_expiry']) : null;
        $this->share_token = $data['share_token'] ?? null;
        $this->is_active = isset($data['is_active']) ? (bool)$data['is_active'] : true;
        $this->is_verified = isset($data['is_verified']) ? (bool)$data['is_verified'] : false;
        $this->created_at = isset($data['user_created_at']) ? new DateTime($data['user_created_at']) : null;
    }
}