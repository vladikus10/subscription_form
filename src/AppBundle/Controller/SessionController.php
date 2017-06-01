<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session as GlobalSession;
use AppBundle\Entity\Session;

class SessionController extends Controller
{
	/**
	* @Route("/login", name="Login")
	*/
	public function indexAction(Request $request)
	{
		$session = new Session();
		$loginpath = $this->get('kernel')->getRootDir() . "/login.json";
		//If the user is already logged in, redirect them to the list
		if($session->tokenMatches($loginpath, $this->get('session')->get('token')) == true){
			return $this->redirectToRoute('Subscribers list');
		}
		if($request->isMethod('POST')){
			$session->setUsername($request->request->get('username'));
			$session->setPassword($request->request->get('password'));
			$validator = $this->get('validator');
    		$errors = $validator->validate($session);
			if(count($errors) > 0){
				$tmp = array();
				foreach ($errors as $error) {
					array_push($tmp, $error->getMessage());
				}
				return new Response(json_encode($tmp), 400);
			}
			else{
				$result = $session->login($loginpath);
				if($result != null){
					//Add token to the session
					$s = new GlobalSession();
					$s->set('token', $result);
				 	return new Response("Login successful", 200);
				}
				else{
					return new Response(json_encode(array('Invalid username or password')), 400);
				}
			}
		}

		return $this->render('subscribers/login.html.twig');
	}

	/**
	* @Route("/logout", name="Logout")
	*/
	public function destroyAction(Request $request)
	{
		$session = new Session();
		$loginpath = $this->get('kernel')->getRootDir() . "/login.json";
		$this->get('session')->clear();
		$session->logout($loginpath);
		return $this->redirectToRoute('Login');
	}
}
