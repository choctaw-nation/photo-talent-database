# CNO Photo Talent Database

A WordPress site to allow the photo team to find models for different photoshoots.

## Changelog

### v1.5.0 - [August 20, 2025]

-   Added: Users can now set custom "last-used" date!
-   Fixed: Modal now has spinner when loading data
-   Fixed: Refactored spinner to global util functions

### v1.4.0 - [August 19, 2025]

-   Added: New Modal to preview Talent Details on Talent archive page
-   Chore: Updated packages

### v1.3.2

-   Updated: Cron Events are now handled in their own class
-   Fixed: Missing talent-list expiry cron event is now wired up.

### v1.3.1

-   Fixed: Nav Item's conditional rendering logic has been optimized
-   Fixed: Post images set with Gravity Forms are now handled correctly in the rest router

### v1.3.0

-   Added: Nav Items added for pending talent and talent lists
-   Added: Talent Lists can be deleted
-   Fixed: Generated PDFs also generate higher quality images

### v1.2.1

-   Fixed: Pagination renders if it exists

### v1.2.0

-   Added: Pending Talent posts can now be approved/rejected on client side.
-   Added: Pending Talent posts also have breadcrumbs

### v1.1.0

-   Added: "Login / Logout" button added to header
-   Fixed: Cleaned up footer

### v1.0.2

-   Fixed: Swapped `<details>` block for BS Collapse for better mobile support (iOS Webkit bug)
-   Fixed: Swapped method for saving pdf to save instead of opening the blob in new window for better mobile support

### v1.0.1

-   Fixed: Images now render correctly on the talent single pages.

### v1.0.0

-   First Build
