# Product Overview

This is a Laravel-based meeting room booking and management system with comprehensive activity management. The application provides functionality for:

- **Meeting room booking** - Public interface for users to book meeting rooms
- **Calendar management** - View and manage room availability 
- **Activity management** - Create, track and report on various business activities
- **Multi-role administration** - Different access levels with specific permissions
- **Department management** - Organize users and resources by department
- **Employee management** - User administration and role assignment
- **Sales mission tracking** - Field visits, team assignments, and feedback surveys
- **Lead worksheet management** - Lead-specific worksheet functionality

## Role-Based Access Control

The system implements comprehensive role-based access with custom middleware:

### **SuperAdmin** 
- Complete system access with all features from other roles
- User management and system administration
- Activity logs and comprehensive reporting
- Meeting rooms, departments, bookings, employees management
- Activity types configuration

### **Admin**
- Meeting room booking management
- Department and employee administration  
- Booking reports and statistics
- Basic activity reporting access

### **Admin BAS (Business Activity Supervisor)**
- Activity management and reporting (primary focus)
- Activity types configuration
- Meeting rooms and departments management
- Employee management with activity context
- Booking approval/rejection capabilities

### **Sales Mission**
- Sales activity management and tracking
- Team management and field visit assignments
- Feedback survey generation and management
- Sales reports including agenda and survey analytics
- Daily schedule management for teams

### **Sales Officer**
- Personal activity creation and management
- Calendar view of assigned activities
- Basic reporting for own activities
- Limited scope compared to Sales Mission role

### **Lead**
- Lead worksheet management
- Dashboard with lead-specific metrics
- Edit and update worksheet capabilities

## Key Features
- **Multi-level activity system** - Different activity types with role-based management
- **Team assignment system** - Field visits with public calendar access
- **Feedback survey system** - Token-based public surveys with analytics
- **PDF/Excel reporting** - Comprehensive export capabilities using DomPDF and Maatwebsite Excel
- **Real-time calendar interfaces** - Multiple calendar views for different contexts
- **Public interfaces** - Field visits calendar and feedback forms
- **Queue-based processing** - Background job handling
- **Multi-tenant department structure** - Organized resource management

## Route Structure
- `/` - Public booking interface
- `/admin/*` - Admin role routes (booking management)
- `/superadmin/*` - SuperAdmin routes (full system access)
- `/bas/*` - Admin BAS routes (activity management focus)
- `/sales/*` - Sales Mission routes (team and survey management)
- `/officer/*` - Sales Officer routes (personal activity management)
- `/lead/*` - Lead role routes (worksheet management)
- `/feedback/*` - Public feedback survey interfaces
- `/field-visits/*` - Public field visit calendar