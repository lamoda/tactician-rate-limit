# Lamoda Tactician Rate Limit Middleware
[![Build Status](https://travis-ci.org/lamoda/tactician-rate-limit.svg?branch=master)](https://travis-ci.org/lamoda/tactician-rate-limit)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lamoda/tactician-rate-limit/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/lamoda/tactician-rate-limit/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/lamoda/tactician-rate-limit/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/lamoda/tactician-rate-limit/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/lamoda/tactician-rate-limit/badges/build.png?b=master)](https://scrutinizer-ci.com/g/lamoda/tactician-rate-limit/build-status/master)

## Installation

Usage is as simple as 

```bash
composer require lamoda/tactician-rate-limit
```

After that you need to choose library that is providing rate limiting capabilities.
For now there out of the box support for the following libraries:
* [davedevelopment/stiphle](https://github.com/davedevelopment/stiphle)

To use this library do the following:

1. Require `stiphle` via composer:
	```bash
	composer require davedevelopment/stiphle
	```
2. Configure middleware:
	```php
	$rateLimitProvider = new MatchingCommandClassRateLimitProvider(
		RateLimitedCommand::class,
		3, // max commands to execute per interval
		1000 // interval in milliseconds
	);
	
	$rateLimiter = new StiphleRateLimiterAdapter(new LeakyBucket());
	
	$rateLimitMiddleware = new RateLimitMiddleware(
		$rateLimitProvider,
		$rateLimiter
	);

	// Create command bus with this middleware:
	$commandBus = new CommandBus([
	 	$rateLimitMiddleware,
	 	// other middlewares
  	]);
	
	```

If you are using Symfony: use 
[lamoda/tactician-rate-limit-bundle](https://github.com/lamoda/tactician-rate-limit-bundle)