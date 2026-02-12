<?php 

namespace App\Repositories;
use App\Framework\Repository;
use App\Repositories\Interfaces\IUserRepository;
use App\Models\User;
use App\Models\Enums\UserRole;
use DateTime;

use PDO;
use PDOException;


class UserRepository extends Repository implements IUserRepository{
	private function mapUser(array $data): User {
        $user = new User();
        $user->id = isset($data['id']) ? (int)$data['id'] : null;
        $user->email = $data['email'];
        $user->password_hash = $data['password_hash'] ?? '';
        $user->fname = $data['fname'] ?? null;
        $user->lname = $data['lname'] ?? null;
        $user->role = UserRole::from($data['role'] ?? 'CUSTOMER');
        $user->address = $data['address'] ?? null;
        $user->phone = $data['phone'] ?? null;
        $user->verification_token = $data['verification_token'] ?? null;
        $user->reset_token = $data['reset_token'] ?? null;
        $user->reset_token_expiry = isset($data['reset_token_expiry']) && $data['reset_token_expiry'] 
            ? new DateTime($data['reset_token_expiry']) 
            : null;
        $user->is_active = isset($data['is_active']) ? (bool)$data['is_active'] : true;
        $user->is_verified = isset($data['is_verified']) ? (bool)$data['is_verified'] : false;
        $user->created_at = isset($data['created_at']) && $data['created_at'] 
            ? new DateTime($data['created_at']) 
            : null;
		return $user;
	}
	public function getUserById(int $id): ?User {
		try{
			$pdo = $this->connect();
			$query = 'SELECT * FROM users WHERE id = :id';
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':id', $id);
			$stmt->execute();
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			
			return $user ? $this->mapUser($user) : null;
		}catch(PDOException $e){
			die("Error fetching user: " . $e->getMessage());
		}

	}
	
	public function getAllUsers(): array {
		
		return [];
	}
	
	public function getUserByEmail(string $email): ?User {
		try{
            $pdo = $this->connect();
            $query = 'SELECT * FROM users WHERE email = :email';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $user ? $this->mapUser($user) : null;


        }catch(PDOException $e){
            die("Error fetching user: " . $e->getMessage());
        }
	}
	
	public function createUser(User $user): bool {
		try{
			$pdo = $this->connect();
			$query = 'INSERT INTO users (email, password_hash, fname, lname, role, address, phone, 
					verification_token, is_active, is_verified) 
					VALUES (:email, :password_hash, :fname, :lname, :role, :address, :phone, 
					:verification_token, :is_active, :is_verified)';
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':email', $user->email);
			$stmt->bindParam(':password_hash', $user->password_hash);
			$stmt->bindParam(':fname', $user->fname);
			$stmt->bindParam(':lname', $user->lname);
			$roleValue = $user->role->value;
			$stmt->bindParam(':role', $roleValue);
			$stmt->bindParam(':address', $user->address);
			$stmt->bindParam(':phone', $user->phone);
			$stmt->bindParam(':verification_token', $user->verification_token);
			$stmt->bindParam(':is_active', $user->is_active, PDO::PARAM_BOOL);
			$stmt->bindParam(':is_verified', $user->is_verified, PDO::PARAM_BOOL);
			return $stmt->execute();
		}catch(PDOException $e){
			die("Error creating user: " . $e->getMessage());
		}
	}
	public function updateUser(User $user): bool {
        try {
            $pdo = $this->connect();
            $query = 'UPDATE users SET email = :email, password_hash = :password_hash, 
                    fname = :fname, lname = :lname, role = :role, address = :address, phone = :phone,
                    verification_token = :verification_token, reset_token = :reset_token, 
                    reset_token_expiry = :reset_token_expiry, is_active = :is_active, is_verified = :is_verified
                    WHERE id = :id';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':email', $user->email);
            $stmt->bindParam(':password_hash', $user->password_hash);
            $stmt->bindParam(':fname', $user->fname);
            $stmt->bindParam(':lname', $user->lname);
            $roleValue = $user->role->value;
            $stmt->bindParam(':role', $roleValue);
            $stmt->bindParam(':address', $user->address);
            $stmt->bindParam(':phone', $user->phone);
            $stmt->bindParam(':verification_token', $user->verification_token);
            $stmt->bindParam(':reset_token', $user->reset_token);
            $resetTokenExpiry = $user->reset_token_expiry ? $user->reset_token_expiry->format('Y-m-d H:i:s') : null;
            $stmt->bindParam(':reset_token_expiry', $resetTokenExpiry);
            $stmt->bindParam(':is_active', $user->is_active, PDO::PARAM_BOOL);
            $stmt->bindParam(':is_verified', $user->is_verified, PDO::PARAM_BOOL);
            $stmt->bindParam(':id', $user->id, PDO::PARAM_INT); 
            return $stmt->execute();

        } catch (PDOException $e) {
            throw new \Exception("Error updating user: " . $e->getMessage());
        }
	}
}