# EventSphere — Complete Online Event Management & Ticketing System

**BCA Semester 5 Academic Project Submission (Evaluator-Ready)**  
**Student Name:** Parmar Ankita Pankajbhai  
**Enrollment Number:** `24CS002UG01020`  
**Project Guide:** Prof. Aryan More  
**Course & Semester:** Bachelor of Computer Applications (BCA) — Semester 5  
**Project Domain:** Web Development / Relational Database Systems (RDBMS)

---

## 🌟 Key Highlights & Commercial Capabilities

1. **Company-Grade SaaS Aesthetic**: Crafted using a modern Indigo Violet (`#4F46E5`) and Cyan Teal (`#06B6D4`) design system on Slate background with glassmorphism navigation, floating card elevation, and smooth micro-interactions.
2. **ACID Concurrency Locking**: Prevents overbooking race conditions using MySQL `SELECT ... FOR UPDATE` row-level locks inside PDO database transactions.
3. **Printable E-Ticket Voucher**: Instant digital pass issuance with simulated barcode and entry QR code graphic, styled for clean one-click PDF printing (`@media print`).
4. **Self-Healing Database Installer**: Built-in auto-provisioning in `config/db.php` that automatically imports `database.sql` if the database is not yet created in phpMyAdmin.
5. **Full Admin Control Console**: Real-time KPI analytics (Active Events, Total Bookings, Ticket Revenue, Registered Users), Event CRUD with banner upload, Booking audit register with one-click refund/seat restoration, and Category taxonomy.

---

## 🛠️ Technology Stack

- **Frontend**: HTML5, Vanilla CSS3 (Custom Design System with CSS Grid & Flexbox), Vanilla JavaScript (ES6+), FontAwesome 6 CDN, Google Fonts (*Plus Jakarta Sans* & *Inter*).
- **Backend**: PHP 8.x (Modular Procedural / Clean PDO architecture with Prepared Statements).
- **Database**: MySQL / MariaDB (Normalized 3NF Relational Database).
- **Security**: Bcrypt password hashing (`PASSWORD_BCRYPT`), XSS sanitization, session security guards.

---

## 🚀 Quick Localhost Setup (XAMPP / WAMP)

### Step 1: Place Project Folder
Ensure this project folder is located inside your web root:
```
D:\XAMPP\htdocs\event management\
```
*(or `C:\xampp\htdocs\event management\`)*

### Step 2: Start Apache & MySQL
Open your **XAMPP Control Panel** and click **Start** for both **Apache** and **MySQL**.

### Step 3: Database Import
1. Navigate to [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/) in your browser.
2. Create a new database named `events_db` (Collation: `utf8mb4_unicode_ci`).
3. Click **Import**, select `database.sql` from this folder, and click **Import**.
*(Note: If you run the site before importing, `config/db.php` will automatically attempt to create and seed `events_db` for you!)*

### Step 4: Access Application
- **Public & Attendee Portal**: [http://localhost/event%20management/](http://localhost/event%20management/)
- **Admin Control Console**: [http://localhost/event%20management/admin/](http://localhost/event%20management/admin/)

---

## 🔑 Evaluator & Demo Accounts

| Role | Email | Password | Access Privileges |
| :--- | :--- | :--- | :--- |
| **Admin Officer** | `admin@eventsphere.com` | `password123` | Full CRUD over events, categories, and booking register |
| **Student (Attendee)** | `ankita@gmail.com` | `password123` | Book tickets, view ticket wallet, download e-ticket passes |

*(Quick-fill buttons are provided on the login page for effortless evaluation demonstrations).*

---

## 📂 Project Architecture

```
event-management/
├── assets/
│   ├── css/
│   │   ├── style.css           # Global design system & theme variables
│   │   ├── admin.css           # High-grade SaaS admin console styling
│   │   └── responsive.css      # Mobile breakpoints & @media print stylesheet
│   ├── js/
│   │   ├── main.js             # Public search, quantity counter & modal logic
│   │   └── admin.js            # Live table search, status filter & image preview
│   └── images/
│       ├── events/             # Uploaded & generated event banners
│       └── logo.svg            # Vector branding badge
│
├── config/
│   └── db.php                  # PDO connection with auto-provisioning fallback
│
├── includes/
│   ├── header.php              # Global navigation bar & session state
│   ├── footer.php              # Global footer + BCA Project Credits Modal
│   └── functions.php           # Sanitization, auth guards, formatters & flash
│
├── admin/                      # Secure Administrator Dashboard
│   ├── index.php               # Admin login portal
│   ├── dashboard.php           # KPI metrics (Events, Bookings, Revenue, Users)
│   ├── manage-events.php       # Event CRUD with image upload & capacity guard
│   ├── manage-bookings.php     # Booking audit register with seat restoration
│   ├── manage-categories.php   # Category taxonomy management
│   ├── manage-users.php        # Customer & admin account directory
│   ├── sidebar.php             # Reusable admin navigation shell
│   ├── footer.php              # Admin footer shell
│   └── logout.php              # Admin session termination
│
├── user/                       # Attendee Portal
│   ├── my-bookings.php         # User ticket wallet & cancellation engine
│   └── profile.php             # Account settings & password change
│
├── index.php                   # Landing page (Hero banner, search, highlights)
├── events.php                  # Catalog with keyword search, category & sorting
├── event-details.php           # Event showcase & live ticket booking widget
├── book-ticket.php             # Concurrency-safe ACID booking engine
├── ticket-success.php          # Printable E-Ticket voucher with barcode
├── login.php                   # User login with demo account auto-filler
├── register.php                # User registration with Bcrypt hashing
├── contact.php                 # Technical inquiries & FAQ section
├── logout.php                  # User session destruction
├── database.sql                # Complete schema & verified seed export
├── README.md                   # Installation & overview guide
└── PROJECT_REPORT.md           # Academic project report & viva defense guide
```
"# event-shpere" 
