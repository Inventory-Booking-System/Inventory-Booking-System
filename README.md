<a name="readme-top"></a>

<!-- PROJECT LOGO -->
<br />
<div align="center">
  <a href="https://github.com/othneildrew/Best-README-Template">
    <img src="https://github.com/Dragnogd/Inventory-Booking-System/assets/6664974/f351af2c-66e5-41dc-9ea9-aa4bae1926fa" alt="Logo" width="600" height="250">
  </a>

  <h3 align="center">Inventory Booking System</h3>

  <p align="center">
    A web based bookings, loans and setups management system for IT departments.
    <br />
    <a href="https://github.com/Inventory-Booking-System/Inventory-Booking-System/wiki"><strong>Explore the docs »</strong></a>
    <br />
    <br />
    </a>
  </p>
</div>



<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#license">License</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## About The Project

Inventory Booking System is a web based bookings, loans and setups management system.

Here's why:
* Keep a list of all bookable assets
* Book reserve assets on behalf of users
* Track upcoming setups and any equipment required
* Log incidents that happen to equipment
* Email notifications for overdue bookings & upcoming setups

<p align="right">(<a href="#readme-top">back to top</a>)</p>


<!-- GETTING STARTED -->
## Getting Started

### Docker

The Docker image is the simplest way to get started. The image comes with a self-signed SSL certificate.

First create config and storage directories on the host to persist data. Then run:

<pre><code>docker run -d --name booking -v <mark>/local/config</mark>:/etc/inventory-booking-system/config -v <mark>/local/storage</mark>:/var/www/html/storage -p 443:443 angusmcd/inventory-booking-system:latest</code></pre>

The server will now be available at [https://localhost](https://localhost).

### Manual Install

#### Prerequisites

Below are the recommended prerequisites to install the Inventory Booking System. Note similar tools such as different database engines (e.g MariaDB) are supported by the Laravel Framework although have not been tested.

* [Microsoft IIS](https://www.iis.net/) or Apache
* [PHP](https://windows.php.net/) >= 8.1
* [MySQL](https://www.mysql.com/) >= 8

#### Installation

[Click here for a step by step guide on installing using IIS](https://github.com/Dragnogd/Inventory-Booking-System/wiki/Setup-with-IIS)

Below is a basic overview of how to install the inventory booking.

1. [Download](https://github.com/Dragnogd/Inventory-Booking-System/releases/latest) the latest release
2. Place the website into your webserver
4. Navigate to the http://localhost/install (replacing localhost as appropriate)
5. Setup scheduled as detailed [here](https://github.com/Inventory-Booking-System/Inventory-Booking-System/wiki/Create-Scheduled-Tasks)


<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- USAGE EXAMPLES -->
## Usage

### [Loans](https://github.com/Inventory-Booking-System/Inventory-Booking-System/wiki/Loans) 

* Loans are a list of bookable assets between two time periods, either over part of the day or multiple days. Loans can be booked in advance or done when equipment is collected.

* Emails are sent to users upon creation of a loan as well as on return and when the loan is overdue.

* Assets in loans can be partially booked back in

### [Setups](https://github.com/Inventory-Booking-System/Inventory-Booking-System/wiki/Setups)

* Setups have a similar to loans but will also notify the user when a setup is upcoming in the next 30 minutes. This is useful for departments which have to do regular setups such as setting up av equipment.

### [Incidents](https://github.com/Inventory-Booking-System/Inventory-Booking-System/wiki/Incidents)

* The incident module is used to keep track of any damage done to equipment. You can list different types of equipment and assign cost to them.

* You can setup distribution groups which allows you to email a certain set of users when an incident occurs.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- LICENSE -->
## License

Distributed under the MIT License.

<p align="right">(<a href="#readme-top">back to top</a>)</p>
