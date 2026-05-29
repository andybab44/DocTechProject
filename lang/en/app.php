<?php

return [

    // Navigation
    'nav' => [
        'calendar'  => 'Calendar',
        'users'     => 'Users',
        'licenses'  => 'Licenses',
        'logout'    => 'Logout',
    ],

    // Auth
    'auth' => [
        'sign_in'         => 'Sign in to :app',
        'email'           => 'Email address',
        'password'        => 'Password',
        'remember_me'     => 'Remember me',
        'sign_in_button'  => 'Sign in',
    ],

    // Dashboard
    'dashboard' => [
        'admin'       => 'Admin Dashboard',
        'doctor'      => 'Doctor Dashboard',
        'technician'  => 'Technician Dashboard',
        'welcome'     => 'Welcome back, :name.',
        'welcome_dr'  => 'Welcome back, Dr. :name.',
        'access_full' => 'You have full access.',

        'role'        => 'Role',
        'access_level'=> 'Access Level',
        'status'      => 'Status',
        'active'      => 'Active',
        'full'        => 'Full',
        'clinical'    => 'Clinical',
        'technical'   => 'Technical',

        'manage_users'       => 'Manage Users',
        'manage_users_desc'  => 'Add, view and manage user accounts',
        'view_calendar'      => '📅 View Calendar',
        'view_my_calendar'   => '📅 View My Jobs Calendar',
        'new_work_job'       => '+ New Work Job',
    ],

    // Work Jobs – Calendar
    'calendar' => [
        'title'     => 'Work Jobs — :month',
        'new_job'   => '+ New Job',
        'add_job'   => 'Add job',
        'mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed', 'thu' => 'Thu',
        'fri' => 'Fri', 'sat' => 'Sat', 'sun' => 'Sun',
    ],

    // Work Jobs – Form (shared create/edit)
    'work_job' => [
        'back_calendar'     => '← Back to Calendar',
        'back'              => '← Back',
        'new_title'         => 'New Work Job',
        'edit_title'        => 'Edit Work Job',

        'field_title'       => 'Title',
        'field_description' => 'Description',
        'field_datetime'    => 'Date & Time',
        'field_technician'  => 'Assign Technician',
        'select_technician' => '— Select a technician —',
        'field_status'      => 'Status',

        'attachments'          => 'Attachments',
        'no_attachments'       => 'No files attached yet.',
        'attach_file'          => 'Attach File',
        'upload'               => 'Upload',
        'drop_or_browse'       => ':link or drag & drop',
        'click_to_browse'      => 'Click to browse',
        'file_selected'        => 'File selected — click to change',
        'drop_hint'            => 'PDF, Word, Excel, images, ZIP — max 50 MB',
        'drop_hint_create'     => 'PDF, Word, Excel, images, ZIP — up to 10 files, max 20 MB each',
        'attachments_optional' => 'Attachments',
        'attachments_optional_note' => '(optional)',
        'download'             => 'Download',
        'remove'               => 'Remove',
        'remove_attachment'    => 'Remove Attachment',
        'remove_confirm'       => 'Are you sure you want to remove :name? This cannot be undone.',
        'cancel_download'      => 'Cancel',

        'btn_create'    => 'Create Job',
        'btn_save'      => 'Save Changes',
        'btn_cancel'    => 'Cancel',
        'btn_delete'    => 'Delete',
        'btn_edit'      => 'Edit',
        'btn_upload'    => 'Upload',
        'btn_remove'    => 'Remove',

        'update_status'     => 'Update Status',
        'btn_save_status'   => 'Save',

        'details_requested_by'  => 'Requested by',
        'details_assigned_to'   => 'Assigned to',

        'delete_confirm' => 'Delete this work job?',

        'success_created' => 'Work job created successfully.',
        'success_uploaded' => 'File uploaded successfully.',
        'success_deleted_attachment' => 'Attachment deleted.',
    ],

    // Admin – Users
    'users' => [
        'title'         => 'Users',
        'subtitle'      => 'Manage all users in the system.',
        'add_user'      => '+ Add User',
        'add_title'     => 'Add User',
        'edit_title'    => 'Edit User',

        'col_name'      => 'Name',
        'col_email'     => 'Email',
        'col_role'      => 'Role',
        'col_status'    => 'Status',
        'col_created'   => 'Created',

        'status_active'   => 'Active',
        'status_inactive' => 'Inactive',

        'activate'    => 'Activate',
        'deactivate'  => 'Deactivate',
        'activate_confirm'   => 'Activate this user?',
        'deactivate_confirm' => 'Deactivate this user?',
        'no_users'    => 'No users found.',

        'field_name'                => 'Full Name',
        'field_email'               => 'Email Address',
        'field_role'                => 'Role',
        'field_password'            => 'Password',
        'field_password_new'        => 'New Password',
        'field_password_keep'       => '(leave blank to keep current)',
        'field_password_confirm'    => 'Confirm Password',
        'field_password_new_confirm'=> 'Confirm New Password',
        'select_role'               => 'Select a role…',

        'btn_create'  => 'Create User',
        'btn_save'    => 'Save Changes',
        'btn_cancel'  => 'Cancel',
        'btn_edit'    => 'Edit',
        'btn_back'    => '← Back',

        'search_placeholder' => 'Search by name or email…',
        'filter_all_roles'   => 'All roles',
        'btn_filter'         => 'Filter',
        'btn_reset'          => 'Reset',
        'col_jobs_created'   => 'Jobs Created',
        'col_jobs_assigned'  => 'Jobs Assigned',
    ],

    // Admin – Licenses
    'licenses' => [
        'title'         => 'Licenses',
        'subtitle'      => 'Manage per-user module licenses.',
        'new_license'   => '+ New License',
        'create_title'  => 'New License',
        'edit_title'    => 'Edit License',

        'col_user'      => 'User',
        'col_role'      => 'Role',
        'col_status'    => 'Status',
        'col_modules'   => 'Modules',
        'col_expires'   => 'Expires',
        'col_actions'   => 'Actions',

        'status_active'   => 'Active',
        'status_inactive' => 'Inactive',
        'status_expired'  => 'Expired',
        'status_none'     => 'No License',

        'assign'      => 'Assign',
        'edit'        => 'Edit',
        'activate'    => 'Activate',
        'deactivate'  => 'Deactivate',
        'perpetual'   => 'Perpetual',

        'field_user'       => 'User',
        'field_expires'    => 'Expiry Date',
        'field_expires_hint'=> '(leave blank for perpetual)',
        'field_modules'    => 'Modules',
        'no_modules'       => '—',
        'no_users_available'=> 'All users already have a license.',

        'btn_create'  => 'Create License',
        'btn_save'    => 'Save Changes',
        'btn_cancel'  => 'Cancel',

        'license_status_label'   => 'License Status',
        'license_status_active'  => 'This license is currently active.',
        'license_status_inactive'=> 'This license is currently inactive.',
        'back'        => '← Back to Licenses',

        'select_user'  => '— Select a user —',
    ],

    // Common
    'common' => [
        'back'   => '← Back',
        'cancel' => 'Cancel',
        'save'   => 'Save Changes',
        'delete' => 'Delete',
        'edit'   => 'Edit',
        'remove' => 'Remove',
    ],
];
