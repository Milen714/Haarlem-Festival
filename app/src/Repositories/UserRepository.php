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
        $user->fname = $data['fname'];
        $user->lname = $data['lname'];
        $user->email = $data['email'];
        $user->role = UserRole::from($data['role'] ?? 'user');
        $user->password_hash = $data['password_hash'] ?? '';
        $user->address = $data['address'] ?? null;
        $user->post_code = $data['post_code'] ?? null;
        $user->country = $data['country'] ?? null;
        $user->state = $data['state'] ?? null;
        $user->verification_token = $data['verification_token'] ?? null;
        $user->reset_token = $data['reset_token'] ?? null;
        $user->reset_token_expiry = isset($data['reset_token_expiry']) && $data['reset_token_expiry'] 
            ? new DateTime($data['reset_token_expiry']) 
            : null;
        $user->created_at = isset($data['created_at']) && $data['created_at'] 
            ? new DateTime($data['created_at']) 
            : null;
        $user->isActive = isset($data['isActive']) ? (bool)$data['isActive'] : true;
        $user->isVerified = isset($data['isVerified']) ? (bool)$data['isVerified'] : false;
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
			$query = 'INSERT INTO users (fname, lname, email, password_hash, role, address, post_code, country, state, 
					verification_token, isActive) 
					VALUES (:fname, :lname, :email, :password_hash, :role, :address, :post_code, :country, :state, 
					:verification_token,  :isActive)';
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':fname', $user->fname);
			$stmt->bindParam(':lname', $user->lname);
			$stmt->bindParam(':email', $user->email);
			$stmt->bindParam(':password_hash', $user->password_hash);
			$roleValue = $user->role->value;
			$stmt->bindParam(':role', $roleValue);
			$stmt->bindParam(':address', $user->address);
			$stmt->bindParam(':post_code', $user->post_code);
			$stmt->bindParam(':country', $user->country);
			$stmt->bindParam(':state', $user->state);
			$stmt->bindParam(':verification_token', $user->verification_token);
			$stmt->bindParam(':isActive', $user->isActive, PDO::PARAM_BOOL);
			return $stmt->execute();
		}catch(PDOException $e){
			die("Error creating user: " . $e->getMessage());
		}
	}
	public function updateUser(User $user): bool {
        try {
            $pdo = $this->connect();
            $query = 'UPDATE users SET fname = :fname, lname = :lname, role = :role, email = :email, 
                    password_hash = :password_hash, address = :address, post_code = :post_code, 
                    country = :country, state = :state, verification_token = :verification_token,
                    reset_token = :reset_token, reset_token_expiry = :reset_token_expiry,
                    isActive = :isActive, isVerified = :isVerified
                    WHERE id = :id';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':fname', $user->fname);
            $stmt->bindParam(':lname', $user->lname);
            $roleValue = $user->role->value;
            $stmt->bindParam(':role', $roleValue);
            $stmt->bindParam(':email', $user->email);
            $stmt->bindParam(':password_hash', $user->password_hash);
            $stmt->bindParam(':address', $user->address);
            $stmt->bindParam(':post_code', $user->post_code); 
            $stmt->bindParam(':country', $user->country);
            $stmt->bindParam(':state', $user->state);
            $stmt->bindParam(':verification_token', $user->verification_token);
            $stmt->bindParam(':reset_token', $user->reset_token);
            $resetTokenExpiry = $user->reset_token_expiry ? $user->reset_token_expiry->format('Y-m-d H:i:s') : null;
            $stmt->bindParam(':reset_token_expiry', $resetTokenExpiry);
            $stmt->bindParam(':isActive', $user->isActive, PDO::PARAM_BOOL);
            $stmt->bindParam(':isVerified', $user->isVerified, PDO::PARAM_BOOL);
            $stmt->bindParam(':id', $user->id, PDO::PARAM_INT); 
            return $stmt->execute();

        } catch (PDOException $e) {
            throw new \Exception("Error updating user: " . $e->getMessage());
        }
	}
}