
# My Wallet - Financial Transaction App

Link : https://finance.abysoft.my.id

A professional, mobile-first financial management web application built with **CodeIgniter 3**. This app features a unique **Neubrutalism UI** design, characterized by vibrant colors, thick borders, and heavy shadows.

## ✨ Features

- **Auth System**: Secure login and registration for multiple users.
- **Transaction Tracking**: Easily record Income and Expenses.
- **Dynamic Categories**: Manage categories directly from the database for flexibility.
- **Transaction Details**: In-depth view for every record with a clear summary.
- **Profile Management**: Customize display name and update password.
- **Mobile-First Design**: Optimized for a 480px width mobile view, even on desktop browsers.
- **Neubrutalism UI**: A bold, modern aesthetic using pastel colors and high-contrast elements.

## 🚀 Tech Stack

- **Backend**: PHP 7.4+ (CodeIgniter 3 Framework)
- **Database**: MySQL
- **Frontend**: 
  - Standard HTML5 & CSS3 (Custom Neubrutalism Design)
  - Bootstrap 5 (Layout Utilities)
  - [Iconify](https://iconify.design/) (Lucide Icons)
  - [Google Fonts](https://fonts.google.com/) (Space Grotesk & Space Mono)

## 🛠️ Installation

1. **Clone the repository**:
   ```bash
   git clone <repository-url>
   ```
2. **Setup Database**:
   - Create a database named `finance_app`.
   - Import `finance_app.sql` into your MySQL server.
3. **Configure Database**:
   - Open `application/config/database.php` and update your database credentials:
     ```php
     'hostname' => 'localhost',
     'username' => 'root',
     'password' => '',
     'database' => 'finance_app',
     ```
4. **Configure Base URL**:
   - Open `application/config/config.php` and set your base URL:
     ```php
     $config['base_url'] = 'http://localhost/finance_app/';
     ```
5. **Run the App**:
   - Place the project in your local server directory (e.g., Laragon's `www` or XAMPP's `htdocs`).
   - Access the app via browser.

## 📸 Design Style
The app follows the **Neubrutalism** design philosophy:
- **Max Width**: 480px (Centered on desktop).
- **Colors**: Pastel Blue, Green, Red, and Yellow.
- **Borders**: 2px Solid Black.
- **Shadows**: 4px/8px Solid Black (Brutal Shadows).

---
Developed as a lightweight and fast solution for personal finance tracking.
