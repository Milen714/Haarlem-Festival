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
		// TODO: Implement createUser method
		return false;
	}
}