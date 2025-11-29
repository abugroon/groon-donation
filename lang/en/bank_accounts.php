<?php

return [
    'title' => 'Bank accounts',
    'create' => 'Add bank account',
    'edit' => 'Edit bank account',
    'created' => 'Bank account created successfully.',
    'updated' => 'Bank account updated successfully.',
    'deleted' => 'Bank account deleted (soft delete).',
    'empty' => 'No bank accounts found yet.',
    'fields' => [
        'account_name' => 'Account name',
        'bank_name' => 'Bank name',
        'iban' => 'IBAN',
        'account_number' => 'Account number',
        'status' => 'Status',
    ],
    'status_labels' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],
    'actions' => [
        'save' => 'Save',
        'cancel' => 'Cancel',
        'edit' => 'Edit',
        'delete' => 'Delete',
    ],
    'confirm_delete' => 'Are you sure you want to delete this bank account?',
];
