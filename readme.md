# Give Well Community Donation Platform

This project is a community donation platform built with HTML, CSS (inline), JavaScript (with web3.js integration), PHP, MySQL, and Bootstrap. It uses a single PHP file as the entry point and a provided SQL dump for the database schema.

## Prerequisites

- **XAMPP** installed on your machine

## Steps to Host Locally

### 1. Set Up XAMPP

1. **Download XAMPP:**  
   Go to [Apache Friends](https://www.apachefriends.org/index.html)

2. **Install XAMPP:**  
   Follow the installation instructions provided on the website.

3. **Start XAMPP Control Panel:**  
   Launch the XAMPP Control Panel and start the **Apache** and **MySQL** services.

### 2. Set Up the Database

1. **Open phpMyAdmin:**  
   In your browser, navigate to [http://localhost/phpmyadmin](http://localhost/phpmyadmin).

2. **Create a Database:**  
   Create a new database (name it `givewell221`).

3. **Import the SQL Dump:**
   - Click on your new `givewell221` database.
   - Go to the **Import** tab.
   - Choose your `.sql` file
   - Click **Go** to import the schema and data.

### 3. Deploy the PHP Code

1. **Move the PHP File:**  
   Copy all php files into the XAMPP `htdocs` directory.  
   (On Windows, this is usually located at `C:\xampp\htdocs\`.)

### 4. Run the Site

1. **Open Your Browser:**  
   In your web browser, navigate to [http://localhost](http://localhost)
