<?php
namespace App\Controller;

use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

class AuthorController extends AbstractController
{
    public function __construct(private RequestStack $requestStack, )
    {
    }
    public function listAuthors(Request $request)
    {
        // Get pagination parameters from the request (e.g., page and limit).
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        // Make an API request to fetch paginated authors.
        $authors = $this->fetchPaginatedAuthors($page, $limit);
        // Render the template with authors and pagination data.
        $pagination = [
            'total_results' => $authors['total_results'],
            'total_pages' => $authors['total_pages'],
            'current_page' => $authors['current_page'],
            'limit' => $authors['limit'],
            'offset' => $authors['offset'],
        ];
        return $this->render('author/list.html.twig', [
            'authors' => $authors['items'],
            // List of authors from the API response
            'pagination' => $pagination,
        ]);
    }

    private function fetchPaginatedAuthors($page, $limit)
    {
        // Implement logic to make API request with pagination parameters.
        // You can use Symfony HttpClient or another HTTP client library here.

        // Example using Symfony HttpClient:
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'https://candidate-testing.api.royal-apps.io/api/v2/authors', [
            'query' => [
                'page' => $page,
                'limit' => $limit,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                // Use the stored access token
            ],
        ]);
        // Check if the API request was successful.
        if ($response->getStatusCode() === 200) {
            // Parse the API response JSON.
            $data = $response->toArray();

            // Return the paginated data.
            return [
                'items' => $data['items'],
                // List of authors
                'total_results' => $data['total_results'],
                'total_pages' => $data['total_pages'],
                'current_page' => $data['current_page'],
                'limit' => $data['limit'],
                'offset' => $data['offset'],
            ];
        } else {
            // Handle API request error.
            throw new \Exception('Failed to fetch paginated authors.');
        }
    }


    /**
     * @Route("/authors/{id}/view", name="view_author")
     */
    public function viewAuthor(int $id)
    {
        // Get the access token from the session or wherever you stored it.
        $session = $this->requestStack->getSession();
        $accessToken = $session->get('access_token');

        // Make an API request to fetch author details using the access token.
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'https://candidate-testing.api.royal-apps.io/api/v2/authors/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        // Check if the API request was successful.
        if ($response->getStatusCode() === 200) {
            // Parse the JSON response.
            $author = $response->toArray();
            // Render the author details template with the fetched data.
            // Determine whether the author is deletable based on the condition of having no books.
            $isAuthorDeletable = empty($author['books']);
            return $this->render('author/view.html.twig', [
                'author' => $author,
                'isAuthorDeletable' => $isAuthorDeletable, // Pass the variable to the template.
            ]);
        } else {
            // Handle API request error.
            $this->addFlash('error', 'Failed to fetch author details from the API.');
            $author = [];
            $isAuthorDeletable = false;
            return $this->redirectToRoute('author/list.html.twig'); // Redirect back to the list of authors.
        }
    }
    /**
     * @Route("/authors/{id}", name="delete_author", methods={"DELETE"})
     */
    public function deleteAuthor(Request $request, int $id)
    {
        // Get the access token from the session or wherever you stored it.
        $session = $this->requestStack->getSession();
        $accessToken = $session->get('access_token');

        // Make an API request to delete the author using the access token.
        $httpClient = HttpClient::create();
        $response = $httpClient->request('DELETE', 'https://candidate-testing.api.royal-apps.io/api/v2/authors/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        if ($response->getStatusCode() === 204) {
            // Author deleted successfully.
            $this->addFlash('success', 'Author deleted successfully.');
        } else {
            // Handle API request error.
            $this->addFlash('error', 'Failed to delete author.');
        }

        // Redirect back to the authors list or another appropriate page.
        return $this->redirectToRoute('list_authors');
    }

    // Helper function to get the stored access token (you can implement your own storage method).
    private function getAccessToken()
    {
        // Get the access token from the session or wherever you stored it.
        $session = $this->requestStack->getSession();
        return $session->get('access_token');
    }
}