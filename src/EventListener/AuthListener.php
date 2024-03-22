<?php
	// src/EventListener/AuthListener.php

	namespace App\EventListener;

	use Symfony\Component\HttpKernel\Event\RequestEvent;
	use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
	use Symfony\Component\HttpKernel\KernelEvents;
	use Symfony\Component\EventDispatcher\EventSubscriberInterface;
	use Symfony\Component\HttpFoundation\Request;

	class AuthListener implements EventSubscriberInterface{
		
		public function onKernelRequest(RequestEvent $event){
			$request = $event->getRequest();
			if ($request->isMethod(Request::METHOD_GET)) {
				$apiKey = $request->query->get('apikey');
			} else {
				$apiKey = $request->request->get('apikey');
			}

			if (empty($apiKey)) {
				throw new AccessDeniedHttpException('Missing API key');
			}

			if (!$this->isValidApiKey($apiKey)) {
				throw new AccessDeniedHttpException('Invalid API key');
			}
		}

		private function isValidApiKey(string $apiKey): bool{
			if ($apiKey === $_ENV['API_KEY']) {
				return true;
			}
			else{
				return false;
			}
		}

		public static function getSubscribedEvents(){
			return [
				KernelEvents::REQUEST => 'onKernelRequest',
			];
		}
	}
