# 📚 Surat Bookcycle

![Language](https://img.shields.io/badge/language-PHP-blue.svg)
![Database](https://img.shields.io/badge/database-MySQL-orange.svg)
![Status](https://img.shields.io/badge/status-active-success.svg)

**Surat Bookcycle** is a web-based platform designed to facilitate the exchange, donation, and recycling of books in Surat. Built with **PHP and MySQL**, this application allows users to list old books and find new reads, promoting sustainability and education in the community.

---

## 🚀 Features

- **User System**: Secure user registration and login.
- **Book Listing**: Users can upload book details (Title, Author, Genre, Image).
- **Search Functionality**: Filter books by category or language.
- **Request System**: Users can request books from other members.
- **Admin Panel**: Manage users, approve listings, and view site analytics.
- **Responsive UI**: Works on mobile and desktop devices.

---

## 🛠️ Tech Stack

- **Backend**: PHP (Core)
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (Bootstrap/Tailwind)
- **Server**: Apache (via XAMPP/WAMP)

---

## ⚙️ Installation & Setup

Follow these steps to run the project on your local machine using **XAMPP**.

### 1. Prerequisites
- Install [XAMPP](https://www.apachefriends.org/index.html) (or WAMP/MAMP).
- Ensure **Apache** and **MySQL** modules are running in the XAMPP Control Panel.

### 2. Clone the Repository
Open your terminal/command prompt and navigate to the `htdocs` folder (usually inside `C:\xampp\htdocs`):

```bash
cd C:\xampp\htdocs
git clone [https://github.com/manthanvaghasiya/surat_bookcycle.git](https://github.com/manthanvaghasiya/surat_bookcycle.git)
3. Database ConfigurationOpen your browser and go to http://localhost/phpmyadmin.Create a new database named bookcycle_db (or check your connection file for the exact name).Click on Import.Choose the .sql file located in the project folder (e.g., database/bookcycle.sql) and click Go.4. Connect to DatabaseEnsure the database connection settings in your PHP code match your local setup. Check the config.php or db_connect.php file:PHP$servername = "localhost";
$username = "root";
$password = ""; // Default XAMPP password is empty
$dbname = "bookcycle_db";
5. Run the ProjectOpen your browser and visit:http://localhost/surat_bookcycle📸 ScreenshotsHome PageBook Details📂 Project Structuresurat_bookcycle/
├── assets/          # CSS, JS, and Images
├── config/          # Database connection files
├── database/        # SQL database export file
├── includes/        # Header, Footer, and reusable components
├── admin/           # Admin dashboard files
├── index.php        # Home page
├── login.php        # User login
└── README.md        # Project documentation
👤 AuthorManthan Vaghasiya GitHub: @[manthanvaghasiya](https://github.com/manthanvaghasiya) LinkedIn: 
