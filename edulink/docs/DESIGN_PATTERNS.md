# Design Patterns & SOLID Principles

## Overview

Edulink SmartFees implements several design patterns and follows SOLID principles to ensure maintainable, scalable, and testable code.

## Design Patterns

### 1. Service Pattern

**Purpose:** Encapsulate business logic and coordinate between different components.

**Implementation:**

```php
// app/Services/PaymentService.php
class PaymentService
{
    protected MpesaService $mpesaService;
    protected StripeService $stripeService;

    public function processPayment(
        StudentEnrollment $enrollment,
        float $amount,
        string $paymentMethod,
        array $paymentData = []
    ): array {
        // Coordinate payment processing
        // Delegate to specific payment gateway services
    }
}
```

**Benefits:**
- Separation of concerns
- Reusable business logic
- Easier testing
- Centralized payment processing

**Usage:**
- PaymentService coordinates all payment operations
- NotificationService handles all notifications
- AIAnalyticsService manages analytics

### 2. Strategy Pattern

**Purpose:** Allow runtime selection of payment methods without modifying existing code.

**Implementation:**

```php
// PaymentService uses Strategy Pattern
$result = match($paymentMethod) {
    'mpesa' => $this->processMpesaPayment($payment, $paymentData),
    'stripe' => $this->processStripePayment($payment, $paymentData),
    'bank_transfer' => $this->processBankTransferPayment($payment, $paymentData),
    'cash' => $this->processCashPayment($payment, $paymentData),
    default => throw new \InvalidArgumentException("Unsupported payment method")
};
```

**Benefits:**
- Easy to add new payment methods
- Open/Closed Principle compliance
- No code duplication
- Flexible payment processing

**Usage:**
- Payment method selection
- Notification channel selection (email/SMS)
- Report generation strategies

### 3. Repository Pattern

**Purpose:** Abstract data access layer using Eloquent ORM.

**Implementation:**

```php
// Eloquent Models act as Repositories
class Student extends Authenticatable
{
    // Model acts as repository
    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }
    
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
```

**Benefits:**
- Centralized data access
- Easy to mock in tests
- Consistent query interface
- Relationship management

**Usage:**
- All Eloquent models
- Database queries
- Relationship definitions

### 4. Factory Pattern

**Purpose:** Create objects without specifying exact classes.

**Implementation:**

```php
// Database Factories
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomFloat(2, 1000, 50000),
            'payment_method' => $this->faker->randomElement(['mpesa', 'stripe']),
            'status' => 'pending',
        ];
    }
    
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'is_verified' => true,
        ]);
    }
}

// Usage
Payment::factory()->completed()->create();
```

**Benefits:**
- Flexible object creation
- Test data generation
- Consistent object initialization
- State variations

**Usage:**
- Test factories (StudentFactory, PaymentFactory, etc.)
- Payment creation with different states
- Model instantiation

### 5. Observer Pattern

**Purpose:** Notify multiple objects about state changes.

**Implementation:**

```php
// Laravel Events and Listeners
class PaymentCompleted
{
    public function __construct(public Payment $payment) {}
}

class SendPaymentConfirmation
{
    public function handle(PaymentCompleted $event): void
    {
        // Send email notification
        // Send SMS notification
        // Update enrollment balance
    }
}
```

**Benefits:**
- Loose coupling
- Event-driven architecture
- Easy to add new listeners
- Asynchronous processing

**Usage:**
- Payment completion events
- Enrollment events
- Notification triggers

### 6. Dependency Injection

**Purpose:** Provide dependencies from outside the class.

**Implementation:**

```php
class PaymentService
{
    public function __construct(
        protected MpesaService $mpesaService,
        protected StripeService $stripeService
    ) {}
}

// Laravel automatically resolves dependencies
$paymentService = app(PaymentService::class);
```

**Benefits:**
- Loose coupling
- Easy testing (can inject mocks)
- Flexible configuration
- SOLID compliance

**Usage:**
- Service classes
- Controllers
- Middleware
- Commands

## SOLID Principles

### 1. Single Responsibility Principle (SRP)

**Definition:** A class should have only one reason to change.

**Implementation:**

```php
// ✅ Good: Each service has one responsibility
class PaymentService
{
    // Only handles payment processing
}

class NotificationService
{
    // Only handles notifications
}

class MpesaService
{
    // Only handles M-Pesa integration
}

// ❌ Bad: One class doing multiple things
class PaymentAndNotificationService
{
    // Handles payments AND notifications
}
```

**Examples in Codebase:**
- `PaymentService`: Only payment processing
- `NotificationService`: Only notifications
- `MpesaService`: Only M-Pesa integration
- `AIAnalyticsService`: Only analytics

### 2. Open/Closed Principle (OCP)

**Definition:** Software entities should be open for extension but closed for modification.

**Implementation:**

```php
// ✅ Good: Easy to add new payment methods without modifying existing code
class PaymentService
{
    public function processPayment(string $method, ...): array
    {
        return match($method) {
            'mpesa' => $this->processMpesaPayment(...),
            'stripe' => $this->processStripePayment(...),
            'paypal' => $this->processPaypalPayment(...), // New method added
            default => throw new \InvalidArgumentException()
        };
    }
}

// Adding new payment method doesn't require changing existing methods
```

