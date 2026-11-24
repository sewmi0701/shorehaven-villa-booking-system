# Shorehaven Villa — Coastal Villa & Room Booking System

**IT1208 Web Technologies · Continuous Assessment 2 (2026)**  
An original PHP/MySQL learning project for a fictional villa in **Mirissa, Sri Lanka**, intended to demonstrate the coursework requirements. This is a development reference, not a deployed hotel service. No payment gateway is needed.

## Features

| Guest / visitor | Administrator |
|---|---|
| Responsive home and rooms gallery | Secure role-restricted dashboard |
| Room search by dates, guest count, type, budget | Add, view and edit room records |
| Live availability preview (AJAX) | Activate / deactivate rooms (no history loss) |
| Registration, login and logout | View and filter bookings by status |
| Room details and JavaScript price estimate | Confirm or cancel bookings |
| Booking request with server-side pricing | Conflict checks prevent overlapping **confirmed** bookings |
| View only personal bookings and cancel **pending** requests | Confirmed booking totals and dashboard counts |

Other implementation details: three MySQL tables with foreign keys; SQL prepared statements (PDO); hashed passwords; secure PHP sessions; CSRF tokens; output escaping; client + server validation; room-level transaction locks for booking / confirmation decisions.

## Software requirements

- PHP **8.0 or newer** with **pdo_mysql** extension enabled.
- MySQL **8.x** or MariaDB (typically bundled with XAMPP).
- Apache with PHP support, or PHP development server (with MySQL running).
- Modern browser (Chrome, Firefox or Edge).

## Setup with XAMPP on Windows (recommended)

