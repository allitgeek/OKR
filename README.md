# OKR (Objectives and Key Results) Management System

A comprehensive web application built with Laravel for managing Objectives and Key Results (OKRs) within an organization. This system helps teams track their goals, measure progress, and maintain accountability.

## 🎯 Recent Major Enhancements (July 2025)

### ✨ Comprehensive Filtering System
- **Dashboard Filtering**: 4-category filter system with real-time results
  - Status filters: Not Started (0%), In Progress (1-99%), Completed (100%), Overdue
  - Time filters: Latest Created, Oldest Created, Due Soon (7 days), Due This Month, Recently Updated
  - Progress filters: 0%, 1-25%, 26-50%, 51-75%, 76-99%, 100%
  - Owner filters: All, My Objectives, Others, Individual Users
- **Objectives Page**: Complete filtering with drag-and-drop functionality preserved
- **Tasks Page**: Enhanced filtering with priority, status, time, and assignee filters
- **Real-time Results Count**: Live display of filtered results
- **Quick Sort Options**: A-Z alphabetical and progress-based sorting

### 🔍 Advanced Search Functionality
- **Real-time User Search**: Instant search across names, emails, and roles
- **Debounced Search**: 150ms delay for optimal performance
- **Visual Feedback**: Smart result counting and "no results found" messages
- **Keyboard Support**: Escape key to clear search and blur input
- **Mobile Responsive**: Works seamlessly on both desktop and mobile devices

### ✅ Enhanced Validation & Data Integrity
- **Due Date Validation**: Comprehensive validation for objective due dates
- **Key Result Conflict Detection**: Prevents objective due date changes when conflicting KRs exist
- **Detailed Conflict Reporting**: Popup messages listing specific conflicting KRs with dates
- **Smart Form Validation**: JavaScript validation with user-friendly error messages

### 🎨 UI/UX Improvements
- **Progress Bar Color Consistency**: Green for completed (≥100%), blue for incomplete across all views
- **Multi-line Objective Titles**: Fixed text cutoff issues, proper text wrapping
- **Improved Layout**: Better flex layouts with proper alignment and spacing
- **Enhanced Visual Feedback**: Smooth animations and transitions
- **Professional Design**: Consistent design language across all pages

## Features

### User Management
- Role-based access control (Admin, Manager, User)
- User authentication and authorization
- Profile management
- Super admin capabilities
- Modern icon-based interface for user actions
- **NEW**: Real-time user search and filtering

### Objectives Management
- Create and manage organizational objectives
- Track objective progress with visual indicators
- Assign objectives to teams/individuals
- Set deadlines and priorities
- Modern card layout with intuitive progress tracking
- Pagination support for better performance
- **NEW**: Comprehensive filtering and sorting options
- **NEW**: Enhanced due date validation with conflict detection
- **NEW**: Multi-line title support with proper text wrapping

### Key Results
- Link key results to objectives
- Measure progress quantitatively
- Update status and completion percentage
- Quick progress updates with percentage buttons (25%, 50%, 75%, 100%)
- Add comments and attachments
- Start and due dates for better timeline management
- Real-time progress calculation
- **NEW**: Consistent progress bar colors across all views

### Task Management
- Break down key results into actionable tasks
- Task assignment and acceptance
- Due date tracking
- Task escalation for overdue items
- Progress synchronization with parent objectives
- **NEW**: Advanced filtering by status, priority, time, and assignee
- **NEW**: Overdue detection with smart logic

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
- **NEW**: Real-time filtering with live result counts
- **NEW**: Smooth animations and visual feedback
- **NEW**: Professional design consistency
- **NEW**: Enhanced mobile responsiveness

## Technology Stack

- **Backend**: Laravel 12.x (PHP 8.1+)
- **Frontend**: Blade Templates, Alpine.js, Tailwind CSS
- **Database**: SQLite (development), MySQL/PostgreSQL (production)
- **Build Tools**: Vite, NPM
- **Icons**: Heroicons

## Installation

1. Clone the repository:
```bash
git clone https://github.com/allitgeek/OKR.git
cd OKR
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Node.js dependencies:
```bash
npm install
```

4. Create environment file:
```bash
copy .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Run database migrations and seeders:
```bash
php artisan migrate:fresh --seed
```

7. Build frontend assets:
```bash
npm run build
```

8. Start the development server:
```bash
php artisan serve
```

The application will be available at `http://127.0.0.1:8000`

## Default Users

After running the seeders, you can log in with:

- **Super Admin**: admin@example.com / password
- **Manager**: manager@example.com / password  
- **User**: user@example.com / password

## Database Schema

### Core Tables
- `users` - User accounts and profiles
- `roles` - User roles (Admin, Manager, User)
- `permissions` - System permissions
- `objectives` - Main objectives with progress tracking
- `key_results` - Measurable key results linked to objectives
- `tasks` - Actionable tasks for key results
- `task_acceptances` - Task assignment and acceptance tracking
- `teams` - Team management
- `categories` - Objective categorization
- `comments` - Discussion threads
- `attachments` - File attachments
- `notifications` - System notifications
- `activity_log` - Audit trail for all activities

## API Endpoints

The system provides RESTful API endpoints for:
- Objectives CRUD operations
- Key Results management
- Task operations
- Progress updates
- User management

All API routes are protected with Laravel Sanctum authentication.

## 💾 Backup Information

### Automated Backups
The system includes automated backup capabilities:
- **Database Backups**: SQLite database files with timestamps
- **Complete Code Backups**: Compressed archives excluding temporary files
- **Backup Location**: Project root directory with naming pattern `OKR_*backup_YYYYMMDD_HHMMSS.*`

### Recent Backup Files
- `OKR_complete_backup_20250715_231902.zip` - Complete codebase (4.97 MB)
- `OKR_database_backup_20250715_231848.sqlite` - Database snapshot (323 KB)

## 🚀 Recent Fixes & Optimizations

### Critical Bug Fixes
- **Missing PHP Tags**: Fixed routes/api.php missing opening `<?php` tag
- **Bootstrap Corruption**: Restored proper Laravel bootstrap file
- **Output Buffering Issues**: Removed problematic error suppression code
- **Node Modules**: Clean reinstallation and proper asset building

### Performance Improvements
- **Real-time Filtering**: Debounced search for optimal performance
- **Efficient Queries**: Optimized database queries for filtering
- **Asset Optimization**: Proper Vite build configuration
- **Cache Management**: Clean Laravel cache management

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License.

## Support

For support, email admin@example.com or create an issue in the GitHub repository.

---

**Last Updated**: July 15, 2025  
**Current Version**: 2.0.0 (Major Enhancement Release)  
**Status**: Production Ready ✅
