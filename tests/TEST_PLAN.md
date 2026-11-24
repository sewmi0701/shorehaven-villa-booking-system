# Manual test plan (run on XAMPP/MySQL)

> **Do not mark tests as passed without actually running them.** This project was statically checked in an isolated environment where a live MySQL service was not available.

| ID | Case / steps | Expected result | Actual / Passed? |
|---|---|---|---|
| T01 | Open `/` on desktop and mobile | Navigation, images and booking search render responsively | To test |
| T02 | Import `db_structure.sql` to empty MySQL | Three linked tables, five rooms, two users, two bookings | To test |
| T03 | Register new guest; duplicate-email registration | Registration succeeds once; duplicate blocked; password hashed | To test |
| T04 | Log in and log out as guest | Session created; protected booking pages require login; logout ends session | To test |
| T05 | Search current/future valid dates and 2 guests | Rooms exclude overlapping **confirmed** reservations | To test |
| T06 | Enter check-out <= check-in or past check-in | Invalid dates rejected by JS and PHP | To test |
| T07 | Enter more guests than room capacity | Booking rejected by server | To test |
| T08 | Request available room for 3 nights | Pending booking saved; total equals nightly rate × 3 | To test |
| T09 | Guest attempts to cancel their own **pending** booking | Status becomes cancelled | To test |
| T10 | Guest attempts to cancel another guest's booking via forged ID | No booking updated | To test |
| T11 | Guest opens `/admin/index.php` | HTTP 403 (access denied) | To test |
| T12 | Admin creates, updates, deactivates and reactivates room | Room data changes and inactive room is hidden from visitor search | To test |
| T13 | Admin confirms a pending booking without conflicts | Status changes to confirmed | To test |
| T14 | Admin confirms another request overlapping same room/dates | Rejected; first confirmation preserved | To test |
| T15 | Admin cancels a confirmed booking | Booking cancelled; dates become available again | To test |
| T16 | Run site without JS; submit invalid POST | PHP rejects input; cannot trust browser calculations | To test |
| T17 | Inject HTML text in room description or registration name | Text escaped when displayed (not executed as HTML) | To test |
| T18 | Submit POST request without CSRF token | Request rejected with 419 | To test |
| T19 | Test at widths 375px, 768px, 1440px | Layout readable, responsive navigation usable | To test |

Demo credentials: admin@shorehaven.test / Admin@12345, demo@shorehaven.test / Guest@12345.

Potential follow-up checks: malicious or extremely long inputs, MySQL outages, concurrent confirmations from separate admin sessions, credential stuffing defenses (not included), accessibility keyboard navigation, and SQL query performance with 10k+ bookings.
