# ppl_challenge

---
- author: Oscar Hernandez
---

## Documentation
[API Documentation](https://documenter.getpostman.com/view/19210562/2s7YYpfRSk)


## Prerequisites
In order to run this container you'll need docker installed
* [Windows](https://docs.docker.com/get-started/)
* [OS X](https://docs.docker.com/get-started/)
* [Linux](https://docs.docker.com/get-started/)

## Installation
```shell
$ git clone https://github.com/oschdez97/ppl_challenge.git
$ cd ppl_challenge
$ docker compose build
$ docker-compose up -d
$ docker-compose ps
$ curl -i http://localhost:8080/
```

## Connecting to Mysql
```shell
$ docker-compose exec mysql bash
$ mysql -u root -p
```

## Connecting to PHP
```shell
$ docker-compose exec web bash
```

## Shut down
```shell
$ docker-compose down
```

## Challenge API Base Url: 
`http://127.0.0.1:8080/src/challenge`


## API endpoints
 - `/create_user.php` (Create a new user)
 - `/users.php` (Get the list of registrated users)
 - `/contact.php` ([Get/Add/Update] user contacts)
 - `/common_contacts` (Get list of common contacts between two registrated users)

## Run Tests
[Connect to PHP](#connecting-to-php)
```shell
$ cd /src/challenge
$ phpunit tests/
```

# Note:
> The recommended API for validating phone numbers https://neutrinoapi.net/phone-validate reaches: **DAILY API LIMIT EXCEEDED** Very fast so keep in mind that phone validations can be affected