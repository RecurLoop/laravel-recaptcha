# Laravel Recaptcha

Laravel package for Recaptcha V3

The main motivation for this project was the fact that I could not find an existing package for recaptcha v3 that met two conditions:
- possible reading of scores for requests (without rejecting if the score is too low)
- the recaptcha token should be generated just before sending the request (and not when generating the page), which may result in an expired token.

## Installation


To get started, use Composer to add the package to your project's dependencies:

    composer require recurloop/laravel-recaptcha:dev-main


Add `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY` to your `.env` file. (You can get them [here](https://www.google.com/recaptcha/admin#list))

```
RECAPTCHA_SITE_KEY=sitekey
RECAPTCHA_SECRET_KEY=secret
```

Optionally, you can publish the config file:
```
php artisan vendor:publish --provider="RecurLoop\Recaptcha\Providers\RecaptchaServiceProvider"
```

## Usage

#### Init Recaptcha

recaptcha v3 should be loaded on every page to track user activity:
```html
<head>
    ...
    {!! Recaptcha::initJs() !!}
</head>
```

#### Forms
a recaptcha token should be sent with each form along with the name of the action performed:
```html
<form method="post" action="/contact">
    <button onclick="{!! Recaptcha::onClickSubmitJs('contact') !!}">Send message</button>
</form>
```
or
```html
<form method="post" action="/contact" onsubmit="{!! Recaptcha::addTokenJs('contact') !!}">
    <button type="submit">Send message</button>
</form>
```

#### Request validation

Register the middleware in your Kernel.php file.
```php
protected $routeMiddleware = [
    ...
    'recaptcha' => \App\Http\Middleware\VerifyRecaptchaScore::class,
    ...
];
```

You can then reject requests if they do not reach the minimum specified in the config.
```php
Route::post('contact', fn() => null)->middleware('recaptcha:contact');
```
or with given minimum
```php
Route::post('contact', fn() => null)->middleware('recaptcha:contact,0.7');
```

#### Check the score

Alternatively, you can check if the score has been reached directly in the code:
```php
use RecurLoop\Recaptcha\Exceptions\InvalidTokenException;
use RecurLoop\Recaptcha\Facades\Recaptcha;

try {
    // Regardless of the action
    Recaptcha::checkScore(request());
    // Including a token (the token will be invalid if the action does not match)
    Recaptcha::checkScore(request(), 'contact');
    // Taking into account the action and the minimum score
    Recaptcha::checkScore(request(), 'contact', 0.75);
} catch (InvalidTokenException $exception) {
    ...
}
```

#### Retrieve the score

Alternatively, you can retrieve the score
```php
use RecurLoop\Recaptcha\Exceptions\InvalidTokenException;
use RecurLoop\Recaptcha\Facades\Recaptcha;

try {
    // Regardless of the action
    $score = Recaptcha::retrieveScore(request());
    // Including a token (the token will be invalid if the action does not match)
    $score = Recaptcha::retrieveScore(request(), 'contact');
} catch (InvalidTokenException $exception) {
    ...
}
```


#### Hiding the ReCAPTCHA Badge

Add to your CSS file:
```css
.grecaptcha-badge { visibility: hidden !important; }
```

#### Localization
By default, the package follows the default application locale, which is defined in `config/app.php`. If you want to change this behavior, you can specify what locale to use by adding a new environment variable :
```
RECAPTCHA_LOCALE=en
```
