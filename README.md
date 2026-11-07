# Restaurant Management System

A beginner-friendly, web-based restaurant management application for a **single restaurant**. The system is designed to manage food categories, menu items, customer records, orders, billing, invoice printing, and basic sales reports from one place.

> **Project status:** This README describes the documented project scope and proposed implementation. It does not imply that the application has already been developed or deployed.

## 1. Project Objectives

- Maintain food categories and menu items in a MySQL database.
- Save basic customer information when required.
- Create orders containing multiple food items and quantities.
- Calculate item subtotals and order totals automatically.
- Record the payment method selected at checkout.
- Generate printable invoices for completed orders.
- View order history and basic daily or monthly sales summaries.
- Provide separate access permissions for administrators and cashiers.

## 2. Main Features

| Module | Description |
|---|---|
| Login and User Management | Login, logout, session-based access, and administrator-managed cashier accounts. |
| Dashboard | Displays summary information such as today's sales, completed orders, food items, and customers. |
| Category Management | Add, view, edit, and delete food categories. |
| Food Management | Manage food names, categories, prices, descriptions, optional images, and availability. |
| Customer Management | Store customer names, phone numbers, and optional email addresses. Customer selection is optional for walk-in orders. |
| Order Management | Create orders with multiple food items and quantities. |
| Billing and Payment Recording | Calculate totals automatically and record a payment method such as Cash, Card, bKash, or Nagad. |
| Invoice | Display and print restaurant information, order number, date, ordered items, quantities, prices, and total amount. |
| Sales Reports | View order history and basic sales summaries by date or date range. |

## 3. User Roles

### Administrator

- Manage cashier accounts.
- Manage food categories and food items.
- Manage customer records.
- View orders and sales reports.

### Cashier

- Access the dashboard.
- Create customer records when needed.
- Create orders and select food items and quantities.
- Record the selected payment method.
- View order history and print invoices.

## 4. Technology Stack

- **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend:** Core PHP
- **Database:** MySQL
- **Local server environment:** Apache / XAMPP
- **Database administration:** phpMyAdmin

No chart library is required for the documented basic version. Reports use simple tables and totals.

## 5. System Requirements

- A computer running Windows, Linux, or macOS.
- XAMPP or another local environment that provides Apache, PHP, and MySQL.
- A modern web browser such as Chrome, Edge, or Firefox.
- A code editor such as Visual Studio Code.

## 6. Proposed Project Folder Structure

```text
restaurant_management/
|
|-- assets/
|   |-- css/
|   |   `-- style.css
|   |-- js/
|   |   `-- script.js
|   `-- images/
|
|-- config/
|   |-- db.php
|   `-- restaurant.php
|
|-- includes/
|   |-- auth.php
|   |-- header.php
|   |-- sidebar.php
|   `-- footer.php
|
|-- admin/
|   |-- dashboard.php
|   |-- users.php
|   |-- categories.php
|   |-- foods.php
|   |-- customers.php
|   |-- orders.php
|   `-- reports.php
|
|-- invoices/
|   `-- print.php
|
|-- index.php
|-- login.php
`-- logout.php
```

Folder names may be adjusted during development, but separating configuration, shared layout files, application modules, and static assets is recommended.

## 7. Database Design

**Database name:** `restaurant_db`

The documented design contains six related tables:

| Table | Purpose |
|---|---|
| `users` | Stores administrator and cashier accounts. |
| `categories` | Stores food categories. |
| `foods` | Stores menu items, their category, price, and availability. |
| `customers` | Stores optional customer contact information. |
| `orders` | Stores each sale, the cashier, optional customer, total, payment method, and order status. |
| `order_items` | Stores the individual food items and quantities belonging to an order. |

### Database Relationships

- One category can contain many foods.
- One customer can have many orders. The customer reference can be empty for a walk-in sale.
- One user can process many orders.
- One order can contain many order items.
- One food item can appear in many order items.

Restaurant name, phone number, and address are stored in `config/restaurant.php` and displayed on printed invoices; they are not stored in a separate database table in this version.

## 8. Local Setup Guide

The following steps describe the intended local development setup. The database schema and application source files must be available in the project before the application can run.

1. Install XAMPP or an equivalent Apache, PHP, and MySQL environment.
2. Copy the project folder into the web server's document root (for XAMPP, this is normally the `htdocs` folder).
3. Start **Apache** and **MySQL** from the XAMPP Control Panel.
4. Open phpMyAdmin in a browser and create a database named `restaurant_db`.
5. Create the six tables and their primary-key and foreign-key relationships according to the Database Design section of the project documentation.
6. Set the local database host, database name, username, and password in `config/db.php`.
7. Enter the restaurant name, phone number, and address in `config/restaurant.php`.
8. Open the project through the local web server and use the login page.

