<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Subscriber
{
	/**
     * @Assert\NotBlank()
     */
	private $id;
	/**
     * @Assert\NotBlank(
			message = "Name field cannot be empty"
     )
     */
    private $name;
    /**
     * @Assert\NotBlank(
			message = "Email field cannot be empty"
     ),
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     */
    private $email;
    /**
     * @Assert\Count(
     *      min = 1,
     *      minMessage = "You must select atleast one category",
     * )
     */
    private $categories = array();

    private $registration_date;

    public function getId(){
    	return $this->id;
    }

    public function setId($id){
    	$this->id = $id;
    }

    public function getName(){
    	return $this->name;
    }

    public function setName($name){
    	$this->name = $name;
    }

    public function getEmail(){
    	return $this->email;
    }

    public function setEmail($email){
    	$this->email = $email;
    }

    public function getCategories(){
    	return $this->categories;
    }

    public function setCategories($categories){
    	$this->categories = $categories;
    }

    public function getDate(){
    	return $this->registration_date;
    }

    public function setDate($date){
    	$this->registration_date = $date;
    }
    //Return all available categories
    public static function getAllCategories(){
    	return array(
    		1 => "Politics",
			2 => "Sports",
			3 => "Economy",
			4 => "Animals"
		);
    }
    //Adds a new subscriber to the subscribers.json
    public function addSubscriber($filepath){
    	$contents = file_get_contents($filepath);
		$subscribers = json_decode($contents, true);
		$subscriber = array(
			'id' => $this->getId(),
			'name' => $this->getName(),
			'email' => $this->getEmail(),
			'categories' => $this->getCategories(),
			'registration_date' => date('d-m-Y')
		);
		array_push($subscribers, $subscriber);
		file_put_contents($filepath, json_encode($subscribers));
    }
    //Gets all of the subscribers
    public function getSubscribers($filepath){
    	$contents = file_get_contents($filepath);
		return $subscribers = json_decode($contents, true);
    }
    //Finds and updates a subscriber
    public function updateSubscriber($filepath){
    	$contents = file_get_contents($filepath);
		$subscribers = json_decode($contents, true);
		$subscriber = array(
			'id' => $this->getId(),
			'name' => $this->getName(),
			'email' => $this->getEmail(),
			'categories' => $this->getCategories(),
			'registration_date' => $this->getDate()
		);
		foreach ($subscribers as $key => $sub) {
			if($sub['id'] == $subscriber['id']){
				$subscribers[$key] = $subscriber;
				file_put_contents($filepath, json_encode($subscribers));
				break;
			}
		}
    }
    //Finds and deletes a subscriber
    public function deleteSubscriber($filepath, $id){
    	$contents = file_get_contents($filepath);
		$subscribers = json_decode($contents, true);
		foreach ($subscribers as $key => $sub) {
			if($sub['id'] == $id){
				unset($subscribers[$key]);
				file_put_contents($filepath, json_encode($subscribers));
				break;
			}
		}
    }
}