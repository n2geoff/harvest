# Harvest
> Timeless simplicity of a cURL-based HTTP client

## Description

Harvest is a lightweight, single class, PHP HTTP client library built on cURL, designed to simplify interacting with RESTful services or making any HTTP request in your applications. It supports GET, POST, PUT, and HEAD methods out of the box, allowing you to send requests with custom headers, cURL options, server URLs, and query parameters.

### Features

* Single Class
* ~200 Lines of Code
* No Dependencies (well, cURL)
* Implements methods for common HTTP verbs
* RESTful-Focused HTTP-Client

## Getting Started

### Installing

just grap from `src/harvest.php` and place it in your project.

> Composer installation coming soon

### Example

```
require_once "Harvest.php";

$harvest = new Harvest();
```

## TODO

- [ ] Setup on Composer
- [ ] Find a simple testing lib
- [ ] write tests
- [ ] write better documentation
- [ ] make even more versitle

## License

This project is licensed under the [MIT](LICENSE) License

## Acknowledgments

Mostly based on my days doing CodeIgniter development, and trying to build REST APIs which were not a great experience in PHP circa <em>2010's.</em> Way too much code/preamble to make simple HTTP requests.

* [Phil Sturgeon](https://philsturgeon.com/rest-implementation-for-codeigniter/)
* [PHP Curl](https://www.php.net/manual/en/book.curl.php)
