<?php 
namespace App\Models;
class User {
    public $id;
    public $email;
    public $fname;
    public $lname;
    public $country;
    public $address;
    public $state;

    public function __construct($email = '', $fname = '', $lname = '', $country = '', $address = '', $state = '') {
        $this->email = $email;
        $this->fname = $fname;
        $this->lname = $lname;
        //$this->country = $country;
        //$this->address = $address;
        //$this->state = $state;
    }
}