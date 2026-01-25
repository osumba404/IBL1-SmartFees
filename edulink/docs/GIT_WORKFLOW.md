# Git Workflow & Collaboration Guide

## Branch Strategy

### Main Branches

- **`main`**: Production-ready code. Always stable and deployable.
- **`develop`**: Development branch. Integration branch for features.

### Supporting Branches

- **`feature/*`**: New features
- **`fix/*`**: Bug fixes
- **`hotfix/*`**: Critical production fixes
- **`docs/*`**: Documentation updates
- **`refactor/*`**: Code refactoring
- **`test/*`**: Test additions/updates

## Workflow

### Feature Development

1. **Create feature branch from develop**
   ```bash
   git checkout develop
   git pull origin develop
   git checkout -b feature/payment-reminder-system
   ```

2. **Make changes and commit**
   ```bash
   git add .
   git commit -m "feat(payment): add automated reminder system"
   ```

3. **Push to remote**
   ```bash
   git push origin feature/payment-reminder-system
   ```

4. **Create Pull Request**
   - Target: `develop`
   - Fill out PR template
   - Request review
   - Wait for approval

5. **Merge after approval**
   - Use "Squash and merge"
   - Delete feature branch

### Bug Fixes

1. **Create fix branch**
   ```bash
   git checkout develop
   git checkout -b fix/login-redirect-issue
   ```

2. **Fix and commit**
   ```bash
   git commit -m "fix(auth): resolve login redirect issue"
   ```

3. **Create PR to develop**

### Hotfixes (Production Issues)

1. **Create hotfix from main**
   ```bash
   git checkout main
   git checkout -b hotfix/critical-security-patch
   ```

2. **Fix and commit**
   ```bash
   git commit -m "hotfix(security): patch SQL injection vulnerability"
   ```

3. **Merge to both main and develop**
   ```bash
   git checkout main
   git merge hotfix/critical-security-patch
   git checkout develop
   git merge hotfix/critical-security-patch
   ```

## Commit Message Convention

We follow [Conventional Commits](https://www.conventionalcommits.org/):

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Test additions/updates
- `chore`: Maintenance tasks
- `perf`: Performance improvements
- `ci`: CI/CD changes

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

### Before Creating PR

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

4. **Update documentation** if needed

### PR Requirements

- [ ] All tests pass
- [ ] Code follows style guidelines
- [ ] Documentation updated
- [ ] No merge conflicts
- [ ] PR description filled out
- [ ] Related issues linked
- [ ] Screenshots for UI changes

### PR Review

- At least **one approval** required
- Address all review comments
- Update PR based on feedback
- Re-request review after changes

## Branch Protection Rules

### Main Branch

- Require pull request reviews (1 approval)
- Require status checks to pass
- Require branches to be up to date
- Restrict force pushes
- Restrict deletions

### Develop Branch

- Require pull request reviews (1 approval)
- Require status checks to pass
- Allow force pushes (for rebasing)

## Collaboration Guidelines

### Code Review

**As a Reviewer:**
- Be constructive and respectful
- Focus on code quality, not personal preferences
- Explain reasoning for suggestions
- Approve when satisfied

**As an Author:**
- Respond to all comments
- Be open to feedback
- Ask questions if unclear
- Thank reviewers

### Communication

- Use GitHub Issues for bugs/features
- Use PR comments for code discussions
- Use GitHub Discussions for questions
- Keep conversations professional

### Conflict Resolution

1. **Rebase your branch**
   ```bash
   git checkout feature/your-feature
   git rebase develop
   ```

2. **Resolve conflicts**
   - Edit conflicted files
   - Stage resolved files
   - Continue rebase

3. **Force push** (if needed)
   ```bash
   git push origin feature/your-feature --force-with-lease
   ```

## Best Practices

### Do's

✅ Create feature branches for all changes
✅ Write clear commit messages
✅ Keep commits focused and atomic
✅ Test before committing
✅ Update documentation
✅ Review your own code before PR
✅ Keep branches up to date
✅ Delete merged branches

### Don'ts

❌ Commit directly to main/develop
❌ Commit broken code
❌ Write vague commit messages
❌ Force push to main/develop
❌ Leave branches unmerged
❌ Ignore review feedback
❌ Commit sensitive data
❌ Commit large binary files

## Git Aliases (Optional)

Add to `~/.gitconfig`:

```ini
[alias]
    co = checkout
    br = branch
    ci = commit
    st = status
    unstage = reset HEAD --
    last = log -1 HEAD
    visual = !gitk
    lg = log --oneline --decorate --graph --all
```

## Troubleshooting

### Undo Last Commit

```bash
# Keep changes
git reset --soft HEAD~1

# Discard changes
git reset --hard HEAD~1
```

### Change Last Commit Message

```bash
git commit --amend -m "New commit message"
```

### Stash Changes

```bash
# Save changes
git stash

# Apply stashed changes
git stash pop

# List stashes
git stash list
```

### Clean Working Directory

```bash
# Remove untracked files
git clean -fd

# Dry run first
git clean -fdn
```

## Resources

- [Git Documentation](https://git-scm.com/doc)
- [Conventional Commits](https://www.conventionalcommits.org/)
- [GitHub Flow](https://guides.github.com/introduction/flow/)
- [Laravel Contributing Guide](https://laravel.com/docs/contributions)