1. Download/install [XAMPP](https://www.apachefriends.org/), then start **Apache** and **MySQL** from the XAMPP Control Panel.
2. Unzip/copy the **`shorehaven-villa`** project folder to `C:\xampp\htdocs\shorehaven-villa`.
3. Open [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/), choose **Import**, select `C:\xampp\htdocs\shorehaven-villa\db_structure.sql`, then click **Import**. The script creates the database `shorehaven_booking` and adds fictional sample data.
4. Visit **[http://localhost/shorehaven-villa/](http://localhost/shorehaven-villa/)** in your browser. If Apache runs on port **8080** (as in the tested Windows installation), visit **[http://localhost:8080/shorehaven-villa/](http://localhost:8080/shorehaven-villa/)** instead. To open phpMyAdmin with this configuration use **http://localhost:8080/phpmyadmin/**.
5. Test registering a new user, searching rooms, making a booking, viewing My Bookings, then log in as admin to confirm a pending request.

### Database connection settings

The default connection is in `includes/bootstrap.php` (or you may set environment variables):

```text
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=shorehaven_booking
DB_USER=root
DB_PASS=             (empty, default XAMPP)
```

If your local MySQL `root` has a password, set `DB_PASS` in the bootstrap file or your PHP/Apache environment. For nonlocal installs, create a restricted database user. On some XAMPP releases `pdo_mysql` must be enabled in `php.ini` (restart Apache after changes).

**Alternative local PHP server:** Open a terminal in the extracted project folder and run `php -S 127.0.0.1:8000`. MySQL still needs to be running, and `pdo_mysql` must be available. Browse to http://127.0.0.1:8000/.

## Sample users (testing only)

| Role | Email | Password |
|---|---|---|
| Administrator | `admin@shorehaven.test` | `Admin@12345` |
| Demo guest | `demo@shorehaven.test` | `Guest@12345` |

**IMPORTANT:** These are deliberately public test accounts. Remove/change them before any real hosting. The system handles only *fictional* guests and accommodation during the assignment.

## Folder structure

```text
shorehaven-villa/
├── index.php, rooms.php, room.php, book.php, my-bookings.php
├── register.php, login.php, logout.php
├── admin/
│   ├── index.php, rooms.php, room-form.php, bookings.php
├── api/availability.php           # JSON endpoint
├── includes/bootstrap.php         # config and PDO connection
├── includes/functions.php         # auth, CSRF, availability helpers
├── includes/header.php, footer.php
├── assets/css/style.css
├── assets/js/main.js
├── assets/images/*.svg             # existing offline fallback illustrations
├── assets/images/photos/*.jpg     # optional downloaded Unsplash photos
├── db_structure.sql                # MySQL schema + seed data
├── preview.html                    # static front-end-only preview
├── project_report.pdf              # five-page report with actual local test screenshots
└── tests/TEST_PLAN.md
```

## How to use it

**Guest:** Register or log in → explore rooms → enter valid check-in/out dates and guest count → check the cost → submit request → see `Pending` in My Bookings → cancel while pending if desired. Bookings are not automatically confirmed.

**Admin:** Log in → dashboard → manage rooms → review reservations → confirm a pending request only when there are no overlapping confirmed reservations of the same room. The room remains blocked for new availability searches while confirmed. Cancelling a confirmed reservation releases its dates.

**Booking overlap rule:** Two date ranges overlap only when `existing.check_in < requested.check_out AND existing.check_out > requested.check_in`. Back-to-back bookings are allowed. Pending requests can coexist; a second overlapping request cannot be **confirmed**. Both booking and admin confirmation perform server-side checks and use a MySQL transaction / room-level lock.

## Testing

See [`tests/TEST_PLAN.md`](tests/TEST_PLAN.md) for manual test cases and expected results. **PHP syntax and JavaScript static checks** were performed during preparation. The student then supplied XAMPP browser screenshots for login, new-user registration, room search, booking, admin management and cancellations. Details and untested security cases are listed in `tests/TEST_RESULTS.md`. Re-test after changing the project; do not mark any pending test as passed without exercising it.

## Security, scope & limitations

- Passwords stored using `password_hash` / checked with `password_verify`.
- SQL statements use prepared parameters, and user-generated output is escaped with `htmlspecialchars`.
- POST forms include CSRF tokens; guest booking lookup filters by signed-in user ID.
- Administration checks the user's stored role; nonadmins cannot perform admin actions.
- Booking price and dates are recalculated/validated on PHP, rather than trusting JavaScript.
- This is a coursework demonstration: no payment, real mail, public guest reviews, production email verification, rate limiting, password reset or photo upload. Property information is fictional; updated room photographs are sample stock photos taken at other locations, not images of the fictional villa.
- For a production deployment, add HTTPS, stronger audit logging, login throttling, email verification, backups, and formal tests. Do not use known demo accounts in production.

## Academic integrity and generative AI acknowledgment

This starter/example implementation was prepared with assistance from **OpenAI ChatGPT** for initial scaffolding, interface styles and programming suggestions. **The submitting student must read, test, modify and be able to explain every component**, adapt the content and personal report, acknowledge any third-party dependencies/assets, and comply with the institution's AI-use policy. The villa illustrations are programmatically authored original SVG files. The updated room gallery uses real **Unsplash stock photography** (with sources/photographers credited in `PHOTO_CREDITS.md`); images are illustrative, not photos of the fictional property. Room photos require internet unless you run `DOWNLOAD_ROOM_PHOTOS.bat` to save them locally; offline SVG fallbacks remain available.

## Submission checklist

- [ ] Customize villa branding/details and verify all data and images.
- [ ] Run the app under XAMPP/MySQL and test against `tests/TEST_PLAN.md`.
- [x] Include authentic screenshots in the five-page PDF report.
- [ ] Fill student name/ID on the report cover and personally review the report text.
- [ ] Confirm the report is **at most 5 pages**.
- [ ] Verify SQL imports cleanly into an empty MySQL database.
- [ ] Explain ERD, transactions, SQL, sessions and code during demonstration.
- [ ] Submit source code, `db_structure.sql`, README and final PDF report through LMS/GitHub before **24 November 2026**.

## New: real room photo gallery

The room-listing, room detail, booking summary, My Bookings and admin room previews now show credited **genuine stock photographs** instead of SVG illustrations when online. Read [PHOTO_CREDITS.md](PHOTO_CREDITS.md) before using the pictures publicly. The existing booking database is not changed; **do not re-import SQL** when updating an existing installation. Optionally run [DOWNLOAD_ROOM_PHOTOS.bat](DOWNLOAD_ROOM_PHOTOS.bat) in Windows to download the images so they load offline. The original SVG illustrations remain as a fallback.

## Approved 2, 3, 5, 6 photo replacements

The original images for **The Ocean Suite** and **Sunset Balcony Suite** remain as previously chosen. Photos for **Palm Garden Deluxe**, **The Family Retreat**, **Coastal Comfort Room**, and **Ocean View Deluxe** now show the approved tropical cabana / resort or hotel exterior photographs. These are **representative Unsplash stock photos**, not photographs of a real Shorehaven Villa. See [PHOTO_CREDITS.md](PHOTO_CREDITS.md).

If upgrading a working Windows XAMPP installation, use [HOW_TO_UPDATE_PHOTOS.md](HOW_TO_UPDATE_PHOTOS.md) instead of importing SQL or re-setting up the app. The final project PDF contains real user screenshots captured before these specific image changes.
