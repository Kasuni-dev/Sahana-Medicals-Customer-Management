# Profile Page Setup Instructions

## Overview
The profile page has been successfully integrated into your Sahana Medicals application. This page allows users to manage their personal information, medical details, and account settings.

## Files Added/Modified

### New Files:
- `profile.php` - Main profile page with full functionality
- `setup_profile_fields.sql` - Database schema updates
- `update_database_schema.php` - PHP script to update database
- `PROFILE_SETUP.md` - This setup guide

### Modified Files:
- `home.html` - Updated navigation to link to profile page
- `login.php` - Updated redirects to send users to profile page after login

## Database Setup

To enable full profile functionality, you need to add additional columns to your users table. Run the following SQL commands in your database:

```sql
-- Add profile fields to users table
ALTER TABLE users ADD COLUMN IF NOT EXISTS dateOfBirth DATE NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS gender VARCHAR(20) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS bloodType VARCHAR(10) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS weight VARCHAR(20) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS emergencyContact VARCHAR(100) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS emergencyPhone VARCHAR(20) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS allergies TEXT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS currentMedications TEXT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
```

## Features

### Profile Page Features:
1. **Personal Information Management**
   - First Name, Last Name
   - Email, Phone Number
   - Date of Birth, Gender
   - Address

2. **Medical Information**
   - Blood Type
   - Weight
   - Emergency Contact Details
   - Allergies
   - Current Medications

3. **Account Security**
   - Password Change
   - Two-Factor Authentication (UI ready)
   - Email Verification Status

4. **Notification Preferences**
   - Order Updates
   - Prescription Reminders
   - Health Tips
   - Promotional Offers

5. **Activity Timeline**
   - Recent account activity
   - Order history
   - Profile updates

## User Flow

1. **Login**: Users are redirected to profile page after successful login
2. **Navigation**: "My Profile" link appears in navigation for logged-in users
3. **Profile Management**: Users can update their information through forms
4. **Data Persistence**: All changes are saved to the database

## Admin Integration

- Admins are redirected to the user management page after login
- The profile page is accessible to all logged-in users
- Admin users see their profile information just like regular users

## Testing

To test the profile page:

1. **Login as a regular user** - You should be redirected to `profile.php`
2. **Login as an admin** - You should be redirected to `user_management.php`
3. **Navigate to profile** - Click "My Profile" in the navigation
4. **Update information** - Try updating personal and medical information
5. **Change password** - Test the password change functionality

## Notes

- The profile page is fully responsive and works on mobile devices
- All form submissions are handled securely with proper validation
- The page includes proper error handling and success messages
- The design matches the existing Sahana Medicals branding

## Troubleshooting

If you encounter issues:

1. **Database errors**: Make sure you've run the SQL commands to add the new columns
2. **Redirect issues**: Check that the login.php redirects are working correctly
3. **Session issues**: Ensure session_start() is called at the beginning of profile.php
4. **Permission errors**: Make sure the web server has write permissions for any file uploads

The profile page is now fully integrated and ready to use!