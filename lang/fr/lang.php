<?php

return [
    'plugin' => [
        'name' => 'Waka - MJML',
        'description' => 'Plugin permettant d\'envoyer des emails en exploitant le MJML. il nécessite Waka.Productor pour fonctionner'
    ],
    'permissions' => [
        'some_permission' => 'Some permission',
        'user_base' => 'Administrateur MJML'
    ],
    'models' => [
        'mailmjml' => [
            'click_log' => 'Log click',
            'create' => 'Créer un Email',
            'has_log' => 'Enregistrer les logs ?',
            'has_log_com' => 'Permets de faire des statistiques sur les emails envoyés',
            'has_sender' => 'Personnaliser l\'adresse d\' envoi',
            'has_sender_com' => 'sinon l\'adresse par défaut sera utilisée',
            'html' => 'Éditeur',
            'ids_error' => 'Liste des erreurs',
            'is_embed' => 'Embarquer les images',
            'is_embed_com' => 'Ralentie les envois mais permet d\'incruster les images dans le coprtdu mail',
            'is_lot' => 'Autorisé dans les lots ?',
            'is_mjml' => 'En MJML ?',
            'job_scoped' => 'Mail avec erreur sur le scope',
            'job_send' => 'Mail envoyés',
            'job_skipped' => 'Mail abandonnées',
            'job_title' => 'Message(s) à envoyer',
            'mailLogs' => 'Logs emails',
            'mail_logs' => 'Logs des emails',
            'mail_success' => 'Mail envoyé avec succès',
            'mjml' => 'Code MJML',
            'name' => 'Nom de l\'email',
            'name_com' => 'above::interne uniquement',
            'open_log' => 'Log ouverture',
            'reply_to' => 'Adresse de réponse',
            'rule_asks' => 'Champs éditables',
            'rule_blocs' => 'Blocs additionels',
            'rule_blocs_com' => 'Permet d\'ajouter des blocs avant ou après le contenu principal',
            'sender' => 'Adresse envoyeur',
            'slug' => 'Code ou Slug',
            'slug_com' => 'Seul un super admin peux maj. Attention le code est exploité par les controlleurs !',
            'tab_attributs' => 'Attributs',
            'tab_blocs' => 'Blocs',
            'tab_contents' => 'Contenus aditionels',
            'tab_edit' => 'Édition',
            'tab_info' => 'Information',
            'tab_infos' => 'Infos',
            'tab_logs' => 'Logs',
            'tab_options' => 'Options',
            'cci' => 'CCI',
            'has_cci' => 'Autoriser CCI',
            'has_cciss' => 'CCI',
            'cci_com' => 'Autoriser CCI',
            'has_reply_to_com' => 'Sinon l\'adresse par defaut sera utilisé',
            'has_reply_to' => 'Personaliser adresse de réponse',
            'label' => 'Mail Mjml',
            'label_plural' => 'Mail Mjmls',
            'subject' => 'Sujet'
        ]
    ],
    'driver' => [
        'mjmler' => [
            'label' => 'Mail MJML'
        ],
        'description' => 'Permet d\'envoyer des emails en source MJML'
    ],
    'menu' => [
        'wakamails' => 'Mail MJML'
    ],
    'mailmjml' => [
        'controller' => [
            'create' => 'Créer un mail MJML',
            'index_label' => 'Mails MJML',
            'title' => 'Gérer'
        ]
    ]
];
