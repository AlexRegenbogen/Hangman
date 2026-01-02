# Hangman API #

In this assignment we ask you to implement a minimal version of a hangman API using the following resources below:

## Resources ##

**/api/games (POST)**

Start a new game

- A list of words can be found in the MySQL database. At the start of the game a random word should be picked from this list.

**/api/games/[:id] (PUT)**

Guess a started game

- Guessing a correct letter doesn’t decrement the amount of tries left

- Only valid characters are a-z

## Response ##

Every response should contain the following fields:

*word*: representation of the word that is being guessed. Should contain dots for letters that have not been guessed yet (e.g. aw.so..)

*tries_left*: the number of tries left to guess the word (starts at 11)

*status*: current status of the game (busy|fail|success)




## Installation ##
After cloning/downloading;
- run `composer install` to retrieve the required dependencies;
- create a .env file containing the database details (see .env.example)
- run `php artisan key:generate` - To generate a unique key for the application
- `php artisan migrate` - To install the database tables
- `php artisan db::seed` (only needed if using the word-generation from database option)
- `php artisan serve` (to allow access to both the api and the frontend on http://localhost:8000)
