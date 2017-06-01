<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Subscriber;
use AppBundle\Entity\Session;

class SubscribersController extends Controller
{

	/**
	* @Route("/", name="Subscription Form")
	*/
	public function indexAction(Request $request)
	{	
		//Create a new Subscriber entity
		$subscriber = new Subscriber();
		$filepath = $this->get('kernel')->getRootDir() . "/subscribers.json";
		//Get all available categories from entity
		$categories = $subscriber->getAllCategories();
		//Check if the request type is POST
		if ($request->isMethod('POST')) {
			//Build a new subscriber
			$subscriber->setId(uniqid());
			$subscriber->setName($request->request->get('name'));
			$subscriber->setEmail($request->request->get('email'));
			//Check if category arrays is null, if so, set an empty array for a correct validation
			if($request->request->get('category') == null){
				$subscriber->setCategories([]);
			}
			else{
				$subscriber->setCategories($request->request->get('category'));
			}
			$validator = $this->get('validator');
    		$errors = $validator->validate($subscriber);
    		//Check if there were any errors
			if(count($errors) > 0){
				$tmp = array();
				foreach ($errors as $error) {
					array_push($tmp, $error->getMessage());
				}
				return new Response(json_encode($tmp), 400);
			}
			else{
				//Saves the new subscriber to app/subscriber.json
				$subscriber->addSubscriber($filepath);
				return new Response("Subscription was saved", 200);
			}
			
    	}
		return $this->render('subscribers/new_subscriber.html.twig', array('categories' => $categories));
	}

	/**
	* @Route("/list", name="Subscribers list")
	*/
	public function listAction(Request $request)
	{
		$session = new Session();
		$loginpath = $this->get('kernel')->getRootDir() . "/login.json";
		//Check if user is logged in, if no, redirect them to the login page
		if($session->tokenMatches($loginpath, $this->get('session')->get('token')) == false){
			return $this->redirectToRoute('Login');
		}
		$subscriber = new Subscriber();
		$filepath = $this->get('kernel')->getRootDir() . "/subscribers.json";
		$categories = $subscriber->getAllCategories();
		//Gets the list of all subscribers
		$subscribers = $subscriber->getSubscribers($filepath);
		//Check if request method is PUT
		if($request->isMethod('PUT')){
			//return new Response($request->request->get('categories'));
			$subscriber->setId($request->request->get('id'));
			$subscriber->setName($request->request->get('name'));
			$subscriber->setEmail($request->request->get('email'));
			$subscriber->setCategories($request->request->get('categories'));
			$subscriber->setDate($request->request->get('registration_date'));
			$validator = $this->get('validator');
    		$errors = $validator->validate($subscriber);
			if(count($errors) > 0){
				$tmp = array();
				foreach ($errors as $error) {
					array_push($tmp, $error->getMessage());
				}
				return new Response(json_encode($tmp), 400);
			}
			else{
				$subscriber->updateSubscriber($filepath);
				return new Response("Updated", 200);

			}
		}
		//Checkif request method is DELETE
		if($request->isMethod('DELETE')){
			$id = $request->request->get('id');
			$subscriber->deleteSubscriber($filepath, $id);
			return new Response("Deleted", 200);
		}

		return $this->render('subscribers/subscribers.html.twig', array('subscribers' => $subscribers, 'categories' => $categories));
	}

	/**
	* @Route("/subscriber/{id}", name="Subscriber actions")
	*/
	public function subscriberAction(Request $request, $id)
	{
		$subscriber = new Subscriber();
		$filepath = $this->get('kernel')->getRootDir() . "/subscribers.json";

	}
}
