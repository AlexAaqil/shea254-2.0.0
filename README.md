# Shea254
Experts in Skin Care Products.



## Installation
1. Clone the Repository
    ```bash
    git clone git@github.com:AlexAaqil/shea254-2.0.0.git
    ```
2. Install the dependencies
    ```bash
    composer install && npm install
    ```
3. Set up the Environment file
    ```bash
    cp .env.example .env
    ```
4. Generate the application key variable for the .env file
    ```bash
    php artisan key:generate
    ```
5. Run the migrations
    ```bash
    php artisan migrate
    ```
6. Make sure GD driver is installed to be able to use image intervention library.
	- To install GD driver
    ```bash
    sudo apt-get install php-gd
    ```
    - To check if it's installed
    [/config/check-gd-driver-for-image-intervention](/config/check-gd-driver-for-image-intervention)



## Usage
1. Start the local development server
    ```bash
    php artisan serve
    ```
2. Open your browser and navigate to the following address
    ```
    http://localhost:8000
    ```



## Security Vulnerabilities
If you discover a security vulnerability within Laravel, please send an e-mail to Alex via [alexaaqil.se@gmail.com](mailto:alexaaqil.se@gmail.com). All security vulnerabilities will be promptly addressed.
