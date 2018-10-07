# The backend for the web shop application (bfh)
[![Build Status](https://travis-ci.com/janfriedli/bfh-webshop-backend.svg?branch=master)](https://travis-ci.com/janfriedli/bfh-webshop-backend)

## Getting started

* clone this repo
* run `composer install`
* start the dev server `php bin/console server:run`

## API Documentation
You can find the API documentation at `/api/doc`.
The documentation for production: https://bfh-webshop-backend.herokuapp.com/api/doc

## CI/CD

The code is automatically tested by Travis Ci and will then (if successful) be deployed to Heroku.

## Testing

Run `phpunit` in the project root folder.