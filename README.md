# SMA (Sales Management Application)

SMA is a comprehensive Sales Management Application designed to streamline and automate sales operations for pharmaceutical and healthcare field teams. Built on Laravel and Filament, SMA provides robust tools for campaign management, customer tracking, product booking, coupon distribution, and advanced reporting.

## Features

- **Campaign Management:** Create, manage, and monitor marketing campaigns for products and brands.
- **KSV Bookings:** Record and track product bookings (KSV Bookings) with invoice uploads and validation.
- **Customer Management:** Manage doctors, chemists, and other customer types with detailed profiles and approval workflows.
- **Coupon Distribution:** Automate coupon code generation and email/SMS delivery to eligible customers.
- **Product Catalog:** Maintain a catalog of products and brands, including Kofol and other pharma products.
- **User Roles & Permissions:** Fine-grained access control using Filament Shield for admin, manager, and field staff roles.
- **Advanced Reporting:** Real-time dashboards and exportable reports for campaign performance, sales, and customer engagement.
- **File & Image Management:** Secure S3 storage for invoices and campaign assets, with lightbox previews.
- **Bulk Actions & Imports:** Import/export data, bulk approve/reject entries, and send notifications in bulk.
- **Responsive UI:** Modern, mobile-friendly admin panel powered by Filament.

## Tech Stack
- **Backend:** Laravel 10+
- **Frontend/Admin:** Filament 3.x
- **Database:** MySQL (or compatible)
- **Storage:** AWS S3 (for invoices and assets)
- **Other:** Spatie Media Library, Filament Shield, custom plugins for lightbox, advanced widgets, and more.

## Getting Started

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & npm (for asset compilation)
- MySQL or compatible database
- AWS S3 credentials (for file storage)

### Installation
1. **Clone the repository:**
   ```bash
   git clone <your-repo-url> sma
   cd sma
   ```
2. **Install dependencies:**
   ```bash
   composer install
   npm install && npm run build
   ```
3. **Copy and configure environment:**
   ```bash
   cp .env.example .env
   # Edit .env with your DB, S3, and mail credentials
   php artisan key:generate
   ```
4. **Run migrations and seeders:**
   ```bash
   php artisan migrate --seed
   ```
5. **Start the development server:**
   ```bash
   php artisan serve
   ```
6. **Access the admin panel:**
   Visit `http://localhost:8000/admin` and log in with the seeded admin credentials.

## Contributing
We welcome contributions! Please open issues or submit pull requests for new features, bug fixes, or improvements.

## License
This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).

---
For more information, contact the SMA development team or refer to the in-app documentation.
