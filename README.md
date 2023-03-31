![Inventory Booking System Logo](https://user-images.githubusercontent.com/6664974/225930143-0f33b85c-e915-4a11-bbb9-e6f900cff473.png)

## About

Inventory Booking System is a web based bookings, loans and setups management system for IT departments.

- Keep a list of all bookable assets
- Book assets out between two time periods for a specified user
- Create setups for upcoming events for a specified user
- Log incidents to specified distribution groups along with the total cost of damage
- Automatic emails sent out daily for overdue assets
- Automatic emails sent out for upcoming setups starting soon

## Requirements
- Composer (https://getcomposer.org/)
- PHP >= 8.1 (https://windows.php.net/download/)
- SQL Database (e.g Mysql)
- Web Server (iis, apache etc)

## Installation Production

- (TODO) Download from releases
- Navigate to root folder and run `Composer Update`
- (TODO) Run file to setup task scheduler etc
- Point web Server to public folder
- Navigate to http://<websitename>/install and follow instructions

### IIS Setup

- In IIS, navigate to your site and open `Handler Mappings`
- Double click `php-8.x.x` then click `Request Restrictions...`
- On the Verbs tab, select `All Verbs`.
- When prompted with "Do you want to create a Fast CGI Application for this Executable", click Yes.