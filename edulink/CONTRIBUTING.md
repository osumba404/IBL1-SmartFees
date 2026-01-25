# Contributing to Edulink SmartFees

Thank you for your interest in contributing to Edulink SmartFees! This document provides guidelines and instructions for contributing to the project.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Workflow](#development-workflow)
- [Code Standards](#code-standards)
- [Commit Guidelines](#commit-guidelines)
- [Pull Request Process](#pull-request-process)
- [Testing Requirements](#testing-requirements)
- [Documentation](#documentation)

## Code of Conduct

- Be respectful and inclusive
- Welcome newcomers and help them learn
- Focus on constructive feedback
- Respect different viewpoints and experiences

## Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer 2.x
- Node.js 18+ and NPM
- MySQL 8.0+
- Git

### Setting Up Development Environment

1. **Fork and Clone the Repository**
   ```bash
   git clone https://github.com/YOUR_USERNAME/IBL1-SmartFees.git
   cd IBL1-SmartFees/edulink
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Start Development Server**
   ```bash
   composer run dev
   ```

## Development Workflow

### Branch Strategy

- `main` - Production-ready code
- `develop` - Development branch (default)
- `feature/` - New features
- `fix/` - Bug fixes
- `docs/` - Documentation updates
- `refactor/` - Code refactoring
- `test/` - Test additions/updates

### Creating a Feature Branch

```bash
# Update develop branch
git checkout develop
git pull origin develop

# Create feature branch
git checkout -b feature/your-feature-name

# Or for bug fixes
git checkout -b fix/bug-description
```

### Making Changes

1. Make your changes following the code standards
2. Write or update tests for your changes
3. Ensure all tests pass
4. Update documentation if needed
5. Commit your changes with meaningful messages

## Code Standards

### PHP/Laravel Standards

- Follow **PSR-12** coding standards
- Use **Laravel Pint** for code formatting
- Follow **Laravel best practices**
- Use type hints and return types where possible
- Write self-documenting code with clear variable names

### Code Formatting

Run Laravel Pint before committing:

```bash
./vendor/bin/pint
```

### Naming Conventions

- **Classes**: PascalCase (e.g., `PaymentService`)
- **Methods**: camelCase (e.g., `processPayment`)
- **Variables**: camelCase (e.g., `$paymentAmount`)
- **Constants**: UPPER_SNAKE_CASE (e.g., `MAX_PAYMENT_AMOUNT`)
- **Database tables**: snake_case (e.g., `student_enrollments`)

### Code Structure

- Keep methods focused and single-purpose
- Use dependency injection
- Follow SOLID principles
- Keep controllers thin, move logic to services
- Use Eloquent relationships appropriately

### Example Code Structure

```php
<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Log;

/**
 * Payment processing service
 * 
 * Handles payment processing logic
 */
class PaymentService
{
    /**
     * Process payment
     * 
     * @param array $paymentData
     * @return array
     */
    public function processPayment(array $paymentData): array
    {
        // Implementation
    }
}
```

## Commit Guidelines

We follow [Conventional Commits](https://www.conventionalcommits.org/) specification.

### Commit Message Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

### Examples

```bash
feat(payment): add M-Pesa payment integration

- Implement STK Push functionality
- Add payment callback handling
- Update payment service

Closes #123

fix(auth): resolve login redirect issue

The login redirect was not working for students.
Fixed by updating the redirect logic in StudentAuthController.

docs(readme): update installation instructions

Add Docker setup instructions and environment variables guide.
```

## Pull Request Process

### Before Submitting

1. **Update your branch**
   ```bash
   git checkout develop
   git pull origin develop
   git checkout feature/your-feature
   git rebase develop
   ```

2. **Run tests**
   ```bash
   composer test
   ```

3. **Check code style**
   ```bash
   ./vendor/bin/pint --test
   ```

4. **Update documentation** if your changes require it

### Submitting a Pull Request

1. **Push your branch**
   ```bash
   git push origin feature/your-feature
   ```

2. **Create Pull Request** on GitHub
   - Use a clear, descriptive title
   - Fill out the PR template completely
   - Link related issues
   - Add screenshots for UI changes

3. **PR Description Template**
   ```markdown
   ## Description
   Brief description of changes
   
   ## Type of Change
   - [ ] Bug fix
   - [ ] New feature
   - [ ] Breaking change
   - [ ] Documentation update
   
   ## Testing
   - [ ] Unit tests added/updated
   - [ ] Feature tests added/updated
   - [ ] Manual testing completed
   
   ## Checklist
   - [ ] Code follows style guidelines
   - [ ] Self-review completed
   - [ ] Comments added for complex code
   - [ ] Documentation updated
   - [ ] No new warnings generated
   - [ ] Tests pass locally
   ```

### PR Review Process

1. **Automated Checks**
   - CI/CD pipeline runs automatically
   - Code coverage is checked
   - Code style is validated

2. **Code Review**
   - At least one approval required
   - Address all review comments
   - Update PR based on feedback

3. **Merge**
   - PR will be merged after approval
   - Use "Squash and merge" for feature branches
   - Delete branch after merge

## Testing Requirements

### Writing Tests

- Write tests for all new features
- Write tests for bug fixes
- Aim for ≥60% code coverage
- Use descriptive test names

### Test Structure

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;

class PaymentTest extends TestCase
{
    /** @test */
    public function student_can_make_payment()
    {
        // Arrange
        $student = Student::factory()->create();
        
        // Act
        $response = $this->actingAs($student, 'student')
            ->post('/student/payments/initiate', [
                'amount' => 1000,
                'payment_method' => 'mpesa'
            ]);
        
        // Assert
        $response->assertStatus(200);
    }
}
```

### Running Tests

```bash
# Run all tests
composer test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage

# Run specific test
php artisan test --filter PaymentTest
```

## Documentation

### Code Documentation

- Add PHPDoc comments to all public methods
- Document complex logic with inline comments
- Keep comments up-to-date with code changes

### Example Documentation

```php
/**
 * Process payment through payment gateway
 * 
 * @param StudentEnrollment $enrollment
 * @param float $amount
 * @param string $paymentMethod
 * @param array $paymentData
 * @return array Returns array with 'success' and 'message' keys
 * @throws \InvalidArgumentException If payment method is invalid
 */
public function processPayment(
    StudentEnrollment $enrollment,
    float $amount,
    string $paymentMethod,
    array $paymentData = []
): array {
    // Implementation
}
```

### Updating Documentation

- Update README.md for user-facing changes
- Update API documentation if endpoints change
- Add examples for new features
- Keep installation instructions current

## Getting Help

- **Issues**:** Open an issue for bugs or feature requests
- **Discussions**: Use GitHub Discussions for questions
- **Email**: support@edulink.ac.ke

## Recognition

Contributors will be recognized in:
- README.md contributors section
- Release notes
- Project documentation

Thank you for contributing to Edulink SmartFees!
