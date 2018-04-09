# laravel-phpmailer

Require: 
    `laravel ^5.5.*`
    `phpmailer ^6.0`

Config:
Update composer.json with   
    `"require": { "phpmailer/phpmailer": "^6.0" }` and run `composer update`

Route:
`Route::post('mail','MailController@mail');`

Extra:
Add your rout into `$except[]` in `App\Http\Middleware\VerifyCsrfToken.php` for use as API/Web service (for use of ajax)
`protected $except = ['mail'];`

Manage your own env configs for keep mail server server settings.