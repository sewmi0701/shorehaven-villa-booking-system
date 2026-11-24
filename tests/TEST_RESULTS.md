# Manual test evidence (local XAMPP, 23-24 November 2026)

Evidence: screenshots supplied from `http://localhost:8080/shorehaven-villa/`. Captures are in `report/screenshots/`. Screenshot file names are descriptive; these are real browser captures, not design previews.

| Test | Observation | Status |
|---|---|---|
| MySQL schema import | 3 tables: users, rooms, bookings; seed rows visible in phpMyAdmin | PASS (observed) |
| Demo guest sign-in | Redirected to My Bookings as Demo Guest | PASS (observed) |
| New guest registration | Separate new guest account logged in with an empty bookings list | PASS (observed) |
| New booking | Booking #3 for Coastal Comfort Room (26-27 Nov, 2 guests) created as Pending, LKR 12,500 | PASS (observed) |
| Admin approval | Booking #3 changed to Confirmed | PASS (observed) |
| Guest cancellation | Booking #2 changed from Pending to Cancelled | PASS (observed) |
| Room add and edit | Ocean View Deluxe visible; nightly rate updated from LKR 20,000 to LKR 21,999.99 | PASS (observed) |
| Room deactivation/reactivation | User reported clicking each control; confirmation dialog screenshot supplied | PASS (user reported; no final state screenshot) |
| Availability search | Search 26-27 Nov for two guests returned 4 rooms and excluded booked Coastal Comfort Room | PASS (observed) |
| Second overlapping admin confirmation | No attempted second confirmation screenshot | NOT TESTED |
| CSRF, XSS, role denial, duplicate-email check, invalid date ranges, no-JS validation | No direct observed test | NOT TESTED |
| Mobile 375px / tablet 768px | No responsive testing screenshot | NOT TESTED |

**Note:** A successful availability search is not equivalent to fully exercising the server-side admin confirmation conflict check. The latter remains pending.

## Suggested conflict confirmation test

1. Keep confirmed booking #3 for Coastal Comfort Room (26-27 Nov 2026).
2. Use a separate registered guest account to submit a request for the same room and dates if the app allows it; if creation is blocked at the request stage, test the reject behavior there.
3. If a second pending request exists, sign in as admin and click Confirm on the second request.
4. Verify an error is shown and existing confirmed booking #3 remains unchanged. Do not modify production records.

## Submission reminder

Fill student name and ID in `project_report.pdf` (re-export as a new PDF), verify the screenshots and conclusions, and keep the academic AI-use declaration.
