# SYSTEM_SPEC.md

> **📌 For GitHub Copilot & Developers**
>
> This file is the **single source of truth** for the Centralized Canteen E-Commerce Management System.
> - Keep this file **up-to-date** as the system evolves.
> - When coding with **GitHub Copilot**, open this file **side-by-side** with the file you’re working on for the most accurate AI-assisted code suggestions.
> - Follow the described **roles, rules, and features** exactly unless an update to the spec is agreed upon.

---

# SYSTEM_SPEC.md

## 🔐 Authentication & Roles

- Use **Laravel Jetstream** for authentication (login, registration, password reset).  
- User roles managed by **Spatie Laravel-Permission** package.  
- Roles: `admin`, `cashier`, `tenant`, `customer`, `guest` (guest is unauthenticated).  
- **Role-based access control (RBAC)** enforced via middleware, gates, and policies.  
- Upon login, users are directed to their respective dashboards based on role:  
  - `admin` → Admin Panel  
  - `tenant` → Tenant Panel  
  - `cashier` → Cashier POS Interface  
  - `customer` → Public site with profile and order tracking  
- Guests can browse and place orders without login but must provide contact info at checkout.

---

## 🔐 Two-Factor Authentication (2FA)

- Use Laravel Jetstream’s built-in **2FA** feature to enhance security.  
- **Enabled for Admin and Tenant roles only** (optional for others).  
- Users set up 2FA using authenticator apps (Google Authenticator, Authy).  
- During login, after password verification, prompt 2FA code input if enabled.  
- Provide UI for enabling/disabling 2FA in user profile settings.  
- Enforce 2FA requirement via middleware or gate policies for Admin and Tenants.  
- Cashiers, Customers, and Guests are **not required** to use 2FA.

---

## 📌 Purpose
This document serves as the **single source of truth** for the Canteen Ordering System.  
GitHub Copilot should use this specification to ensure all generated code matches the intended business logic, features, and layout.

---

## 🎯 System Overview
The system is an **online canteen ordering platform** with no inventory tracking.  
It allows customers to order from multiple food stalls.  
Roles include **Admin**, **Cashier**, **Stall Tenant**, **Customer**, and **Guest**.  
Both Admin and Stall Tenants act as **sellers** who can create and manage products.

---

## 👤 Roles & Responsibilities

### **1. Admin**
- Create & manage user accounts (including Stall Tenants and Cashiers)
- Manage stalls (assign tenants to stalls)
- Add/edit/delete products for any stall
- View & manage orders for **their own products only** (Admin is a seller too)
- View sales reports for Admin-owned stall only
- Manage employee attendance & salary (Cashiers)
- Manage their own profile

### **2. Cashier** *(Admin-assigned)*
- Handle **onsite orders** via POS
- Process payments
- Manage order statuses for walk-in customers on Admin’s stall only
- Manage their own profile

### **3. Stall Tenant**
- Manage their assigned stall’s details
- Create/edit/delete products for their own stall
- Toggle product visibility (Publish/Hide)
- View and manage orders for their stall only
- View sales report for their stall
- Handle onsite orders via POS
- Manage their own profile

### **4. Customer (Logged-in)**
- Browse stalls and menu
- Add products to cart
- Place online orders
- View past orders & order status
- Manage their own profile (name, contact info)

### **5. Guest**
- Browse stalls and menu
- Cannot place orders until checkout with required contact info (name, phone/email)
- Track orders using unique order reference (no login required)

---

## 📦 Core Features by Role

| Feature                                      | Admin | Cashier | Tenant | Customer | Guest |
|----------------------------------------------|:-----:|:-------:|:------:|:--------:|:-----:|
| Create user accounts                         | ✅    | ❌      | ❌     | ❌       | ❌    |
| Manage stalls                                | ✅    | ❌      | ❌     | ❌       | ❌    |
| Add/edit/delete products                     | ✅    | ❌      | ✅     | ❌       | ❌    |
| Toggle product visibility (Publish/Hide)     | ✅    | ❌      | ✅     | ❌       | ❌    |
| View/manage orders for own products only    | ✅    | ✅*     | ✅     | ❌       | ❌    |
| Process onsite orders (POS)                  | ❌    | ✅      | ✅     | ❌       | ❌    |
| View sales reports                           | ✅    | ❌      | ✅     | ❌       | ❌    |
| Browse menu                                  | ✅    | ✅      | ✅     | ✅       | ✅    |
| Place online orders                          | ❌    | ❌      | ❌     | ✅       | ✅**  |
| View order history                           | ✅    | ✅*     | ✅     | ✅       | ✅**  |
| Manage profile                               | ✅    | ✅      | ✅     | ✅       | ❌    |

*Cashier views orders related to onsite POS only (Admin’s stall).  
**Guest orders tracked by order reference, no login required.

---

## 🌐 Public Website Pages (Customer-Facing)

### **1. Home Page**
**Purpose:** Showcase the canteen, highlight stalls and trending food.  

