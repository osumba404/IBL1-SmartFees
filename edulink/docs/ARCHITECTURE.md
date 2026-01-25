# System Architecture

## Overview

Edulink SmartFees follows a **layered architecture** pattern with clear separation of concerns. The system is built using Laravel framework, which provides a solid foundation for MVC (Model-View-Controller) architecture.

## High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        Client Layer                          │
│  (Web Browsers, Mobile Devices)                             │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                    Presentation Layer                        │
│  (Blade Templates, Bootstrap, JavaScript)                   │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                    Application Layer                        │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │ Controllers  │  │  Middleware  │  │   Routes     │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                      Service Layer                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │ Payment      │  │ Notification │  │   AI        │      │
│  │ Service      │  │   Service    │  │ Analytics   │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
│  ┌──────────────┐  ┌──────────────┐                        │
│  │ M-Pesa       │  │   Stripe     │                        │
│  │ Service      │  │   Service    │                        │
│  └──────────────┘  └──────────────┘                        │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                      Domain Layer                           │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   Models     │  │  Eloquent    │  │ Relationships│      │
│  │  (Student,   │  │   ORM        │  │              │      │
│  │  Payment,    │  │              │  │              │      │
│  │  Course)     │  │              │  │              │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                    Infrastructure Layer                     │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   MySQL      │  │    Redis     │  │   File       │      │
│  │  Database    │  │   Cache      │  │   Storage    │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
```

## Component Architecture

### 1. Presentation Layer

**Responsibilities:**
- User interface rendering
- User interaction handling
- Form validation (client-side)
- Responsive design

**Technologies:**
- Blade templating engine
- Bootstrap 5
- Vanilla JavaScript (ES6+)
- CSS3 with custom properties

### 2. Application Layer

**Controllers:**
- Handle HTTP requests
- Validate input
- Coordinate between services and views
- Return responses

**Middleware:**
- Authentication (StudentAuth, AdminAuth)
- Authorization (permission checks)
- Request validation
- CSRF protection

**Routes:**
- Define application endpoints
- Apply middleware
- Route to appropriate controllers

### 3. Service Layer

**PaymentService:**
- Coordinates payment processing
- Handles payment method selection
- Manages payment verification
- Processes refunds

**MpesaService:**
- M-Pesa STK Push integration
- Callback handling
- Transaction verification

**StripeService:**
- Stripe payment intent creation
- Webhook handling
- Payment confirmation

**NotificationService:**
- Email notifications
- SMS notifications
- In-app notifications

**AIAnalyticsService:**
- Payment behavior analysis
- Fraud detection
- Predictive analytics

### 4. Domain Layer

**Models:**
- Student: Student information and authentication
- Payment: Payment transactions
- Course: Academic programs
- StudentEnrollment: Student-course relationships
- Admin: Administrative users
- FeeStructure: Fee configurations

**Relationships:**
- Student hasMany Enrollments
- Student hasMany Payments
- Enrollment belongsTo Course
- Payment belongsTo Student
- Payment belongsTo Enrollment

### 5. Infrastructure Layer

**Database (MySQL):**
- Persistent data storage
- Transaction management
- Data integrity

**Cache (Redis):**
- Session storage
- Application cache
- Queue management

**File Storage:**
- Profile pictures
- Receipts/statements
- Uploaded documents

## Authentication Architecture

### Multi-Guard Authentication

```
┌─────────────────────────────────────────┐
│         Authentication Layer            │
├─────────────────────────────────────────┤
│                                         │
│  ┌──────────────┐    ┌──────────────┐  │
│  │ Student      │    │   Admin      │  │
│  │ Guard        │    │   Guard      │  │
│  └──────────────┘    └──────────────┘  │
│         │                   │            │
│         ▼                   ▼            │
│  ┌──────────────┐    ┌──────────────┐  │
│  │ Student      │    │   Admin      │  │
│  │ Model        │    │   Model      │  │
│  └──────────────┘    └──────────────┘  │
│                                         │
└─────────────────────────────────────────┘
```

**Features:**
- Separate authentication for students and admins
- Role-based access control (RBAC)
- Permission-based authorization
- Session management

## Payment Processing Architecture

```
┌─────────────────────────────────────────────────┐
│            Payment Request Flow                  │
└────────────────┬────────────────────────────────┘
            │
            ▼
┌─────────────────────────────────────────────────┐
│         PaymentService                          │
│  ┌──────────────────────────────────────────┐  │
│  │  Strategy Pattern: Payment Method Selection│  │
│  └──────────────────────────────────────────┘  │
└───┬──────────┬──────────┬──────────┬──────────┘
    │          │          │          │
    ▼          ▼          ▼          ▼
┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐
│ M-Pesa │ │ Stripe │ │  Bank  │ │  Cash  │
│Service │ │Service │ │Transfer│ │Payment │
└───┬────┘ └───┬────┘ └───┬────┘ └───┬────┘
    │          │          │          │
    ▼          ▼          ▼          ▼
┌──────────────────────────────────────────────┐
│         Payment Gateway APIs                 │
└──────────────────────────────────────────────┘
```

## Data Flow

### Student Payment Flow

1. **Request Initiation**
   - Student submits payment form
   - Controller validates input
   - PaymentService receives request

2. **Payment Processing**
   - PaymentService creates payment record
   - Selects appropriate payment gateway service
   - Initiates payment with gateway

3. **Gateway Response**
   - Gateway processes payment
   - Webhook/callback received
   - PaymentService verifies payment

4. **Completion**
   - Payment status updated
   - Enrollment balance updated
   - Notifications sent
   - Receipt generated

## Security Architecture

### Security Layers

1. **Authentication Layer**
   - Password hashing (bcrypt)
   - Multi-guard authentication
   - Session management

2. **Authorization Layer**
   - Role-based access control
   - Permission checks
   - Route protection

3. **Input Validation Layer**
   - Form validation
   - SQL injection prevention
   - XSS protection

4. **Communication Layer**
   - HTTPS/TLS
   - CSRF protection
   - Secure headers

## Scalability Considerations

### Horizontal Scaling
- Stateless application design
- Session storage in Redis
- Database connection pooling

### Performance Optimization
- Query optimization
- Caching strategy
- Asset compression
- CDN for static assets

## Deployment Architecture

```
┌─────────────────────────────────────────┐
│         Load Balancer                   │
└──────────────┬──────────────────────────┘
               │
       ┌───────┴───────┐
       │               │
       ▼               ▼
┌──────────┐     ┌──────────┐
│  App     │     │  App     │
│ Server 1 │     │ Server 2 │
└────┬─────┘     └────┬─────┘
     │                │
     └────────┬───────┘
              │
              ▼
     ┌─────────────────┐
     │  Database       │
     │  (MySQL)        │
     └─────────────────┘
              │
              ▼
     ┌─────────────────┐
     │  Cache (Redis)  │
     └─────────────────┘
```

## Technology Stack

- **Backend Framework:** Laravel 12.x
- **Database:** MySQL 8.0
- **Cache:** Redis
- **Web Server:** Nginx
- **PHP Version:** 8.2+
- **Frontend:** Bootstrap 5, Vanilla JavaScript
- **Payment Gateways:** M-Pesa, Stripe, PayPal
