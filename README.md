# OKR (Objectives and Key Results) Management System

A comprehensive web application built with Laravel for managing Objectives and Key Results (OKRs) within an organization. This system helps teams track their goals, measure progress, and maintain accountability.

## Features

### User Management
- Role-based access control (Admin, Manager, User)
- User authentication and authorization
- Profile management
- Super admin capabilities
- Modern icon-based interface for user actions

### Objectives Management
- Create and manage organizational objectives
- Track objective progress with visual indicators
- Assign objectives to teams/individuals
- Set deadlines and priorities
- Modern card layout with intuitive progress tracking
- Pagination support for better performance

### Key Results
- Link key results to objectives
- Measure progress quantitatively
- Update status and completion percentage
- Quick progress updates with percentage buttons (25%, 50%, 75%, 100%)
- Add comments and attachments
- Start and due dates for better timeline management
- Real-time progress calculation

### Task Management
- Break down key results into actionable tasks
- Task assignment and acceptance
- Due date tracking
- Task escalation for overdue items
- Progress synchronization with parent objectives

### Team Collaboration
- Team creation and management
- Comment system for discussions
- File attachments support
- Activity logging
- Real-time progress updates

### UI/UX Features
- Responsive design for mobile and desktop
- Navy blue theme with modern aesthetics
- Intuitive navigation
- Modal-based forms for better user experience
- Icon-based actions for cleaner interface
- Enhanced progress bars with status indicators
- Quick-action buttons for common tasks
- Improved spacing and typography
- Consistent color scheme for status and progress

## Technical Stack

- **Framework**: Laravel 10.x
- **Frontend**: 
  - Blade templates
  - Tailwind CSS
  - Alpine.js
  - Custom JavaScript for interactive features
- **Database**: SQLite with proper migrations
- **Authentication**: Laravel Breeze
- **Authorization**: Custom roles and permissions system

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   npm install
   ```
3. Copy `.env.example` to `.env` and configure your environment
4. Generate application key:
   ```bash
   php artisan key:generate
   ```
5. Run migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```
6. Build assets:
   ```bash
   npm run dev
   ```
7. Start the development server:
   ```bash
   php -d display_errors=off -d error_reporting=0 artisan serve --port=8080
   ```

## Usage

1. Register a new account or use the default admin account:
   - Email: admin@example.com
   - Password: password

2. Create objectives and assign key results:
   - Set clear titles and descriptions
   - Define target values and metrics
   - Assign owners and due dates

3. Track progress:
   - Use quick-select buttons (25%, 50%, 75%, 100%) for fast updates
   - View progress bars and status indicators
   - Monitor overall objective completion

4. Manage tasks:
   - Create and assign tasks
   - Track completion status
   - Escalate overdue items

5. Team collaboration:
   - Comment on objectives and key results
   - Attach relevant files
   - Monitor team progress

## Security

- CSRF protection enabled
- Form validation
- Secure password hashing
- Role-based access control
- Session management
- XSS protection
- Input sanitization
- Proper error handling

## Backup and Maintenance

- Automated backup system
- Excludes unnecessary files (vendor, node_modules, etc.)
- Regular cleanup of old backups
- Database migration management
- Error logging and monitoring

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
