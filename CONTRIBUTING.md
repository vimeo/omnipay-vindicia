# Contributing Guidelines

* Fork the project.
* Make your feature addition or bug fix.
* Add tests for it. This is important so we don't break it in a future version unintentionally.
* Commit just the modifications, do not mess with the composer.json file.
* Ensure your code is nicely formatted in the [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
style and that all tests pass.
* Send the pull request.
* Check that the Travis CI build passed. If not, rinse and repeat. **Note:** This repo uses [Psalm](https://github.com/vimeo/psalm) to statically analyze all the code. Psalm runs on all the builds for PHP 5.5+. To install and run Psalm locally:

 ```
 export COMPOSER=composer-psalm.json
 php composer.phar install
 make psalm
 ```