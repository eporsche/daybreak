<p align="center"><img width="150px" src="/resources/logo_purple.svg" alt="Logo Daybreak"></p>
<p align="center">
    <a href="https://github.com/eporsche/daybreak/actions">
        <img src="https://github.com/eporsche/daybreak/workflows/tests/badge.svg" alt="Build Status">
    </a>
    <a href="https://packagist.org/packages/daybreak/daybreak">
        <img src="https://img.shields.io/packagist/dt/daybreak/daybreak" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/daybreak/daybreak">
        <img src="https://img.shields.io/packagist/v/daybreak/daybreak" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/daybreak/daybreak">
        <img src="https://img.shields.io/packagist/l/daybreak/daybreak" alt="License">
    </a>
</p>

## About Daybreak

Daybreak is a very simplistic timesheet and vacation planning program for small businesses. It was created because I needed something I could host myself and integrate better into our local IT enviornment. Other open sourced programs like [smalltime](https://www.small.li/) or [kimai](https://www.kimai.org/) didn't work out for me, since they did not comply to C-55/18 EuGH or where not easily extendable.
This program is inspired by papershift. If you are looking for something more robust and/or more enterprise ready, I suggest you test and use their service at [papershift.com](https://papershift.com).

## Installation Instructions

- make sure [docker](https://docs.docker.com/get-docker/) and [composer](https://getcomposer.org/download/) are installed on your system
- create a new daybreak project with `composer create daybreak/daybreak && cd daybreak`
- (optional) add `APP_PORT=YOUR CUSTOM WEBSERVER PORT` to the .env file to change the default webserver port
- run docker services with laravel sail `./vendor/bin/sail up -d`
- install js dependencies
     - `./vendor/bin/sail npm install`
     - `./vendor/bin/sail npm run prod`
- generate app key `./vendor/bin/sail artisan key:generate`
- run database migrations `./vendor/bin/sail artisan migrate`
- goto [http://localhost](http://localhost) in your browser and register a new account
- in the default installation the sending of mails is simulated with mailhog, you can view them via your browser here [http://localhost:8025/](http://localhost:8025/)

## Open Todos

- [ ] Documentation
- [ ] Add automatic pause times after "x" working hours
- [ ] Include holiday importer for other countries
- [ ] Make timezone of location configurable and make use of it
- [ ] Add more absence times calculators
- [ ] Add extended datatables and disable employee switcher
- [ ] More Tests

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Erik Porsche via [e.porsche@gmail.com](mailto:e.porsche@gmail.com). All security vulnerabilities will be promptly addressed.

## License

Copyright (c) Erik Porsche

Daybreak is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

Icons made by [Freepik](https://www.freepik.com) from [www.flaticon.com](https://www.flaticon.com/)
