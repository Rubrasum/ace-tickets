# Ace Tickets

This is an open source sample app to demo load testing features. I have not yet decided on Artillery or k6 for the 
load testing tool. It's meant to be useful for any organization that runs events with ticket scanners, onsite ticket 
counter, and a public facing ticket purchase website, that also needs to confirm their ability to handle large loads 
over time. 

## System Components
### Laravel / Inertia / Vue3 Site
- Public Ticket Site 
- Ticket Counter App
- Backend API

### React Native 
- Ticket Scanner

### Artillery / k6
- Load Testing Suite


## Component Specifications

### Public Ticket Site (Inertia/Vue3 frontend, full responsive)
#### Features
- Ticket Browsing
- Cart
- Checkout w Stripe
- Generate Tickets
- Confirm via email

#### contains
- No permanent data
- Local browser session data

#### refers to 
- Backend for ticket information
- Backend for checkout data
#### writes to 
- Backend on purchase 
- Prompts Email via backend
- Prompts Save QR ticket data


### Onsite Ticket Counter (Inertia/Vue3, Desktop/tablet)
#### Features
- Choose Ticket Type
- Record Cash payment OR
- Strip Terminal
- Issue ticket with QR
- Show, print, or email ticket

#### contains
- Staff user auth

#### refers to
- Ticket data from backend
- staff use data for login/clockin
#### writes to
- Backend on purchase
- Prompts Email via backend
- Prompts Save QR ticket data

### Ticket Scanner App (React Native, Mobile / Table)
#### Features
- Camera QR Scanner
- Validation of Tickets
- Confirmation with Success/Fail indicator
- Log Scan times, Staff User ID, & location/zone
- ~~Caching with offline mode for areas with bad reception~~ Removed for now. That's an avanced scanner feature. We 
COULD add it but this demo is mostly for showing off the load testing features. All we need to do that is the 
  requests themselves. 

#### contains
- Staff user auth
- local cache of recent scans (offline mode)

#### refers to
- Backend Ticket validation
#### writes to
- Backend Scan log
- Backend Scan sync (for offline mode)


### Backend API (Laravel, MySQL, Redis)
#### Features
- API for storing and sharing DATA

#### contains
- Events
- Ticket Types, Prices
- Purchases Tickets Records
- QR Codes and Validation keys
- Scan Logs
- Staff User Acconts for scanners and counter
- Stripe Charges and webhooks

#### exposes API for
- Public frontend purchases (All within US)
- Counter App (only specified Staff)
- Scanner App (only specified Staff)

### Load Testing Suite (Artillery/k6)
#### Features
- Run simulated
  - scanners
  - ticket counters
  - public checkout

#### outputs
- Lataency Stats
- Throughput 
- Failures/Errors

#### Max Load Tested
- in Free mode, for Artillery, ~10,000 requests per month. (Could not confirm this.)

## Dependencies
All features rely on the Laravel API for storing and referencing data, with some local syncing on the scanner app. 
Laravel Backend manages all comms with Stripe. 

### Optional future features
- Stripe webhooks for auto-update tickets after payment
- Redis Queue to buffer scan logs and email jobs
- JWT auth for scanners - Secure device acceess
- QR ticket expiration (Might not be optional)
- PDF generator for at home printing (again, probably not optional)

## App Artchitecture
I have decided to run it all within a monorepo that looks like this: 
```
apps/
  scanner-app/     # React Native scanner
backend/           # Laravel + Inertia site and API
load-tests/        # Artillery scripts
```
You can obviously separate these into individual apps all separately hosted but for a small team without FE/BE 
specialization, this seems to me to be the best way to do it. It does require more tooling for devops and other 
aspects of development but is definitely easier for a single developer to handle. 


## Anticipated Routes (Extremely Rough Draft)
### PUBLIC ROUTES (Inertia / Web)

| Method | URI                   | Purpose                           |
|--------|------------------------|-----------------------------------|
| GET    | `/`                   | Home / Event landing page         |
| GET    | `/tickets`           | List ticket types                 |
| GET    | `/cart`              | Show cart                         |
| POST   | `/cart`              | Add ticket to cart                |
| DELETE | `/cart/{id}`         | Remove ticket from cart           |
| POST   | `/checkout`          | Submit order (Stripe session)     |
| GET    | `/checkout/success`  | Stripe success callback           |
| GET    | `/checkout/cancel`   | Stripe cancel                     |
| GET    | `/order/{id}`        | View past order + ticket links    |

---

### COUNTER STAFF ROUTES (Inertia / Web UI)

| Method | URI                      | Purpose                          |
|--------|--------------------------|----------------------------------|
| GET    | `/counter`               | Counter app interface            |
| POST   | `/counter/order`         | Create in-person cash order      |
| GET    | `/counter/order/{id}`    | Show printed order/ticket page   |

---

### AUTH ROUTES (Jetstream defaults already setup)

| Method | URI            | Purpose                  |
|--------|----------------|--------------------------|
| GET    | `/login`       | Show login form          |
| POST   | `/login`       | Log in                   |
| POST   | `/logout`      | Log out                  |
| GET    | `/register`    | Optional (public buyers) |
| POST   | `/register`    | Create account           |

---

### ADMIN DASHBOARD (Inertia / Web UI)

| Method | URI                  | Purpose                          |
|--------|----------------------|----------------------------------|
| GET    | `/admin`             | Admin dashboard home             |
| GET    | `/admin/events`      | List/manage events               |
| GET    | `/admin/tickets`     | List issued tickets              |
| GET    | `/admin/orders`      | List all orders                  |
| GET    | `/admin/scans`       | Scan log view                    |
| GET    | `/admin/staff`       | Manage users & devices           |

---

### SCANNER API ROUTES (React Native)

> Prefixed with `/api`, auth via Sanctum token or similar.

| Method | URI                          | Purpose                        |
|--------|------------------------------|--------------------------------|
| POST   | `/api/login`                 | Mobile staff login             |
| POST   | `/api/validate-ticket`       | Submit QR to validate          |
| GET    | `/api/event/current`         | Get current active event info  |
| POST   | `/api/scans`                 | Log scan result                |
| GET    | `/api/user`                  | Authenticated scanner profile  |
| GET    | `/api/tickets/{qr}`          | (Optional) Manual QR check     |

---

### STRIPE WEBHOOKS

| Method | URI                 | Purpose                |
|--------|---------------------|------------------------|
| POST   | `/webhooks/stripe` | Handle payment events  |

---

### SYSTEM / DEV

| Method | URI              | Purpose               |
|--------|------------------|-----------------------|
| GET    | `/health`        | Health check endpoint |
| GET    | `/api/version`   | API versioning        |

---

### Route Summary by Audience

| Audience        | Route Prefix / Location         |
|-----------------|----------------------------------|
| Public Buyers   | `/`, `/tickets`, `/checkout`    |
| Staff Counter   | `/counter/...`                  |
| Admins          | `/admin/...`                    |
| Mobile App      | `/api/...`                      |
| Stripe          | `/webhooks/...`                 |