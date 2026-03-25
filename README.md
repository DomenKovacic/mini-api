# Mini Task API

A small Laravel JSON API for managing and processing tasks.

## Features

- CRUD API for tasks
- Filtering and sorting on task listing
- Business rule validation
- Task processing flow:
  - `todo -> in_progress`
  - `in_progress -> done` or `failed`
- SQLite database
- Seed data included
- Feature tests included

## Tech Stack

- PHP
- Laravel
- SQLite
- Laravel testing tools

## Task Fields

Each task contains:

- `id`
- `title`
- `description`
- `status`
- `priority`
- `due_date`
- `external_reference`
- `metadata`
- `created_at`
- `updated_at`

## Business Rules

- A task marked as `done` cannot have a due date in the future
- A task marked as `done` cannot be moved back to `todo`
- `external_reference` must be unique if provided
- A `high` priority task must have a due date

## Architecture

The application is split into clear layers:

- `TaskController` handles HTTP requests and JSON responses
- `StoreTaskRequest` and `UpdateTaskRequest` handle input validation and business-rule validation
- `ProcessTaskAction` contains task processing logic
- `Task` is the Eloquent model for persistence
- migrations define the database structure
- seeders provide sample data
- feature tests cover API behavior and business rules

## Local Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

Application will be available at:

```text
http://127.0.0.1:8000
```

## Running Tests

```bash
php artisan test
```

## Seeding Sample Data

```bash
php artisan db:seed
```

## API Endpoints

### List tasks

```http
GET /api/tasks
```

Optional query params:

- `status`
- `priority`
- `due_before`
- `sort`
- `direction`

Example:

```bash
curl -H "Accept: application/json" "http://127.0.0.1:8000/api/tasks?priority=high&sort=created_at&direction=desc"
```

### Show one task

```http
GET /api/tasks/{id}
```

Example:

```bash
curl -H "Accept: application/json" http://127.0.0.1:8000/api/tasks/1
```

### Create task

```http
POST /api/tasks
```

Example:

```bash
curl -X POST http://127.0.0.1:8000/api/tasks \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "New task",
    "priority": "medium"
  }'
```

### Update task

```http
PUT /api/tasks/{id}
```

Example:

```bash
curl -X PUT http://127.0.0.1:8000/api/tasks/1 \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Updated task title",
    "priority": "high",
    "due_date": "2026-03-30"
  }'
```

### Delete task

```http
DELETE /api/tasks/{id}
```

Example:

```bash
curl -X DELETE -H "Accept: application/json" http://127.0.0.1:8000/api/tasks/1
```

### Process task

```http
POST /api/tasks/{id}/process
```

Example:

```bash
curl -X POST -H "Accept: application/json" http://127.0.0.1:8000/api/tasks/1/process
```

## Docker

This project includes Docker support with:

- `Dockerfile`
- `docker-compose.yml`

To run with Docker:

```bash
docker compose up --build
```

Then open:

```text
http://127.0.0.1:8000
```

## Notes

This project was built in stages:

1. Create a working CRUD baseline
2. Refactor validation into Form Requests
3. Move task processing logic into an Action class
4. Add tests, seed data, documentation, and Docker support

Primary development and testing were done locally with PHP + SQLite. Docker configuration is included for easier startup.