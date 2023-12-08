<?php

return [
    'controllers' => [
        'layouts' => [
            'label' => 'Layouts',
        ],
        'mailmjmls' => [
            'label' => 'MJML Emails',
            'layouts' => 'Edit layouts',
        ],
        'pdfs' => [
            'layouts' => 'Edit layouts',
        ],
    ],
    'driver' => [
        'description' => 'Allows sending emails in MJML format',
        'mjmler' => [
            'label' => 'Mail MJML',
            'success' => [
                'btn_message_send_label' => 'View the email in the send box',
                'message_send' => 'Email sent successfully',
            ],
        ],
    ],
    'jobs' => [
        'mailmjml' => [
            'ids_error' => 'IDs of emails with errors',
            'result' => 'Results',
            'scoped' => 'Scoped',
            'send' => 'Emails sent',
            'send_emails' => 'Send MJML emails',
            'skipped' => 'Skipped emails',
        ],
    ],
    'menu' => [
        'mailMjmls' => [
            'description' => 'Emails in MJML format',
            'label' => 'MJML Emails',
        ],
    ],
    'models' => [
        'layout' => [
            'label' => 'Layout',
            'name' => 'Template name',
            'slug' => 'Slug',
            'template' => 'Template',
        ],
        'mail_logs' => 'Email logs',
        'mailmjml' => [
            'cci' => 'BCC',
            'cci_com' => 'Allow BCC',
            'click_log' => 'Click log',
            'code' => 'Code/Slug',
            'has_cci' => 'BCC',
            'has_log' => 'Record logs?',
            'has_log_com' => 'Allows for statistics on sent emails',
            'has_reply_to' => 'Customize reply-to address',
            'has_reply_to_com' => 'Otherwise, the default address will be used',
            'has_sender' => 'Customize sender address',
            'has_sender_com' => 'Otherwise, the default address will be used',
            'is_embed' => 'Embed images',
            'is_embed_com' => 'Slows down sending but allows embedding images in the email body',
            'label' => 'MJML Email',
            'layout' => 'Layout',
            'mjml' => 'MJML Code',
            'name' => 'Email title',
            'name_com' => 'Internal use only',
            'open_log' => 'Open log',
            'reply_to' => 'Reply-to address',
            'rule_blocs' => 'Additional blocks',
            'rule_blocs_com' => 'Allows adding blocks before or after the main content',
            'sender' => 'Sender address',
            'slug' => 'Code or Slug',
            'slug_com' => 'Only a super-admin can update. Be careful, the code is used by controllers!',
            'subject' => 'Subject',
            'sync' => 'Static',
            'tab_contents' => 'Additional contents',
            'tab_edit' => 'Editing',
            'tab_info' => 'Information',
            'tab_options' => 'Options',
        ],
        'mjml' => 'MJML',
        'subject' => 'Subject',
        'tab_edit' => 'Editing',
        'tab_logs' => 'Logs',
    ],
    'plugin' => [
        'description' => 'Plugin allowing to send emails using MJML. It requires Waka.Productor to function',
        'name' => 'Waka - MJML',
    ],
];
