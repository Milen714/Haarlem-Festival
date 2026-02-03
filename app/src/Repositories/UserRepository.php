<?php 

namespace App\Repositories;
use App\Framework\Repository;
use App\Repositories\Interfaces\IUserRepository;
use App\Models\User;

use PDO;
use PDOException;


class UserRepository extends Repository implements IUserRepository{
	private function mapUser(array $data): User {
        $user = new User();
        $user->id = $data['id'];
        $user->fname = $data['fname'];
        $user->lname = $data['lname'];
        $user->email = $data['email'];
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
			$query = 'INSERT INTO users (email, fname, lname) VALUES (:email, :fname, :lname)';
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':email', $user->email);
			$stmt->bindParam(':fname', $user->fname);
			$stmt->bindParam(':lname', $user->lname);
			return $stmt->execute();
		}catch(PDOException $e){
			die("Error creating user: " . $e->getMessage());
		}
	}
	public function updateUser(User $user): bool {
        // Implementation to update user details in the database
        try {
            $pdo = $this->connect();
            $query = 'UPDATE users SET fname = :fname, lname = :lname, role = :role, email = :email, 
                    password_hash = :password_hash, address = :address, post_code = :post_code, 
                    country = :country, isActive = :isActive, isVerified = :isVerified, 
                    reset_token = :reset_token, reset_token_expiry = :reset_token_expiry
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
            $stmt->bindParam(':isActive', $user->isActive, PDO::PARAM_BOOL);
            $stmt->bindParam(':isVerified', $user->isVerified, PDO::PARAM_BOOL);
            $stmt->bindParam(':id', $user->id, PDO::PARAM_INT); 
            $stmt->bindParam(':reset_token', $user->reset_token);
            $resetTokenExpiry = $user->reset_token_expiry ? $user->reset_token_expiry->format('Y-m-d H:i:s') : null;
            $stmt->bindParam(':reset_token_expiry', $resetTokenExpiry);
            return $stmt->execute();

        } catch (PDOException $e) {
            throw new \Exception("Error updating user: " . $e->getMessage());
        }
	}
}