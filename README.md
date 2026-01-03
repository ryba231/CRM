# CRM Project

## Requirements

This project is built with **Symfony** and requires the following environment:

- **PHP 8.2**
- **MySQL**
- **Composer**

## Technologies

- PHP 8.2  
- Symfony  
- MySQL  

## Installation

1. Clone the repository:
```
   git clone <repository-url>

```
2. Go to the project directory:
```
    cd <project-name>
```
3. Install dependencies using Composer:
```
    composer install
```
4. Configure the `.env` file (MySQL database connection).
5. Run database migrations (if applicable):
```
    php bin/console doctrine:migrations:migrate
```