# Install the approved photo changes — Windows / XAMPP

You already have a working database and application at `http://localhost:8080/shorehaven-villa/`.

1. Download **Shorehaven_Villa_Photos_Approved_Update.zip** to Downloads (do not open/import the SQL file).
2. Open Windows PowerShell and run:

   ```powershell
   Expand-Archive -LiteralPath "$env:USERPROFILE\Downloads\Shorehaven_Villa_Photos_Approved_Update.zip" -DestinationPath "C:\xampp\htdocs" -Force
   ```

3. Open **http://localhost:8080/shorehaven-villa/rooms.php** and press **Ctrl+F5** to refresh.
4. Images load over the internet. To keep local photo copies for offline use, double-click:

   `C:\xampp\htdocs\shorehaven-villa\DOWNLOAD_ROOM_PHOTOS.bat`

5. Photos 2/3/5/6 are updated. Photos 1/4 keep the earlier selection. Booking logic, SQL table content and demo login credentials are unchanged.

**DO NOT re-import db_structure.sql** or replace a live database. Existing bookings and your added `Ocean View Deluxe` room stay in your MySQL database.

The source report captures are from before this visual photo update; they are valid historical evidence of the tested workflow. `PHOTO_CREDITS.md` lists stock sources and explains that they are not genuine Shorehaven property photos.
