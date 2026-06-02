# ProjectPilot

## Overview

ProjectPilot is a modern Project Management System built with Laravel. It helps teams manage projects, tasks, team members, clients, and project progress from a centralized dashboard.

The goal of ProjectPilot is to provide a simple, scalable, and efficient platform for managing software development projects and team collaboration.

---

## Features

### Authentication & Authorization

* User Registration
* Login & Logout
* Password Reset
* Profile Management
* Role-Based Access Control (RBAC)

### User Roles

* Super Admin
* Project Manager
* Team Member
* Client

### Dashboard

* Total Projects
* Active Projects
* Completed Projects
* Pending Projects
* Recent Activities
* Project Statistics

### Project Management

* Create Projects
* Edit Projects
* Delete Projects
* Project Status Management
* Project Priority Management
* Project Details View
* Project Search & Filters

### Team Management

* Assign Team Members to Projects
* Manage Project Teams
* User Assignment Tracking

### Task Management

* Create Tasks
* Assign Tasks
* Task Priorities
* Task Status Tracking
* Due Dates
* Task Progress Monitoring

### Task Collaboration

* Task Comments
* Activity History
* Attachments

### File Management

* Upload Project Files
* Manage Documents
* Download Attachments

### Reporting

* Project Progress Reports
* Team Performance Reports
* Task Completion Reports

### Notifications

* Task Assignment Notifications
* Project Updates
* Deadline Reminders

---

## Technology Stack

### Backend

* Laravel 12
* PHP 8.3+
* MySQL 8

### Frontend

* Blade Templates
* Bootstrap 5
* JavaScript
* jQuery

### Packages

* Spatie Laravel Permission
* Laravel Breeze
* Laravel Reverb (Optional)
* Laravel Excel (Future)

---

## Project Structure

```text
app/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Middleware/
│
├── Models/
│
├── Services/
│
├── Policies/
│
└── Providers/

resources/
├── views/
│   ├── layouts/
│   ├── dashboard/
│   ├── projects/
│   ├── tasks/
│   └── components/

routes/
├── web.php

database/
├── migrations/
├── seeders/
```

---

## Database Modules

### Core Tables

```text
users
roles
permissions

projects
project_members

tasks
task_comments
task_attachments

clients

notifications

activity_logs
```

---

## Project Status

Available project statuses:

```text
Pending
In Progress
On Hold
Completed
Cancelled
```

---

## Task Status

Available task statuses:

```text
Todo
In Progress
Testing
Done
```

---

## Task Priority

Available task priorities:

```text
Low
Medium
High
Critical
```

---

## Development Roadmap

### Phase 1 (MVP)

* Authentication
* Roles & Permissions
* Dashboard
* Project CRUD
* Project Members
* Task CRUD

### Phase 2

* Task Comments
* File Uploads
* Notifications
* Activity Logs

### Phase 3

* Time Tracking
* Leave Management
* Attendance System
* Reporting Module

### Phase 4

* Client Portal
* Realtime Chat
* Kanban Board
* Gantt Chart
* API Integration

---

## Installation

### Clone Repository

```bash
git clone <repository-url>
cd ProjectPilot
```

### Install Dependencies

```bash
composer install
npm install
```

### Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### Configure Database

Update `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=projectpilot
DB_USERNAME=root
DB_PASSWORD=
```

### Run Migrations

```bash
php artisan migrate
```

### Seed Roles

```bash
php artisan db:seed
```

### Start Development Server

```bash
php artisan serve
```

Visit:

```text
http://127.0.0.1:8000
```

---

## Future Enhancements

* Multi-Tenant Support
* Mobile Application
* Advanced Analytics
* Calendar Integration
* Email Integration
* Slack Integration
* AI-Based Project Insights

---

## License

This project is proprietary and intended for internal or commercial use.

---

## Author

Developed and maintained by Kieran.

ProjectPilot is an independent project management platform built using Laravel and modern web technologies.

Built with Laravel ❤️