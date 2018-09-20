# Download page

This is a small application to store and distribute files.

## Backend

* REST API with PHP micro framework [Slim](https://www.slimframework.com)The * * Locally a dummy archive is extracted to `app/download` after `make run` is called.

### Endpoints:
* GET /logout - Logout as user
* POST /login
	* Params: "user_name" and "user_password" (dev:dev on the local machine)
* GET /content - Get all available contents
* GET /download?file=[FILENAME] - Download a file

## Tools
* To run the application docker, docker-compose and make need to be installed
* To run the tests bats, jq, and curl need to be installed 
* To interconnect with your app you can use [Postman](https://www.getpostman.com/) or curl.

## Run and test the app locally

* Call `make run` to run the app locally with dummy data
* Call `make test` to test, whether the app works correctly with the dummy data 
* For more commands type `make help`

## Deploy the app 

TBD...