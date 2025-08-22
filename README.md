# Laravel Library Management System

A simple and efficient Library Management System built with Laravel framework using clean architecture principles and Object-Oriented Programming.

## Features

- **Book Management**: Add, view, and manage book availability
- **Member Management**: Register members and track their borrowing activity
- **Loan System**: Handle book borrowing and returns with automatic due date calculation
- **Business Rules**: 
  - 5-book borrowing limit per member
  - 14-day loan period
  - Automatic overdue tracking

## Tech Stack

- **Framework**: Laravel
- **Database**: SQLite (configurable to MySQL)
- **PHP Version**: 8.1+
- **Architecture**: Clean OOP with Eloquent ORM
- **API**: RESTful endpoints

## Installation

1. Clone the repository
```bash
git clone <your-repo-url>
cd Dev_php
```

2. Set up the database
```bash
php setup_database.php
```

## API Endpoints

### Books
- `POST /api/books` - Add a new book
- `GET /api/books` - Get all books
- `GET /api/book/{id}/availability` - Check book availability

### Members
- `POST /api/members` - Add a new member
- `GET /api/members` - Get all members
- `PUT /api/members/{id}` - Update member information
- `DELETE /api/members/{id}` - Delete a member

### Loans
- `POST /api/borrow` - Borrow a book
- `POST /api/return` - Return a book
- `GET /api/loans` - Get all loans
- `GET /api/check-overdue` - Check overdue books

## Project Structure

```
├── app/
│   ├── Models/          # Eloquent models
│   ├── Http/Controllers/ # API controllers
│   ├── Http/Requests/   # Form validation
│   └── Enums/           # Book status enum
├── database/
│   ├── migrations/      # Database schema
│   ├── factories/       # Model factories
│   └── seeders/         # Sample data
└── routes/api.php       # API routes
```

## Models

- **Book**: Manages book entities with status tracking
- **Member**: Handles library member information
- **Loan**: Tracks borrowing transactions and due dates

## Business Logic

The system enforces key library rules:
- Members can borrow up to 5 books simultaneously
- Each loan has a 14-day return period
- Books are automatically marked as borrowed/available
- Overdue tracking for late returns

## Development

This project follows Laravel best practices with:
- Clean getter/setter methods
- Eloquent relationships
- Form validation
- RESTful API design
- Professional code structure without excessive comments
