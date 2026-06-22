# Pure PHP MVC Migration Plan

## Why this needs a staged rewrite

This project is not just a few Laravel pages. The current app includes:

- Public storefront and landing pages
- Product catalog and category browsing
- Cart and checkout
- Paystack callback and webhook handling
- Customer dashboard and profile flows
- Service, consultancy, booking, and emergency request modules
- Admin dashboards and operational management tools
- Role-based access control
- Notifications, receipts, and activity logs

Because of that, the safest path is to move module by module into a lightweight PHP MVC structure instead of trying to replace the whole app in one edit.

## Current module inventory

### Public routes

- `/`
- `/shop`
- `/shop/{slug}`
- `/services`
- `/consultancy`
- `/booking`
- `/emergency`
- `/payments/paystack/callback`
- `/webhooks/paystack`

### Authenticated customer routes

- `/dashboard`
- `/cart`
- `/checkout`
- `/orders`
- `/my-services`
- `/my-consultancy`
- `/my-bookings`
- `/my-emergency`
- `/receipts/{payment}`
- `/notifications`
- `/profile`
- `/reviews/{type}/{record}`

### Admin routes

- `/admin`
- Category management
- Product management
- Marketplace sync
- Orders and order tracking
- Payments
- Service requests and tracking
- Consultancy assignment
- Booking updates
- Emergency dispatch and tracking
- Reports and activity logs

## Laravel features currently in use

These are the pieces we need to replace in pure PHP:

- Routing and middleware
- Blade templating and layouts
- Eloquent models and relationships
- Form request validation
- Authentication and session guards
- Role/permission checks
- Notifications
- Queued jobs
- Database migrations and seeders
- Storage helpers
- Config and environment handling

## Recommended migration order

### Phase 1: Foundation

- Build front controller
- Build router
- Build base controller and view renderer
- Build PDO database layer
- Build session and auth helpers
- Add shared config loader

### Phase 2: Public pages

- Home page
- Shop listing and product detail
- Services, consultancy, booking, and emergency landing pages

### Phase 3: Accounts and auth

- Register
- Login/logout
- Password reset flow, if still required on shared hosting
- Profile management

### Phase 4: Commerce

- Cart
- Checkout
- Orders
- Paystack callback and webhook verification
- Receipts

### Phase 5: Service operations

- Service requests
- Consultancy requests
- Bookings
- Emergency requests and tracking

### Phase 6: Admin area

- Role checks
- Dashboard analytics
- CRUD screens
- Tracking and assignment workflows

## Database strategy

The easiest migration path is to keep the existing MySQL schema shape as much as possible and replace Laravel access with PDO-based repositories/models.

Recommended approach:

- Export the production-ready MySQL schema from the Laravel version
- Stop relying on Laravel migrations in hosting
- Add SQL install files for shared hosting import through phpMyAdmin
- Port models one table group at a time

## Shared hosting target structure

Recommended cPanel-friendly layout:

- `public_html/index.php`
- `app/Controllers`
- `app/Models`
- `app/Views`
- `app/Core`
- `config`
- `storage/logs`
- `storage/uploads`
- `routes/web.php`

If your host allows a custom document root, you can keep `public/` as the web root. If not, use `public_html/` and keep application code above it.

## What has been started in this repo

A lightweight pure PHP MVC starter has been added under `mvc/` with:

- Front controller
- Router
- View renderer
- PDO database bootstrap
- Session helper
- Basic public pages

This starter is intentionally small so we can port real business modules into it cleanly.

## Next recommended implementation steps

1. Port the database connection to your actual shared-hosting MySQL credentials.
2. Add SQL schema export files for the core tables.
3. Port product/category listing first so the public site works.
4. Port login/session handling.
5. Port cart and checkout before touching admin features.

