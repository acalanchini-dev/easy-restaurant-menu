# Contributing to Easy Restaurant Menu

Thank you for considering contributing to Easy Restaurant Menu! This document outlines the guidelines and workflows for contributing to this project.

## Development Environment Setup

1. **Clone the repository**:
   ```
   git clone https://github.com/acalanchini-dev/easy-restaurant-menu.git
   cd easy-restaurant-menu
   ```

2. **Install dependencies**:
   ```
   npm install
   ```

3. **Start development mode**:
   ```
   npm start
   ```

4. **Build for production**:
   ```
   npm run build
   ```

## Coding Standards

This project follows the WordPress coding standards. Please ensure your code adheres to these standards:

- [WordPress PHP Coding Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/)
- [WordPress JavaScript Coding Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/javascript/)
- [WordPress CSS Coding Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/css/)

## Pull Request Process

1. Fork the repository and create your branch from `main`.
2. Make your changes and ensure they follow the coding standards.
3. Ensure your code is properly documented with PHPDoc comments.
4. Update the README.md and documentation with details of changes if applicable.
5. Make sure all tests pass (if applicable).
6. Submit your pull request with a clear description of the changes.

## Commit Message Guidelines

Please use clear and descriptive commit messages. Each commit message should have:

- A brief summary (50 chars or less) in the first line
- A blank line followed by a more detailed explanation if necessary

Example:
```
Add feature to filter menu items by category

- Added new filter dropdown in the menu display
- Updated documentation to reflect the new feature
- Fixed CSS for mobile responsiveness
```

## Translations

If you're adding or modifying strings:

1. Ensure all user-facing strings are internationalized using the appropriate functions (`__()`, `_e()`, etc.).
2. Use 'easy-restaurant-menu' as the text domain for all strings.
3. Consider adding or updating translations if you're fluent in languages other than English.

## Security

Security is a top priority:

- Sanitize all inputs using WordPress sanitization functions
- Validate and escape all data before output
- Use nonces for all forms and AJAX requests
- Always check user capabilities before performing privileged actions

## Questions?

If you have questions or need help, please open an issue on GitHub or contact the project maintainers.

Thank you for contributing to Easy Restaurant Menu! 