## About Daybreak

Daybreak is a very simplistic timesheet and vacation planning program for small busnisses. It was created since I needed something I could host myself and integrate better into our local IT enviornment. Other open sourced programs like [smalltime](https://www.small.li/) or [kimai](https://www.kimai.org/) didn't work out for me, since they did not comply to C-55/18 EuGH or where not easily extendable.
This program is inspired and derived by [papershift](https://papershift.com). If you are looking for something more robust and/or more enterprise ready, I suggest you use their service. It has much more features like planning shifts.

## Installation Instructions

- copy .env.example to .env
- make sure [docker](https://docs.docker.com/get-docker/) is installed on your system
- run `composer install` in the root folder daybreak
- run `./vendor/bin sail up -d`
- install dependencies
  - for php dependencies  `./vendor/bin sail composer install`
  - for js dependencies
     - `./vendor/bin sail npm install`
     - `./vendor/bin sail npm run prod`
- run database migrations `./vendor/bin sail artisan migrate`
- goto [http://localhost](http://localhost) in your browser and register a new account

## Open Todos

- [] Add automatic pause times after "x" working hours
- [] Include holiday importer for other countries
- [] Make timezone of location configurable and make use of it
- [] Add more absence times calculators
- [] Add extended datatables and disable employee switcher

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Erik Porsche via [e.porsche@gmail.com](mailto:e.porsche@gmail.com). All security vulnerabilities will be promptly addressed.

## License

Copyright (c) Erik Porsche

Daybreak is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
