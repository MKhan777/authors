<?php

namespace App\Controller;

use App\Form\LoginFormType; // Include the LoginFormType class at the top
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\RequestStack;


class SecurityController extends AbstractController
{
    public function __construct(
        private RequestStack $requestStack,
    ){
        // Accessing the session in the constructor is *NOT* recommended, since
        // it might not be accessible yet or lead to unwanted side-effects
        // $this->session = $requestStack->getSession();
    }
    /**
     * @Route("/login", name="app_login")
     */
    public function login(Request $request, Security $security)
    {
        // Check if the user is already authenticated, then redirect to the home page.
        if ($security->getUser()) {
            return $this->redirectToRoute('app_homepage');
        }

        // Create a login form using the LoginFormType you generated earlier.
        $form = $this->createForm(LoginFormType::class);

        // Handle form submission.
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Retrieve email and password from the form.
            $formData = $form->getData();
            $email = $formData['email'];
            $password = $formData['password'];

            // Make an API request to get the access token.
            // You can use libraries like Guzzle or Symfony HttpClient for the API request.
            // Here's an example using Symfony HttpClient:
            $httpClient = HttpClient::create();
            $response = $httpClient->request('POST', 'https://candidate-testing.api.royal-apps.io/api/v2/token', [
                'json' => [
                    'email' => $email,
                    'password' => $password,
                ],
            ]);

            // Check if the API request was successful.
            if ($response->getStatusCode() === 200) {
                // Parse the API response JSON.
                $data = $response->toArray();

                // Store the access token in a secure way (e.g., Symfony's built-in security system).
                // For simplicity, you can store it in the session here.
                $session = $this->requestStack->getSession();
                $session->set('access_token', $data['token_key']);

                // Redirect to the homepage or any other route after successful login.
                return $this->redirectToRoute('list_authors');
            } else {
                // Handle API request error.
                $this->addFlash('error', 'Invalid credentials. Please try again.');
            }
        }

        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        // This controller is empty because Symfony handles the logout process automatically.
    }
}
