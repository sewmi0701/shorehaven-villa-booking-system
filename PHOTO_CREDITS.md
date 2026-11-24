# Shorehaven Villa — approved room photo credits

**All six pictures are real stock photographs of other properties. Shorehaven Villa and its room names are fictional student project examples.** Do not present these photographs as verified pictures of accommodation actually offered by Shorehaven Villa. Credits/links are provided for educational transparency.

The photographs are from individual Unsplash free-photo pages, subject to the [Unsplash License](https://unsplash.com/license). Owners are credited below. The user explicitly approved photos **2, 3, 5 and 6**; photos **1 and 4 are unchanged**.

| # | Demonstration room | Photographer | Original photo | Change |
|---|---|---|---|---|
| 1 | The Ocean Suite | Michael Lock | [Unsplash source](https://unsplash.com/photos/a-bedroom-with-a-view-of-the-ocean-AMDFVRlTlAU) | Unchanged |
| 2 | Palm Garden Deluxe | Alef Morais | [Unsplash source](https://unsplash.com/photos/tropical-poolside-cabana-with-palm-trees-and-pool-G_g0L4ygli0) | Approved new photo |
| 3 | The Family Retreat | Kirke Kiki | [Unsplash source](https://unsplash.com/photos/modern-resort-entrance-with-palm-trees-and-ocean-view-gm2Ehgl6jG8) | Approved new photo |
| 4 | Sunset Balcony Suite | Colton Jones | [Unsplash source](https://unsplash.com/photos/a-bedroom-with-a-view-of-the-ocean-OfLluwPamI0) | Unchanged |
| 5 | Coastal Comfort Room | Margo Evardson | [Unsplash source](https://unsplash.com/photos/palm-trees-in-front-of-a-hotel-building-_sTUiYXIqGM) | Approved new photo |
| 6 | Ocean View Deluxe | Pasqualino Capobianco | [Unsplash source](https://unsplash.com/photos/a-tall-white-building-with-palm-trees-in-front-of-it-9yLe9r7dal4) | Approved new photo |

Photos 2, 3, 5 and 6 show exterior/resort architecture; photos 1 and 4 show rooms. These different hotel images do **not** depict a single real property.

## Technical behavior

- Normal loading uses the Unsplash CDN (requires an internet connection). Existing local SVG illustrations remain fallback imagery.
- Run `DOWNLOAD_ROOM_PHOTOS.bat` with internet access to cache all six photos locally for offline use. Newly approved images use filenames ending `-approved.jpg` so an old downloaded photo cannot replace a newly approved selection.
- The PHP helper `room_photo_key()` matches the six demo names without changing `rooms` table values. Existing bookings, room prices, and login accounts stay unchanged.
- You **must not re-import** `db_structure.sql` when installing the photo update.
