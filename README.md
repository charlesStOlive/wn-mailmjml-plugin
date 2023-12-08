# wn-mailmjml-plugin
Ce plugin pour winter.cms permet d'envoyer des emails via MJMl

## installation 
AJouter cette ligne à composer.json ( pour l'instant sur branche test )
```
"require": {
        "php": "^8.0.2",
        ...
        "waka/wn-mailmjml-plugin": "dev-test",
        ...
}
```
## Envoyer un mail manuellement 
Il existe deux méthodes : 
* sendEmail (envoie le mail ( crée un mail dans la sendbox du plugin waka/wn-maillog-plugin ))
* show (retourne un html )

Exemple : 
```
\Waka\MailMjml\Classes\Mjmler::sendEmail('wcli.tarificateur::mjml.projet.base', $data, function ($mail) use ($model, $link) {
            $mail->setTos([$model->contact->email]);
            $mail->setSubject('hello word');
        });
```
Mjmler::sendMail('code', $datas, callBacks)
Le code est  : 
* soit un code dans un template enregistré dans le B.O
* soit l'adresse du fichier dans le dossier view 

Les textes du amil seront parsé via twig à partir des données de $datas. 

Lorsque productor est utilisé, si le modèle cible comporte les méthodes dsMap, le dsMap sera injecté dans un sous tableau 'ds' et seront accessible comme ceci : {{ ds.name }}
### Les callBacks 
Dans le callBack il est possible d'ajouter ces valeurs : 
```
\Waka\MailMjml\Classes\Mjmler::sendEmail('wcli.tarificateur::mjml.projet.base', $data, function ($mail) use ($model, $link) {
            $mail->setTos([$model->contact->email]); // mettre les emails cibles @var array
            $mail->setSubject('hello word'); // mettre un sujet @var string
            $mail->setSender('hello word'); // mettre un envoyeur @var string
            $mail->setReplyTo('hello word'); // mettre le replyTo @var string
            $mail->setCcs('hello word'); // mettre  des cibles pour la copie CC @var array
            $mail->setCci('hello word'); // choisir un sujet @var string
            $mail->setHeaders('hello word'); // choisir des headers @var array ['ds' => pour la classe de la target, 'ds_id' pour l'id de la classe]
        });
```
Réferez vous à la classe  Waka\MailMjml\Classes\MjmlCreator pour plus d'info

### Envoyer un email avec productor 
* referez vous au plugin  productor : waka/wn-productor-plugin
* Nom du driver mjmler