**Sections:**
1. **Header**
   - Logo (canteen branding)
   - Navigation links: `Home | Menu | Stalls | Login/Register`
   - Cart icon (with item count)
   - Mobile hamburger menu for small screens
2. **Hero Banner**
   - Large canteen background image
   - Headline: `"Order Fresh, Hot, and Fast from Your Favorite Stalls"`
   - CTA Button → links to Menu page
3. **Featured Stalls** *(dynamic)*
   - Horizontal carousel of stalls with logo, name, tagline
   - "View Menu" button per stall
4. **Trending Food Items** *(dynamic)*
   - Grid layout: image, name, price, stall name, "Add to Cart" button
5. **How It Works** *(static)*
   - Icons with short steps: Browse → Order → Pay → Enjoy
6. **Footer**
   - About the canteen
   - Contact info
   - Terms & Privacy links  

---

### **2. Menu Page**
**Purpose:** Display all products from all sellers with filters/search.  

**Sections:**
1. **Header** – same as Home page
2. **Filter & Search Bar** *(dynamic)*
   - Dropdown: Filter by Stall
   - Dropdown: Filter by Category (e.g., Rice Meals, Snacks, Drinks)
   - Search input (product name)
   - Reset filters button
3. **Menu Grid** *(dynamic)*
   - Product card: image, name, price, stall name, "Add to Cart"
   - Pagination if too many products
4. **Cart Sidebar** *(Livewire component)*
   - Slides in from right
   - Shows cart items, quantity controls, total price
   - Checkout button
5. **Footer** – same as Home page  

---

### **3. Stalls Page**
**Purpose:** Showcase each stall’s profile and menu preview.  

**Sections:**
1. **Header** – same as Home page
2. **Stalls Grid** *(dynamic)*
   - Stall card: logo/image, name, tagline, avg rating, "View Menu" button
   - Optional category tabs (All, Drinks, Snacks, Meals)
3. **Footer** – same as Home page  

---

## 🛠 Tech Stack & Tools
- Laravel 12.20 (Backend & Blade templates)
- Filament v3 (multi-panel admin and tenant dashboards)
- Spatie Laravel-Permission for roles and access control
- Laravel Reverb for real-time broadcasting (Echo)
- Livewire for dynamic components (cart, checkout, POS)
- MySQL (via XAMPP)
- Tailwind CSS for styling
- Alpine.js for lightweight interactivity
- VS Code as IDE
- GitHub Copilot for AI-assisted coding

---

## 🔒 Security & Data Rules
- Tenants cannot view or edit other tenants’ orders or products.
- Cashiers can only process orders for Admin’s stall (onsite POS).
- Customers can only see their own orders.
- Admin can only view/manage orders and products belonging to Admin stall.
- Guests track orders using unique `order_reference` without login.

---

## 🧑‍💻 Developer Notes for GitHub Copilot
- Use Laravel 12 conventions for controllers, models, migrations.
- Livewire components for interactivity: cart, filters, POS, checkout.
- Tailwind CSS for styling; follow mobile-first responsive design.
- Use Eloquent with eager loading for menu and orders.
- Implement role-based gates/policies via Spatie.
- New products default to `published = true` unless toggled hidden.
- Separate dashboards for Admin and Tenants.
- Real-time updates with Laravel Reverb broadcasting.
- Guests provide contact info at checkout for order tracking.

---

## **Product Creation Rules**
- Sellers (Admin/Tenants) create products.
- Products can be **hidden/draft** with toggle until published.
- Only published products appear on menu for customers.

---

## **Order & Checkout Flow**

### General Rules
- Cart can contain products from multiple stalls.
- Checkout splits orders by stall behind the scenes.
- Sellers manage only their own orders.
- Payments:
  - Guests → online only
  - Customers (logged in) → online or onsite if employee
  - Cashiers → onsite only
- Real-time order updates via Laravel Reverb.

### Guest Checkout Rules
- Guests must enter:
  - Full Name (required)
  - Phone Number (required for SMS updates)
  - Email Address (optional or required for email updates)
- Validate presence of at least one contact method.
- Save guest info in `orders` table fields:
  - `customer_name`
  - `customer_phone`
  - `customer_email`
- Generate unique `order_reference` (e.g., `CANTEEN-YYYYMMDD-####`).
- Send order confirmation and tracking link via provided contact method.
- Guests can track orders on a **Track Order** page using `order_reference` without login.
- Real-time order status updates are pushed via Laravel Reverb if the guest is on the tracking page.

---

## **POS (Onsite Orders)**
- Accessible by Admin, Tenants, and Cashiers.
- Quick order entry bypassing online checkout.
- Receipt printing.

---

## **Profiles**
- Admin, Tenants, Cashiers: edit name, contact info, password.
- Customers: edit name, contact, address, password.
- Guests: no profiles.



## 📌 Developer Notes
- Keep `SYSTEM_SPEC.md` **open alongside your working file** when using GitHub Copilot for best results.
- Follow role rules strictly when building features.
- Commit any spec changes before implementing new features.

