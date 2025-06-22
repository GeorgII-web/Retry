# Development

This document contains instructions for setting up the development environment and contributing to the Retry package.

## Docker Setup

This project includes a Docker setup for development. It provides a PHP 8.2 environment with Xdebug enabled.

### Prerequisites

- Docker
- Docker Compose

### Getting Started

1. Clone the repository:

```bash
git clone https://github.com/georgii-web/retry.git
cd retry
```

2. Build Docker container

```bash
docker-compose up
```

3. Install dependencies:

```bash
composer install
```

### Docker Environment Features

- PHP 8.2 CLI
- Xdebug 3 for debugging and code coverage
- Composer for dependency management
- Custom PHP configuration optimized for development

## Testing

Be sure that all tests are passed by running:

```bash
composer oncommit
```

## Contribution Guidelines

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

Please make sure your code follows the project's coding standards and is covered by tests.