**Examples:**
- Payment methods can be added without modifying PaymentService
- Notification channels can be extended
- Report types can be added

### 3. Liskov Substitution Principle (LSP)

**Definition:** Objects of a superclass should be replaceable with objects of its subclasses.

**Implementation:**

```php
// All payment services implement similar interface
interface PaymentGatewayInterface
{
    public function processPayment(float $amount, array $data): array;
}

class MpesaService implements PaymentGatewayInterface
{
    public function processPayment(float $amount, array $data): array
    {
        // M-Pesa specific implementation
    }
}

class StripeService implements PaymentGatewayInterface
{
    public function processPayment(float $amount, array $data): array
    {
        // Stripe specific implementation
    }
}

// Can be used interchangeably
$gateway = new MpesaService(); // or StripeService
$result = $gateway->processPayment(1000, []);
```

**Examples:**
- Payment gateway services are interchangeable
- Notification services follow same contract
- Report generators have consistent interface

### 4. Interface Segregation Principle (ISP)

**Definition:** Clients should not be forced to depend on interfaces they don't use.

**Implementation:**

```php
// ✅ Good: Focused interfaces
interface PaymentProcessor
{
    public function process(float $amount): array;
}

interface PaymentVerifier
{
    public function verify(string $transactionId): bool;
}

// ❌ Bad: Fat interface
interface PaymentInterface
{
    public function process(float $amount): array;
    public function verify(string $transactionId): bool;
    public function refund(int $paymentId): array;
    public function generateReceipt(int $paymentId): string;
    // Many methods that not all classes need
}
```

**Examples:**
- Services have focused responsibilities
- Controllers use only needed services
- Models expose only relevant methods

### 5. Dependency Inversion Principle (DIP)

**Definition:** High-level modules should not depend on low-level modules. Both should depend on abstractions.

**Implementation:**

```php
// ✅ Good: Depend on abstractions
class PaymentService
{
    public function __construct(
        protected PaymentGatewayInterface $gateway // Interface, not concrete class
    ) {}
}

// ❌ Bad: Depend on concrete classes
class PaymentService
{
    public function __construct(
        protected MpesaService $mpesaService // Concrete class
    ) {}
}
```

**Examples:**
- Services depend on interfaces
- Controllers depend on service abstractions
- Tests can inject mocks easily

## Code Organization

### Directory Structure

```
app/
├── Console/Commands/     # Artisan commands
├── Http/
│   ├── Controllers/     # Request handlers
│   └── Middleware/      # Request filters
├── Models/              # Domain models (Repository pattern)
├── Services/            # Business logic (Service pattern)
└── Providers/          # Service providers
```

### Naming Conventions

- **Classes:** PascalCase (e.g., `PaymentService`)
- **Methods:** camelCase (e.g., `processPayment`)
- **Variables:** camelCase (e.g., `$paymentAmount`)
- **Constants:** UPPER_SNAKE_CASE (e.g., `MAX_PAYMENT_AMOUNT`)
- **Database:** snake_case (e.g., `student_enrollments`)

## Best Practices

### 1. Keep Controllers Thin

```php
// ✅ Good
public function store(Request $request)
{
    $this->paymentService->processPayment(...);
    return redirect()->route('payments.index');
}

// ❌ Bad
public function store(Request $request)
{
    // 100 lines of business logic here
}
```

### 2. Use Type Hints

```php
// ✅ Good
public function processPayment(
    StudentEnrollment $enrollment,
    float $amount,
    string $paymentMethod
): array {
    // Implementation
}

// ❌ Bad
public function processPayment($enrollment, $amount, $paymentMethod)
{
    // Implementation
}
```

### 3. Handle Errors Gracefully

```php
// ✅ Good
try {
    $result = $this->mpesaService->initiateSTKPush(...);
} catch (\Exception $e) {
    Log::error('M-Pesa payment failed', ['error' => $e->getMessage()]);
    return ['success' => false, 'message' => 'Payment processing failed'];
}
```

### 4. Use Dependency Injection

```php
// ✅ Good
public function __construct(
    protected PaymentService $paymentService
) {}

// ❌ Bad
public function process()
{
    $paymentService = new PaymentService(); // Hard dependency
}
```

## Testing Patterns

### 1. Mock External Services

```php
$mpesaServiceMock = Mockery::mock(MpesaService::class);
$mpesaServiceMock->shouldReceive('initiateSTKPush')
    ->once()
    ->andReturn(['success' => true]);
```

### 2. Use Factories for Test Data

```php
$student = Student::factory()->create();
$payment = Payment::factory()->completed()->create();
```

### 3. Test Behavior, Not Implementation

```php
// ✅ Good: Test what the method does
$this->assertTrue($payment->is_verified);

// ❌ Bad: Test implementation details
$this->assertEquals('completed', $payment->status);
```

## Conclusion

The application follows SOLID principles and implements several design patterns to ensure:

- **Maintainability:** Easy to understand and modify
- **Scalability:** Can grow without major refactoring
- **Testability:** Easy to write and run tests
- **Flexibility:** Easy to extend with new features
- **Reliability:** Consistent behavior and error handling
