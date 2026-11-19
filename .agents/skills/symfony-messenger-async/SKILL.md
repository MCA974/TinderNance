---
name: symfony-messenger-async
description: >-
  Skills pour Symfony Messenger et le traitement asynchrone dans Symfony 8.1.
  Utiliser pour découpler les opérations lourdes (envoi d'e-mails, calculs, notifications) via des messages et handlers asynchrones.
---

# Compétence Symfony 8.1 : Messenger & Traitement Asynchrone

Documentation officielle :
[https://symfony.com/doc/8.1/messenger.html](https://symfony.com/doc/8.1/messenger.html)

---

## 1. Principes essentiels

- **Message** : Un simple objet PHP (POPO / DTO) immuable représentant l'instruction à exécuter.
- **Handler** : Une classe de service annotée avec `#[AsMessageHandler]` contenant la logique de traitement `__invoke(MonMessage $message)`.
- **Transports** : Définis dans `config/packages/messenger.yaml` (ex: `doctrine://default`, `amqp://`, `redis://`, ou `sync://`).
- **Consommation** : Commande worker `php bin/console messenger:consume async`.

## 2. Exemple de Message et MessageHandler

### Message (DTO immuable)
```php
namespace App\Message;

readonly class SendEmailNotificationMessage
{
    public function __construct(
        public int $userId,
        public string $subject,
        public string $content,
    ) {}
}
```

### Handler
```php
namespace App\MessageHandler;

use App\Message\SendEmailNotificationMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendEmailNotificationHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(SendEmailNotificationMessage $message): void
    {
        // Traitement de l'envoi
        $this->logger->info('Notification envoyée à l\'utilisateur ' . $message->userId);
    }
}
```

### Envoi depuis un Contrôleur ou Service
```php
use App\Message\SendEmailNotificationMessage;
use Symfony\Component\Messenger\MessageBusInterface;

public function notify(MessageBusInterface $bus, int $userId): void
{
    $bus->dispatch(new SendEmailNotificationMessage(
        userId: $userId,
        subject: 'Bienvenue',
        content: 'Merci pour votre inscription !'
    ));
}
```

## 3. Commandes utiles
```bash
php bin/console debug:messenger
php bin/console messenger:consume async -vv
php bin/console messenger:failed:show
```