**Login note:** The project documentation does not define default login credentials. Create the initial administrator account securely as part of implementation; do not rely on an undocumented default username or password.

## 9. Order and Billing Workflow

1. An administrator or cashier logs in with an active account.
2. The administrator maintains categories and available food items.
3. The cashier optionally selects an existing customer or creates a customer record.
4. The cashier starts an order and selects one or more available food items.
5. The cashier enters a positive quantity for each item.
6. The system calculates each item subtotal and the order total.
7. The cashier selects and records the payment method.
8. The system saves the order and its order items, then displays the invoice.
9. The cashier prints the invoice. The order is then available in order history and sales reports.

### Billing Formula

```text
Item Subtotal = Quantity × Unit Price
Order Total   = Sum of all item subtotals
```

The unit price used for an order should be saved in the corresponding order item. This preserves the original sale price if the menu price changes later.

## 10. Important Business Rules

- Only logged-in users can access protected pages.
- User actions must be restricted according to the assigned role.
- Only available food items can be added to a new order.
- Quantity must be a positive whole number.
- The server must read prices from the database and recalculate totals; it must not trust totals submitted by the browser.
- A completed order must contain at least one order item.
- A walk-in order may have no customer record.
- Cancelled orders must not be counted as completed sales.
- Order and order-item records should be saved together using a database transaction.

## 11. Security and Validation

- Store passwords using PHP `password_hash()` and verify them using `password_verify()`.
- Never store plain-text passwords.
- Use prepared statements (PDO or MySQLi) for database queries that include user input.
- Validate submitted data on the server.
- Check sessions and user roles on protected pages.
- Validate uploaded food images by file type and size, and use safe file names.
- Back up the MySQL database regularly during testing and before deployment.

## 12. Basic Reports

- Today's sales total.
- Number of completed orders for a selected date.
- Sales summary by date range or month.
- Order-wise details, including order number, date, cashier, payment method, and total amount.

The basic version uses simple tables and totals. Interactive charts, forecasting, and advanced analytics are outside the project scope.

## 13. Testing Checklist

- [ ] Verify login with valid and invalid credentials.
- [ ] Verify that a cashier cannot access administrator-only pages.
- [ ] Add, edit, view, and delete a category.
- [ ] Add and edit food items and verify category links and availability.
- [ ] Create and update customer records.
- [ ] Create an order with multiple food items and quantities.
- [ ] Verify every item subtotal and the final order total.
- [ ] Confirm that an order and all its order items are saved together.
- [ ] Open and print an invoice and compare it with the saved order.
- [ ] Verify that the sales report excludes cancelled orders.

## 14. Development Plan

1. Create the project folders and MySQL database.
2. Build the database connection, login/logout, and Bootstrap 5 dashboard.
3. Develop category, food, and customer management modules.
4. Develop order creation, order items, and bill calculation.
5. Develop invoice printing, order history, and basic sales reports.
6. Test all modules, fix errors, and prepare the final demonstration.

## 15. Features Outside the Current Scope

The following features are deliberately excluded to keep the project suitable for beginner-level development and academic submission:

- Kitchen order display and preparation status.
- Table management and table reservations.
- Ingredient-level inventory and low-stock notifications.
- Supplier and purchase management.
- Employee attendance and payroll.
- Online customer ordering and delivery tracking.
- Online payment gateway integration.
- Multi-branch or multi-restaurant support.
- Mobile applications and advanced chart-based analytics.

These can be considered as future enhancements after the basic system is completed and tested.

## 16. Suggested Project Demonstration

For the final presentation, demonstrate the application in this order:

1. Log in as administrator.
2. Add a food category.
3. Add a food item.
4. Log in as cashier.
5. Create a customer record.
6. Place an order with two or more food items.
7. Show the automatically calculated total.
8. Select a payment method and save the order.
9. Print the invoice.
10. Open the sales report.

## 17. Project Deliverables

- A PHP and MySQL web application for one restaurant.
- A MySQL database named `restaurant_db` with six related tables.
- Administrator and cashier login with role-based page access.
- Food category, food item, and customer management pages.
- Order creation, automatic bill calculation, payment method recording, and invoice printing.
- Order history and basic sales reports.
- Project documentation and a demonstration of the completed application.

## 18. Project Scope Summary

The Restaurant Management System focuses on the essential daily workflow of a single restaurant: maintaining the menu, recording customers when needed, creating orders, calculating bills, recording payment methods, printing invoices, and reviewing basic sales information. The limited scope is intended to make the project achievable for a beginner while demonstrating practical PHP and MySQL skills.
