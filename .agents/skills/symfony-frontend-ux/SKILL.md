---
name: symfony-frontend-ux
description: >-
  Skills pour le Frontend, AssetMapper, Twig et Symfony UX (Turbo, Stimulus) dans Symfony 8.1.
  Utiliser pour la gestion des templates Twig, des assets sans Node.js via importmap, et l'interactivité moderne.
---

# Compétence Symfony 8.1 : Frontend, AssetMapper, Twig & UX

Documentation officielle :
- [https://symfony.com/doc/8.1/frontend/asset_mapper.html](https://symfony.com/doc/8.1/frontend/asset_mapper.html)
- [https://symfony.com/doc/8.1/templates.html](https://symfony.com/doc/8.1/templates.html)
- [https://ux.symfony.com/](https://ux.symfony.com/)

---

## 1. Principes et Bonnes Pratiques

- **AssetMapper (zéro configuration Node.js/Webpack)** :
  - Les modules JavaScript sont résolus via `importmap.php`.
  - Pour installer un package JS : `php bin/console importmap:require <package>`
  - Pour auditer les packages : `php bin/console importmap:audit`
- **Symfony UX Turbo & Stimulus** :
  - Interactivité SPA sans framework lourd (Turbo Drive, Turbo Frames, Turbo Streams).
  - Contrôleurs Stimulus légers dans `assets/controllers/`.
- **Twig 3** :
  - Héritage strict des templates (`{% extends 'base.html.twig' %}`).
  - Blocs standardisés (`{% block title %}`, `{% block body %}`).
  - Génération de routes : `{{ path('nom_route', {id: item.id}) }}`.

## 2. Exemple de contrôleur Stimulus & template Twig

### Contrôleur Stimulus (`assets/controllers/counter_controller.js`)
```javascript
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['count'];

    connect() {
        this.counter = 0;
    }

    increment() {
        this.counter++;
        this.countTarget.textContent = this.counter;
    }
}
```

### Template Twig
```twig
{% extends 'base.html.twig' %}

{% block title %}Compteur UX{% endblock %}

{% block body %}
<div class="container my-4" data-controller="counter">
    <h1 class="h3">Compteur interactif</h1>
    <p>Valeur actuelle : <span data-counter-target="count" class="badge bg-primary">0</span></p>
    <button class="btn btn-success" data-action="click->counter#increment">
        Incrémenter
    </button>
</div>
{% endblock %}
```

## 3. Commandes utiles
```bash
php bin/console importmap:outdated
php bin/console lint:twig templates/
```
