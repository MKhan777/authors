<?php
namespace App\Controller;

use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Form\BookType;

class BookController extends AbstractController
{

    public function __construct(private RequestStack $requestStack, )
    {
    }
    public function createBook(Request $request)
    {
        // Fetch authors from the API and populate the choices for the author dropdown.
        $authors = $this->fetchAuthors();
        // Create the form with the injected author choices
        $form = $this->createForm(BookType::class, null, [
            'author_choices' => $authors,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle form submission and make a POST request to the API to create a new book.
            $formData = $form->getData();
            $releaseDateFormatted = $formData['release_date']->format('Y-m-d\TH:i:s.u\Z');
            // Make a POST request to the API to create the book
            $bookData = [
                'author' => ['id' => $formData['author']],
                'title' => $formData['title'],
                'release_date' => $releaseDateFormatted,
                'description' => $formData['description'],
                'isbn' => $formData['isbn'],
                'format' => $formData['format'],
                'number_of_pages' => $formData['number_of_pages'],
            ];
           

            // Make the API POST request here
            $httpClient = HttpClient::create();
            $response = $httpClient->request('POST', 'https://candidate-testing.api.royal-apps.io/api/v2/books', [
                'json' => $bookData,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getAccessToken(),
                    'Content-Type' => 'application/json',
                ],
            ]);
            if ($response->getStatusCode() === 200) {
                // Book created successfully.
                $this->addFlash('success', 'Book added successfully');
                return $this->redirectToRoute('add_book');

            } else {
                // Handle API request error.
                $this->addFlash('error', 'Failed to create a book.');
            }
        }

        return $this->render('author/book.html.twig', [
            'form' => $form->createView(),
            'authors' => $authors,
        ]);
    }
    // src/Controller/BookController.php

    // src/Controller/BookController.php

    private function fetchAuthors(): array
    {
        $authorChoices = [];

        $page = 1;
        $limit = 100; // Adjust the limit per page as needed

        do {
            // Make an API request to fetch authors for the current page.
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
                $authorsData = $response->toArray();

                // Iterate through authors on this page and add them to the choices.
                foreach ($authorsData['items'] as $author) {
                    $authorId = $author['id'];
                    $authorName = $author['first_name'] . ' ' . $author['last_name'];
                    $authorChoices[$authorId] = $authorName;
                }

                // Increment the page number for the next request.
                $page++;
            } else {
                // Handle API request error.
                $this->addFlash('error', 'Failed to fetch authors from the API.');
                break; // Exit the loop in case of an error
            }
        } while ($page <= $authorsData['total_pages']);

        return $authorChoices;
    }


    /**
     * @Route("/books/{id}/delete", name="delete_book", methods={"POST"})
     */
    public function deleteBook(int $id, int $authorId)
    {
        // Make an API request to delete the book using the access token.
        $httpClient = HttpClient::create();
        $response = $httpClient->request('DELETE', 'https://candidate-testing.api.royal-apps.io/api/v2/books/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
            ],
        ]);

        if ($response->getStatusCode() === 204) {
            // Book deleted successfully.
            $this->addFlash('success', 'Book deleted successfully.');
        } else {
            // Handle API request error.
            $this->addFlash('error', 'Failed to delete book.');
        }

        // Redirect back to the author's single view or another appropriate page.
        return $this->redirectToRoute('view_author', ['id' => $authorId]);
    }
    private function getAccessToken()
    {
        // Get the access token from the session or wherever you stored it.
        $session = $this->requestStack->getSession();
        return $session->get('access_token');
    }
}