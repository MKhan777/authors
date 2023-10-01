# Symfony Books | Authors Web Application

This is a simple Symfony-based web application for managing a bookstore. It allows users to create and manage books, view author details, and perform other bookstore-related tasks.

## Getting Started

Follow these steps to set up and run the Symfony application:

### Prerequisites

- PHP (8.1 or higher) installed on your system
- Symfony CLI 
### Installation

1. Clone the repository to your local machine:

   ```shell
   git clone <repository_url>
   git checkout authors <branch_name>

### About the code
Homepage (/):

Route Name: homepage
Controller: HomeController::index
Description: This route serves as the homepage of the application. It is associated with the index action in the HomeController, which displays user information if logged in.
Login Page (/login):

Route Name: app_login
Controller: SecurityController::login
Description: This route displays the login form. Users can log in to the application by providing their credentials. If already logged in, users are redirected to the homepage.
Logout (/logout):

Route Name: app_logout
Controller: SecurityController::logout
Description: This route allows users to log out from the application. Symfony handles the logout process automatically.
List Authors (/authors):

Route Name: list_authors
Controller: AuthorController::listAuthors
Description: This route lists authors in a paginated manner. Users can navigate through the list of authors and view their details.
View Author (/authors/{id}/view):

Route Name: view_author
Controller: AuthorController::viewAuthor
HTTP Method: GET
Description: This route displays detailed information about a specific author identified by {id}. Users can also check if the author is deletable (no associated books).
Delete Author (/authors/{id}):

Route Name: delete_author
Controller: AuthorController::deleteAuthor
HTTP Method: GET
Description: This route allows authenticated users to delete an author identified by {id}. Deletion is allowed only if the author has no associated books.
Delete Book (/books/{id}/delete/{authorId}):

Route Name: delete_book
Controller: BookController::deleteBook
HTTP Methods: GET, POST
Description: This route allows users to delete a book identified by {id} belonging to an author identified by {authorId}. Deletion can be performed via GET or POST requests.
Add Book (/books/add):

Route Name: add_book
Controller: BookController::createBook
HTTP Methods: GET, POST
Description: This route provides a form to add a new book to the application. Users can input book details like title, author, and publication date. The form submission is handled via GET and POST requests.
