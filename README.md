## Signal Assignment 

This plugin is scoffolded based on the wpint framework but the core
concepts are tried to be implemented framework agnostic, and only used subtle features of the framework.

## Installation 
- run `git checkout docker`
- run `docker compose up build --no-cache`
- run `docker compose up`

> If you had errors after plugin activation run these two command inside the container (plugin's root directory)
- run `mkdir bootstrap/cache`
- run `php artisan optimize` 

### What is implemented 
- This plugin is based on ONION architecture with the following Directories: 
- Domain: core of the business and it is implemented in pure php 
- Infra: All infrestructure we need to operate based on wordpress and other requirements (APIs, Audit logs, etc..) 
- Usecase: Application services


### What is not implemented 
#### Idempotency & Locking :
- Strategy: I would add another meta called version in the Signal post-type, 
also in the Domain Signal entity and then update the Singal's state only if its version is equal to the DB's Signal version


