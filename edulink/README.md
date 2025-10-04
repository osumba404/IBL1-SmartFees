# Edulink SmartFees - Student Fee Management System

<p align="center">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

## 📋 Table of Contents

- [About](#about)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [Installation & Setup](#installation--setup)
- [Configuration](#configuration)
- [Database Schema](#database-schema)
- [User Roles & Permissions](#user-roles--permissions)
- [Payment Integration](#payment-integration)
- [API Documentation](#api-documentation)
- [Security](#security)
- [Testing](#testing)
- [Deployment](#deployment)
- [Contributing](#contributing)
- [License](#license)

## 🎯 About

**Edulink SmartFees** is a comprehensive student fee management system designed for **Edulink International College Nairobi**. The system streamlines fee collection, payment processing, student enrollment, and financial reporting for educational institutions.

### Key Objectives
- Automate fee collection and payment processing
- Provide real-time payment tracking and reporting
- Support multiple payment methods (M-Pesa, Stripe, Bank Transfer, Cash)
- Enable student self-service portal for fee management
- Facilitate administrative oversight and financial reporting

## ✨ Features

### 🎓 Student Portal
- **Registration & Authentication**: Secure student registration with email verification
- **Dashboard**: Overview of enrollment status, payment history, and outstanding balances
- **Fee Management**: View fee structures, make payments, download receipts
- **Payment History**: Complete transaction history with filtering and search
- **Profile Management**: Update personal information and contact details
- **Notifications**: Real-time updates on payment status and deadlines

### 👨‍💼 Admin Portal
- **Dashboard**: Comprehensive analytics and financial overview
- **Student Management**: Student registration, enrollment, and profile management
- **Course Management**: Course creation, fee structure assignment, and scheduling
- **Semester Management**: Academic period management and enrollment tracking
- **Payment Processing**: Payment verification, refunds, and manual entries
- **Fee Structure Management**: Dynamic fee configuration per course/semester
- **Reporting**: Financial reports, student analytics, and payment tracking
- **User Management**: Admin role management and permissions

### 💳 Payment Processing
- **Multiple Payment Methods**: M-Pesa, Stripe, Bank Transfer, Cash payments
- **Real-time Processing**: Instant payment verification and confirmation
- **Payment Plans**: Installment payment options with automated tracking
- **Late Fee Management**: Automatic late fee calculation and application
- **Receipt Generation**: Automated PDF receipt generation and email delivery
- **Refund Processing**: Streamlined refund management and tracking

### 📊 Reporting & Analytics
- **Financial Reports**: Revenue tracking, payment analytics, and outstanding balances
- **Student Reports**: Enrollment statistics, payment compliance, and academic progress
- **Export Functionality**: Excel/PDF export for all reports and data
- **Real-time Dashboards**: Live updates on key performance indicators

## 🛠 Technology Stack

### Backend
- **Framework**: Laravel 12.x (PHP 8.2+)
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Breeze with multi-guard authentication
- **Queue System**: Database-driven job processing
- **File Storage**: Local filesystem with configurable cloud storage support

### Frontend
- **Templating**: Blade templates with Bootstrap 5
- **JavaScript**: Vanilla JS with modern ES6+ features
- **CSS Framework**: Bootstrap 5 with custom SCSS
- **Icons**: Bootstrap Icons
- **Charts**: Chart.js for analytics visualization

### Payment Integrations
- **M-Pesa**: Safaricom M-Pesa API integration
- **Stripe**: International card payments
- **Bank Transfer**: Manual verification system
- **Cash Payments**: In-person payment recording

### Development Tools
- **Package Manager**: Composer (PHP), NPM (JavaScript)
- **Build Tools**: Vite for asset compilation
- **Code Quality**: Laravel Pint for code formatting
- **Testing**: PHPUnit for backend testing
- **Version Control**: Git with conventional commits

## 📁 Project Structure

```
edulink/
├── app/
│   ├── Console/Commands/          # Artisan commands
│   ├── Exports/                   # Excel export classes
│   │   ├── StudentsExport.php
│   │   └── StudentsTemplateExport.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/             # Admin controllers
│   │   │   │   ├── AdminController.php
│   │   │   │   ├── CourseController.php
│   │   │   │   ├── PaymentController.php
│   │   │   │   ├── SemesterController.php
│   │   │   │   └── StudentManagementController.php
│   │   │   ├── Auth/              # Authentication controllers
│   │   │   │   ├── AdminAuthController.php
│   │   │   │   └── StudentAuthController.php
│   │   │   └── Student/           # Student controllers
│   │   │       ├── PaymentController.php
│   │   │       └── StudentController.php
│   │   ├── Middleware/            # Custom middleware
│   │   └── Kernel.php
│   ├── Imports/                   # Excel import classes
│   │   └── StudentsImport.php
│   ├── Models/                    # Eloquent models
│   │   ├── Admin.php
│   │   ├── Course.php
│   │   ├── FeeStructure.php
│   │   ├── Payment.php
│   │   ├── PaymentNotification.php
│   │   ├── Semester.php
│   │   ├── Student.php
│   │   ├── StudentEnrollment.php
│   │   └── User.php
│   ├── Providers/                 # Service providers
│   └── Services/                  # Business logic services
│       ├── MpesaService.php
│       ├── PaymentService.php
│       └── StripeService.php
├── config/                        # Configuration files
├── database/
│   ├── migrations/                # Database migrations
│   └── seeders/                   # Database seeders
├── public/                        # Public assets
├── resources/
│   ├── css/                       # Stylesheets
│   ├── js/                        # JavaScript files
│   └── views/                     # Blade templates
│       ├── admin/                 # Admin views
│       ├── auth/                  # Authentication views
│       ├── layouts/               # Layout templates
│       └── student/               # Student views
├── routes/
│   ├── console.php               # Artisan commands
│   └── web.php                   # Web routes
├── storage/                      # File storage
├── tests/                        # Test files
└── vendor/                       # Composer dependencies
```

## 🚀 Installation & Setup

### Prerequisites
- PHP 8.2 or higher
- Composer 2.x
- Node.js 18+ and NPM
- MySQL 8.0+
- Web server (Apache/Nginx) or Laravel Valet

### Step 1: Clone Repository
```bash
git clone <repository-url>
cd edulink
```

### Step 2: Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### Step 3: Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Database Setup
```bash
# Create database
mysql -u root -p
CREATE DATABASE edulink_smartfees;
exit

# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed
```

### Step 5: Build Assets
```bash
# Development build
npm run dev

# Production build
npm run build
```

### Step 6: Start Development Server
```bash
# Start Laravel development server
php artisan serve

# Or use the dev script for full development environment
composer run dev
```

### Step 7: Access Application
- **Student Portal**: http://localhost:8000/student/login
- **Admin Portal**: http://localhost:8000/admin/login

### Default Admin Credentials
- **Email**: admin@edulink.ac.ke
- **Password**: password

## ⚙️ Configuration

### Environment Variables

#### Application Settings
```env
APP_NAME="Edulink SmartFees"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
```

#### Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=edulink_smartfees
DB_USERNAME=root
DB_PASSWORD=
```

#### Payment Gateway Configuration
```env
# M-Pesa Settings
MPESA_CONSUMER_KEY=your_consumer_key
MPESA_CONSUMER_SECRET=your_consumer_secret
MPESA_SHORTCODE=your_shortcode
MPESA_PASSKEY=your_passkey
MPESA_SANDBOX=true

# Stripe Settings
STRIPE_KEY=your_publishable_key
STRIPE_SECRET=your_secret_key
STRIPE_WEBHOOK_SECRET=your_webhook_secret
```

#### College Information
```env
COLLEGE_NAME="Edulink International College Nairobi"
COLLEGE_CURRENCY="KES"
COLLEGE_TIMEZONE="Africa/Nairobi"
```

## 🗄️ Database Schema

### Core Tables

#### Students
- Personal information and authentication
- Enrollment status and academic details
- Financial summary and payment tracking

#### Courses
- Course information and requirements
- Duration, credits, and academic details
- Status management (active/inactive)

#### Semesters
- Academic period management
- Start/end dates and registration periods
- Course-semester associations

#### Student Enrollments
- Student-course-semester relationships
- Enrollment status and dates
- Fee assignments and payment tracking

#### Fee Structures
- Dynamic fee configuration per course/semester
- Multiple fee components (tuition, lab, library, etc.)
- Payment terms and installment options

#### Payments
- Transaction records and payment methods
- Status tracking and verification
- Integration with payment gateways

#### Payment Notifications
- System notifications for payment events
- Email and SMS notification tracking
- User acknowledgment status

#### Admins
- Administrative user management
- Role-based permissions
- Activity tracking and security features

## 👥 User Roles & Permissions

### Student Role
- View personal dashboard and enrollment information
- Make payments and view payment history
- Update profile information
- Download receipts and statements
- Receive notifications about payment status

### Admin Roles

#### Super Admin
- Full system access and configuration
- User management and role assignment
- System settings and maintenance
- All reporting and analytics features

#### Finance Officer
- Payment processing and verification
- Financial reporting and analytics
- Fee structure management
- Refund processing

#### Registrar
- Student enrollment and management
- Course and semester administration
- Academic record management
- Student communication

#### Academic Officer
- Course management and scheduling
- Student academic progress tracking
- Enrollment oversight
- Academic reporting

## 💳 Payment Integration

### Supported Payment Methods

#### M-Pesa Integration
- Real-time STK Push payments
- Automatic payment verification
- Transaction status tracking
- Webhook handling for payment updates

#### Stripe Integration
- International card payments
- Secure payment processing
- Subscription and recurring payments
- Comprehensive webhook handling

#### Bank Transfer
- Manual payment verification
- Upload and tracking system
- Administrative approval workflow
- Receipt generation

#### Cash Payments
- In-person payment recording
- Receipt generation and tracking
- Administrative verification
- Audit trail maintenance

### Payment Flow
1. Student initiates payment from portal
2. Payment gateway processes transaction
3. Webhook confirms payment status
4. System updates student balance
5. Receipt generated and emailed
6. Notifications sent to relevant parties

## 🔒 Security

### Current Security Measures
- Multi-guard authentication (Student/Admin)
- Password hashing with bcrypt
- CSRF protection on forms
- Input validation and sanitization
- Role-based access control
- Session management and timeout

### Security Improvements Applied
- Fixed hardcoded credentials in authentication
- Enhanced input validation for SQL injection prevention
- Improved CSRF protection implementation
- Secure cookie configuration
- Configuration-based security settings

### Recommended Security Enhancements
- Implement comprehensive SQL injection prevention
- Add rate limiting for authentication attempts
- Enable two-factor authentication
- Implement comprehensive audit logging
- Add file upload security validation
- Regular security vulnerability scanning

## 🧪 Testing

### Running Tests
```bash
# Run all tests
composer test

# Run specific test suite
php artisan test --testsuite=Feature

# Run tests with coverage
php artisan test --coverage
```

### Test Structure
- **Feature Tests**: End-to-end functionality testing
- **Unit Tests**: Individual component testing
- **Database Tests**: Model and migration testing

## 🚀 Deployment

### Production Deployment Steps

1. **Server Requirements**
   - PHP 8.2+ with required extensions
   - MySQL 8.0+
   - Web server (Apache/Nginx)
   - SSL certificate for HTTPS

2. **Environment Setup**
   ```bash
   # Set production environment
   APP_ENV=production
   APP_DEBUG=false
   
   # Configure production database
   DB_HOST=your_production_host
   DB_DATABASE=your_production_database
   
   # Set up payment gateway credentials
   MPESA_SANDBOX=false
   ```

3. **Deployment Commands**
   ```bash
   # Install dependencies
   composer install --optimize-autoloader --no-dev
   
   # Run migrations
   php artisan migrate --force
   
   # Cache configuration
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   
   # Build production assets
   npm run build
   ```

4. **Web Server Configuration**
   - Point document root to `public/` directory
   - Configure URL rewriting for Laravel
   - Set appropriate file permissions
   - Enable HTTPS and security headers

### Performance Optimization
- Enable OPcache for PHP
- Configure Redis for caching and sessions
- Implement CDN for static assets
- Database query optimization
- Image optimization and compression

## 📈 Current Progress

### ✅ Completed Features
- **Authentication System**: Multi-guard authentication for students and admins
- **Student Portal**: Dashboard, profile management, payment interface
- **Admin Portal**: User management, course administration, payment processing
- **Payment Integration**: M-Pesa and Stripe integration with webhook handling
- **Database Schema**: Complete database structure with relationships
- **Fee Management**: Dynamic fee structures and payment tracking
- **Reporting**: Basic financial and student reports
- **Security Fixes**: Addressed critical hardcoded credential vulnerabilities

### 🚧 In Progress
- **Advanced Reporting**: Enhanced analytics and dashboard features
- **Notification System**: Email and SMS notification implementation
- **Mobile Responsiveness**: Improved mobile user experience
- **API Development**: RESTful API for mobile app integration

### 📋 Planned Features
- **Mobile Application**: Native mobile app for students
- **Advanced Analytics**: Machine learning-based insights
- **Integration APIs**: Third-party system integrations
- **Multi-language Support**: Internationalization features
- **Advanced Security**: Two-factor authentication and audit logging

### 🐛 Known Issues
- Multiple SQL injection vulnerabilities requiring remediation
- CSRF protection gaps in route definitions
- File upload security validation needed
- Cross-site scripting prevention required
- Comprehensive input validation implementation pending

## 🤝 Contributing

### Development Workflow
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Code Standards
- Follow PSR-12 coding standards
- Use Laravel best practices
- Write comprehensive tests for new features
- Document all public methods and classes
- Follow conventional commit messages

### Development Environment
```bash
# Start development environment
composer run dev

# Run code formatting
./vendor/bin/pint

# Run tests before committing
composer test
```

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Support

For support and questions:
- **Email**: support@edulink.ac.ke
- **Phone**: +254700000000
- **Website**: https://edulink.ac.ke

## 🙏 Acknowledgments

- Laravel Framework and community
- Bootstrap for UI components
- Payment gateway providers (Safaricom M-Pesa, Stripe)
- Open source contributors and maintainers

---

**Edulink SmartFees** - Streamlining education fee management for the digital age.