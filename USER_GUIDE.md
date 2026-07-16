# White Lotus Trading - F.Z.E. — User Guide

**Website:** https://whitelotusfze.com  
**Admin Panel:** https://whitelotusfze.com/admin

---

## Table of Contents

- [1. Frontend (Public Website)](#1-frontend-public-website)
  - [1.1 Homepage](#11-homepage)
  - [1.2 Products](#12-products)
  - [1.3 Product Detail](#13-product-detail)
  - [1.4 Blog](#14-blog)
  - [1.5 Inquiry Form](#15-inquiry-form)
  - [1.6 Contact Page](#16-contact-page)
  - [1.7 Static Pages](#17-static-pages)
  - [1.8 Search](#18-search)
  - [1.9 Newsletter Subscribe](#19-newsletter-subscribe)
  - [1.10 Unsubscribe](#110-unsubscribe)
- [2. Backend (Admin Panel)](#2-backend-admin-panel)
  - [2.1 Login](#21-login)
  - [2.2 Dashboard](#22-dashboard)
  - [2.3 Products Management](#23-products-management)
  - [2.4 Inquiries Management](#24-inquiries-management)
  - [2.5 Pages Management](#25-pages-management)
  - [2.6 Blog Management](#26-blog-management)
  - [2.7 Media Library](#27-media-library)
  - [2.8 Subscribers](#28-subscribers)
  - [2.9 Categories](#29-categories)
  - [2.10 Home Settings](#210-home-settings)
- [3. Appendices](#3-appendices)
  - [3.1 Security Features](#31-security-features)
  - [3.2 Image Upload Guidelines](#32-image-upload-guidelines)
  - [3.3 Default Credentials](#33-default-credentials)

---

## 1. Frontend (Public Website)

### 1.1 Homepage

**URL:** `/`

The homepage is composed of three sections:

1. **Hero Banner** — A split layout with text on the left and an image on the right. The label, title, subtitle, and buttons are all editable from the admin **Home Settings** page. The background image is uploaded via the same panel.

2. **Product Catalog** — A tabbed grid showing up to 4 featured products per tab. Two tabs are available:
   - **HVAC PRODUCTS** — Industrial HVAC equipment
   - **CONSUMABLES** — Wellness and organic products
   
   Click a product card to visit its detail page. Use the tab buttons to switch between categories.

3. **Global Reach Section** — Displays a map image and company stats. The image is managed via **Home Settings** in the admin panel.

### 1.2 Products

**URL:** `/products`

The full product catalog with search and filter capabilities.

- **Search** — Type a keyword in the search box and press Enter to filter products by name, SKU, or description.
- **Division Filter** — Use the dropdown to show only HVAC or Consumables products.
- **Clear Filters** — Click the link to reset all filters.
- **Pagination** — Navigate between pages using the page numbers at the bottom.
- Each product card shows the image, name, SKU, description, and price. Click **View Specs** to see the full detail page.

### 1.3 Product Detail

**URL:** `/product/{slug}` (e.g., `/product/industrial-chiller`)

Shows complete information for a single product:

- Full-size product image
- Division badge (HVAC / Consumables)
- Product name and SKU
- Full description
- **Technical Specifications** — If the admin has entered spec data, it appears as a key-value table (e.g., Capacity, Voltage, Weight).
- Price and stock information
- **Request Quote** — Click to go to the inquiry form with the product name pre-filled.

### 1.4 Blog

**URL:** `/blog`

Blog listing with category filter and pagination.

- **Category Filter** — Use the sidebar links to filter posts by category (Company, Industry News, Wellness).
- **Recent Posts** — The sidebar also shows the 5 most recent posts.
- Each post card shows the featured image, category badge, publication date, title, and excerpt. Click the title or image to read the full post.

**Single Post** — Shows the featured image, full content, and sidebar with recent posts.

### 1.5 Inquiry Form

**URL:** `/inquiry`

Used for two purposes:

**Full Inquiry** — Fill in all fields to send a business inquiry:
- Name (required)
- Email (required)
- Phone
- Company
- Division (General / HVAC / Consumables)
- Subject (required)
- Message (required, min 10 characters)

Upon submission:
1. A confirmation email is sent to you
2. The admin team receives a notification
3. You are redirected to a success page

**Rate Limit:** 3 submissions per 30 minutes per session.

**Quick Subscribe** — The same page also handles the newsletter subscription from the footer (see [1.9 Newsletter Subscribe](#19-newsletter-subscribe)).

### 1.6 Contact Page

**URL:** `/contact`

Displays the company's contact information:
- Office address (Dubai, UAE)
- Phone number
- Email address
- Business hours (Sunday - Thursday, 9:00 AM - 6:00 PM)

The "Send a Message" form on this page works the same as the inquiry form.

### 1.7 Static Pages

**URL:** `/page/{slug}` (e.g., `/page/about-us`)

Static content pages created by the admin. These include:
- **About Us** — Company background, mission, and values
- **Privacy Policy** — Data handling and privacy practices
- **Terms of Service** — Terms and conditions

These pages use the same header and footer as the rest of the site.

### 1.8 Search

**URL:** `/search`

Site-wide search across products, blog posts, and pages.

- Enter at least 2 characters in the search box and press Enter.
- Results are grouped by type with a badge (Product, Blog Post, or Page).
- Click any result to go to its page.
- If no results match, a "No results" message is shown.

### 1.9 Newsletter Subscribe

**Location:** Footer of every page (and the inquiry page)

Enter your email address in the subscribe input and click the button. A confirmation message appears at the top of the page.

If you were previously subscribed and then unsubscribed, subscribing again will reactivate your subscription.

### 1.10 Unsubscribe

**URL:** `/unsubscribe?email=...&token=...`

Click the unsubscribe link in any email sent by the system. The link contains a secure token that validates your email address. After confirmation, your subscription status is set to "unsubscribed" and you will no longer receive emails.

---

## 2. Backend (Admin Panel)

**URL:** `https://whitelotusfze.com/admin`

### 2.1 Login

The login page is a standalone page (not inside the admin layout).

- Enter your **Username** and **Password**.
- Click **Sign In**.
- On success, you are redirected to the Dashboard.
- On failure, an error message is shown. After 5 failed attempts, the account is locked for 15 minutes.

### 2.2 Dashboard

After login, the Dashboard shows:

- **Stats Cards** — Counts of published pages, active products, and unread inquiries.
- **Quick Actions** — Links to add a product, edit pages, and view inquiries.
- **Recent Inquiries** — The 5 most recent contact form submissions. Click any row to view details.

**Navigation:** The left sidebar contains links to all admin sections. Your current section is highlighted. The sidebar also shows your name/role and a **Logout** button.

### 2.3 Products Management

**URL:** `/admin/products`

**List View:**
- Table columns: Image + Name + SKU, Division, Inventory (stock + unit), Status (Active / Draft), Actions.
- **Search** — Type in the search box to filter by name/SKU.
- **Filter** — Use Division and Status dropdowns.
- **Pagination** — 10 products per page.
- **New Entry** — Click the button to open the product form in "create" mode.

**Product Form (slide-out modal):**

| Field | Details |
|---|---|
| Name | Required, 2-200 characters |
| SKU | Stock keeping unit code |
| Unit | e.g., Units, Pieces, Boxes |
| Division | HVAC or Consumables |
| Status | Draft (hidden from site) or Active (visible) |
| Price | Numeric value (0 = "Contact for Price") |
| Stock | Integer quantity |
| Description | Full product description (plain text) |
| Category | Select from existing categories |
| Meta Description | SEO description (max 160 chars) |
| Technical Specs | Dynamic key-value pairs. Click **Add Row** to add a spec (e.g., "Capacity: 5 Tons"). Click the **X** button to remove a row. |
| Product Image | Upload JPEG, PNG, GIF, or WebP (max 2MB). Shows a preview after selection. |

**Operations:**
- **Create** — Fill the form and click Save. A unique slug is auto-generated from the name.
- **Edit** — Click the edit icon on any product row. The modal opens with existing data pre-filled. Make changes and click Save.
- **Delete** — Click the delete icon. A JavaScript confirmation dialog appears. Confirm to remove the product permanently.

### 2.4 Inquiries Management

**URL:** `/admin/inquiries`

**List View (left pane):**
- Filter tabs: **All**, **Unread**, **Flagged**
- Search by name, email, or subject
- Shows: name, subject, message preview, division, status badge, and date
- Unread items show a colored indicator

**Detail View (modal):**
- Click any inquiry to open the detail modal.
- Shows: name, email, phone, company, date, and full message.
- **Status** — Can be changed to Replied or Closed.
- **Reply Form** — Type your response in the textarea and click **Send Reply**. This sends an email to the customer and saves the reply as admin notes.
- **Delete** — Remove the inquiry from the database.

**Note:** Clicking an unread inquiry automatically marks it as read.

### 2.5 Pages Management

**URL:** `/admin/pages`

**Note:** The "Home" page is excluded from this list — it is managed entirely through **Home Settings**.

**List View:**
- Table columns: Page title + slug, Content preview (truncated plain text), Status (Published / Draft), Actions.
- Click the **edit** icon to open the editor modal.

**Edit Modal:**
- **Title** — Page title displayed in the browser tab and page heading.
- **Slug** — URL path segment (e.g., `about-us`).
- **Meta Description** — SEO meta tag (max 160 characters).
- **Status** — Draft (hidden) or Published (visible at `/page/{slug}`).
- **Last Updated** — Read-only timestamp.
- **Content** — Rich text editor powered by TinyMCE. Toolbar includes: undo/redo, heading blocks, bold/italic/underline/strikethrough, alignment, ordered/unordered lists, link, image, table, code view, and remove formatting.
  - **Image Upload** — Click the image icon in the editor toolbar to upload an image from your computer. The image is saved to the uploads directory and inserted at the cursor position.

### 2.6 Blog Management

**URL:** `/admin/blog`

**List View:**
- Table columns: Title (with featured image thumbnail + slug), Category, Status (Published / Draft), Date, Actions.
- Filter by status and category.
- Pagination (10 per page).

**Operations:**
- **Create** — Click **New Post** to open the slide-out modal in create mode.
- **Edit** — Click the edit icon to open the modal with existing data.
- **Delete** — Click the delete icon and confirm.

**Blog Post Form (slide-out modal):**

| Field | Details |
|---|---|
| Title | Required, 2-200 characters |
| Category | Select from existing blog categories |
| Status | Draft or Published |
| Excerpt | Short summary (shown in listings) |
| Meta Description | SEO description |
| Content | TinyMCE rich text editor (same as Pages) |
| Featured Image | Upload JPEG/PNG/GIF/WebP (max 2MB). Shown at the top of the blog post. |

**Note:** When a post is published for the first time, the `published_at` date is set automatically. Changing a published post back to draft and republishing later preserves the original publish date.

### 2.7 Media Library

**URL:** `/admin/media`

Browse all uploaded files in a grid layout.

- Each file card shows: thumbnail (for images) or file icon (for SVGs/ICOs), filename, file size in KB, and a **Copy URL** button.
- Click **Copy URL** to copy the file's public URL to your clipboard. Use this URL to embed images in page/blog content.
- Files are sorted by upload date (newest first).
- Uploading happens through the TinyMCE editors or the product image upload — there is no direct upload button on this page.

**Allowed file types:** JPG, JPEG, PNG, GIF, WebP, SVG, ICO.

### 2.8 Subscribers

**URL:** `/admin/subscribers`

Manage newsletter subscribers.

- **List View:** Table with email, name, status (Active / Unsubscribed), and creation date.
- **Delete** — Click the delete icon to permanently remove a subscriber.
- Pagination: 20 subscribers per page.

Subscribers are created when someone fills in the newsletter form on the frontend.

### 2.9 Categories

**URL:** `/admin/categories`

Two-column layout for managing product categories.

**Left Column (Category List):**
- Table with Name, Slug, Description, and Actions.
- Click **Edit** to load the category into the form.
- Click **Delete** to remove the category (confirmation dialog).

**Right Column (Category Form):**
- **Name** — Display name (required, unique).
- **Description** — Optional description.
- Click **Save** to create or update the category.
- Click **Cancel** (or the **+** button) to reset the form to "add" mode.

**Default categories:** HVAC Systems, HVAC Parts, Wellness Supplements, Spices & Herbs.

### 2.10 Home Settings

**URL:** `/admin/settings`

Control the homepage hero content and site identity. This page has three sections:

#### Hero Section
| Field | Editor Type | Notes |
|---|---|---|
| Label | Text input | Small uppercase label above the title |
| Image Placeholder Text | Text input | Fallback text when no hero image is set |
| Title | TinyMCE rich editor | Main headline. Supports bold, italic, colored spans. |
| Subtitle | TinyMCE rich editor | Supporting text below the title |
| Button 1 Text | Text input | Left button label |
| Button 1 Link | Text input | URL the left button points to |
| Button 2 Text | Text input | Right button label |
| Button 2 Link | Text input | URL the right button points to |
| Hero Background Image | File upload + checkbox | Upload JPEG/PNG/GIF/WebP. Check "Remove image" to clear it. |

#### Global Map Section
| Field | Notes |
|---|---|
| Section Image | Upload the map image shown in the "Global Reach, Local Expertise" section |

#### Site Identity
| Field | Notes |
|---|---|
| Site Logo | Upload your company logo. Displayed in the header navigation bar. |
| Favicon | Upload a browser tab icon. Recommended: 32x32px PNG or ICO. |

**Workflow:**
1. Make your changes.
2. Click **Save Settings** at the bottom of each section.
3. Use the **Preview Homepage** link (opens in a new tab) to see changes live.

**Image Upload:** When you select a file, it is uploaded and saved when you click Save. The preview shows the current image. To replace an image, just select a new file and save. To remove an image entirely, check the "Remove image" checkbox and save.

---

## 3. Appendices

### 3.1 Security Features

The website includes several layers of security:

- **CSRF Tokens** — Every form includes a hidden token that is validated on submission. This prevents cross-site request forgery attacks.
- **Honeypot Fields** — A hidden form field (`website`) that is invisible to humans but catches automated bots. If a bot fills it in, the submission is silently discarded.
- **Rate Limiting** — Login is limited to 5 attempts before a 15-minute lockout. Inquiries are limited to 3 per 30 minutes per session.
- **Content Security Policy (CSP)** — The site sends strict HTTP headers that control which scripts, styles, and images can load.
- **Input Sanitization** — User input is sanitized before display to prevent XSS attacks. Rich text fields only allow a safe subset of HTML tags.
- **Password Security** — Passwords are hashed with bcrypt (cost factor 12). Sessions are managed server-side with HTTP-only cookies.

### 3.2 Image Upload Guidelines

| Property | Details |
|---|---|
| Allowed formats | JPEG, PNG, GIF, WebP |
| Maximum file size | 2 MB |
| Allowed in editors | TinyMCE accepts drag-and-drop or file picker uploads |
| Storage location | `uploads/` directory — files are accessible via `/uploads/filename.jpg` |
| Naming | Files are renamed to a random hex string on upload to prevent name collisions |
| Media Library | All uploaded files appear in the admin Media Library for browsing and URL copying |

### 3.3 Default Credentials

| Role | Username | Password |
|---|---|---|
| Super Admin | `admin` | `admin123` |

**Important:** Change the default password immediately after the first login. Go to an admin page and ask the developer to update the password, or use the install script to reset.

---

*Last updated: July 2026*
