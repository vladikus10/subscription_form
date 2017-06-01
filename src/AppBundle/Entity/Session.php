<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Session
{
	/**
     * @Assert\NotBlank(
			message = "Please enter a username."
     )
     */
	private $username;
	/**
     * @Assert\NotBlank(
			message = "Please enter a password"
     )
     */
    private $password;

    public function getUsername(){
    	return $this->username;
    }

    public function setUsername($username){
    	$this->username = $username;
    }

    public function getPassword(){
    	return $this->password;
    }

    public function setPassword($password){
    	$this->password = $password;
    }
    //Checks if the username and password matches the ones in the login.json, if yes, create and save the token and return it afterwards
    public function login($loginpath){
    	$contents = file_get_contents($loginpath);
		$user = json_decode($contents, true);
		$hashed = md5($this->getPassword());
		if($this->getUsername() == $user['username'] && $hashed == $user['password']){
			$token = md5(uniqid(rand(), true));
			$user['token'] = $token;
			file_put_contents($loginpath, json_encode($user));
			return $token;
		}
		else{
			return null;
		}
    }
    //Deletes the token
    public function logout($loginpath){
    	$contents = file_get_contents($loginpath);
		$user = json_decode($contents, true);
		$user['token'] = null;
		file_put_contents($loginpath, json_encode($user));
    }
    //Check if session token matches the one in the file
    public function tokenMatches($loginpath, $token){
    	$contents = file_get_contents($loginpath);
		$user = json_decode($contents, true);
		if($token == $user['token'] && $user['token'] != null){
			return true;
		}
		else{
			return false;
		}
    }
}

