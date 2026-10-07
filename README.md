ColdChain Guard

📌 Project Overview

ColdChain Guard is a web-based Cold Chain Monitoring System designed to monitor the transportation of temperature-sensitive products such as vaccines, medicines, and food items.

The system helps users track shipments, monitor temperature conditions, view locations, and identify temperature alerts.

---

 🎯 Objectives

- Monitor cold-chain shipments.
- Track shipment status and locations.
- Record and display temperature readings.
- Detect temperature excursions.
- Store shipment information in a database.
- Provide a simple dashboard for monitoring.

---

 🖥️ Project Stages

Stage 1 – Static Website

The first stage focuses on designing the user interface using:

- HTML
- CSS
- JavaScript

The static website provides the dashboard interface, shipment information, temperature cards, alerts, recent activity, and navigation.

At this stage, the data displayed on the pages is predefined/static.

 Stage 2 – Dynamic Website

The second stage converts the website into a dynamic web application using:

- PHP
- MySQL
- XAMPP
- phpMyAdmin

The application is connected to a MySQL database.

Users can add and manage shipment information, while the application retrieves data from the database dynamically.

 ⚙️ Main Features

- Dashboard
- Shipment Management
- Shipment Status Tracking
- Temperature Monitoring
- Temperature Alerts
- Location/Checkpoint Tracking
- MySQL Database Integration
- Dynamic PHP Pages
- Responsive User Interface

🗄️ Database

Database Name:

`coldchain_guard`

Main tables:

- `users`
- `shipments`
- `temperature_readings`
- `locations`
- `incidents`

The database is created and managed using phpMyAdmin.

⚙️ Technologies Used

| Technology | Purpose |
|---|---|
| HTML | Page structure |
| CSS | User interface design |
| JavaScript | Client-side interaction |
| PHP | Server-side processing |
| MySQL | Database management |
| Apache | Local web server |
| phpMyAdmin | Database administration |
| Git & GitHub | Version control |


 📁 Project Structure

```text
coldchain_guard/
│
├── index.php
├── shipments.php
├── add_shipment.php
├── db.php
├── test_db.php
├── script.js
├── style.css
└── README.md