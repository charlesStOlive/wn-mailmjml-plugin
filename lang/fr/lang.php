<?php

return [
    'controllers' => [
        'layouts' => [
            'label' => 'Layouts',
        ],
        'mailmjmls' => [
            'label' => 'Mails MJML',
            'layouts' => 'Modifier les layouts',
        ],
        'pdfs' => [
            'layouts' => 'Modifier les layouts',
        ],
    ],
    'driver' => [
        'description' => 'Permet d\'envoyer des emails en source MJML',
        'mjmler' => [
            'label' => 'Mail MJML',
            'success' => [
                'btn_message_send_label' => 'Voir l\'email dans la boîte d\'envoi',
                'message_send' => 'Mail envoyé avec succès',
            ],
        ],
    ],
    'jobs' => [
        'mailmjml' => [
            'ids_error' => 'ID des mails avec erreurs',
            'result' => 'Resultats',
            'scoped' => 'Scoped',
            'send' => 'Mails envoyés',
            'send_emails' => 'Envoyer des mails MJML',
            'skipped' => 'Mails abandonnées',
        ],
    ],
    'menu' => [
        'mailMjmls' => [
            'description' => 'Des Emails au format MJML',
            'label' => 'Mails MJML',
        ],
    ],
    'models' => [
        'layout' => [
            'label' => 'Layout',
            'name' => 'Nom du template',
            'slug' => 'Slug',
            'template' => 'Template',
        ],
        'mail_logs' => 'Logs mails',
        'mailmjml' => [
            'cci' => 'CCI',
            'cci_com' => 'Autoriser CCI',
            'click_log' => 'Log de clic',
            'code' => 'Code/Slug',
            'has_cciss' => 'CCI',
            'has_log' => 'Enregistrer les logs ?',
            'has_log_com' => 'Permet de faire des statistiques sur les emails envoyés',
            'has_reply_to' => 'Personnaliser l\'adresse de réponse',
            'has_reply_to_com' => 'Sinon, l\'adresse par défaut sera utilisée',
            'has_sender' => 'Personnaliser l\'adresse d\'envoi',
            'has_sender_com' => 'Sinon, l\'adresse par défaut sera utilisée',
            'is_embed' => 'Embarquer les images',
            'is_embed_com' => 'Ralentit les envois mais permet d\'incruster les images dans le corps du mail',
            'label' => 'Mail MJML',
            'layout' => 'Maquette',
            'mjml' => 'Code MJML',
            'name' => 'Intitulé de l\'email',
            'name_com' => 'Usage interne seulement',
            'open_log' => 'Log d\'ouverture',
            'reply_to' => 'Adresse de réponse',
            'rule_blocs' => 'Blocs additionnels',
            'rule_blocs_com' => 'Permet d\'ajouter des blocs avant ou après le contenu principal',
            'sender' => 'Adresse expéditeur',
            'slug' => 'Code ou Slug',
            'slug_com' => 'Seul un super-admin peut mettre à jour. Attention, le code est exploité par les contrôleurs !',
            'subject' => 'Sujet',
            'sync' => 'Static',
            'tab_contents' => 'Contenus additionnels',
            'tab_edit' => 'Édition',
            'tab_info' => 'Information',
            'tab_options' => 'Options',
        ],
        'mjml' => 'MJML',
        'subject' => 'Sujet',
        'tab_edit' => 'Édition',
        'tab_logs' => 'Logs',
    ],
    'plugin' => [
        'description' => 'Plugin permettant d\'envoyer des emails en exploitant le MJML. Il nécessite Waka.Productor pour fonctionner',
        'name' => 'Waka - MJML',
    ],
];
