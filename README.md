# Password Monkey

## Overview
Password Monkey is a simple and secure password manager built with pure PHP and MySQL. The app allows users to store, retrieve, and manage their passwords securely using encryption.

## Features
- User authentication (register & login)
- Secure password storage with AES encryption
- Add, edit, and delete passwords
- Copy passwords to clipboard
- Password visibility toggle
- Export stored passwords to CSV
- CSRF protection for form submissions

## Tech Stack
- **Backend:** PHP (Pure PHP, no framework for now)
- **Database:** MySQL
- **Frontend:** HTML, CSS (Bootstrap), JavaScript
- **Encryption:** AES-256-CBC

## Installation
### Prerequisites
- PHP 7.4 or later
- MySQL database
- Apache/Nginx server

### Steps
1. Clone the repository:
   ```sh
   git clone https://github.com/yourusername/password-monkey.git
   cd password-monkey
   ```
2. Configure the database:
   - Import `database.sql` into your MySQL server.
   - Update `config.php` with your database credentials.
3. Start the local server:
   ```sh
   php -S localhost:8000
   ```
4. Access the app at `http://localhost:8000`.

## Security Measures
- Passwords are encrypted using AES-256-CBC.
- CSRF protection is implemented in forms.
- Uses prepared statements to prevent SQL injection.

## Future Enhancements
- Laravel version (planned)
- Admin panel for user management
- Two-factor authentication (2FA)
- API integration for external password storage

## License
This project is open-source and available under the MIT License.

## Author
Developed by Olateju.

