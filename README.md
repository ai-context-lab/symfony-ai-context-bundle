# Symfony AI Context Bundle

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ai-context/symfony-ai-context-bundle.svg)](https://packagist.org/packages/ai-context/symfony-ai-context-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/ai-context/symfony-ai-context-bundle.svg)](https://packagist.org/packages/ai-context/symfony-ai-context-bundle)
[![Tests](https://github.com/ai-context-lab/symfony-ai-context-bundle/actions/workflows/ci.yml/badge.svg)](...)
[![License](https://img.shields.io/github/license/ai-context-lab/symfony-ai-context-bundle.svg)](https://github.com/ai-context-lab/symfony-ai-context-bundle/blob/main/LICENSE)
[![Static Analysis](https://github.com/ai-context-lab/symfony-ai-context-bundle/actions/workflows/phpstan.yml/badge.svg)](https://github.com/ai-context-lab/symfony-ai-context-bundle/actions/workflows/phpstan.yml)

> 🔍 Automatically generate a structured, AI-readable JSON context from your Symfony application — including entities, services, controllers, routes and repositories.

---

## Installation

```bash
composer require ai-context/symfony-ai-context-bundle --dev
```

Make sure the bundle is enabled (Symfony Flex should do this automatically):

```php
// config/bundles.php
return [
    AiContextBundle\AiContextBundle::class => ['dev' => true, 'test' => true],
];
```

## Configuration

Configure the bundle in `config/packages/ai_context.yaml`

```yaml
ai_context:
    output_dir: '%kernel.project_dir%/var/ai_context'
    output_filename: 'ai-context.json'
    output_dir_checksum: '%kernel.project_dir%/var/ai_context/ai-context-checksum.json'

    include:
        entities: true
        services: true
        controllers: true
        routes: true
        repositories: true

    paths:
        entities: ['%kernel.project_dir%/src/Entity']
        services: ['%kernel.project_dir%/src/Service']
        controllers: ['%kernel.project_dir%/src/Controller']

```

## ⚡ Usage

Generate the AI context:

```bash
php bin/console ai-context:generate
```

The command outputs a structured JSON file (by default in var/ai_context/ai-context.json) including:

    🧩 Entities: class names, fields, types, relations

    🛠️ Services: method signatures and parameters

    🎮 Controllers: public actions and @IsGranted annotations

    🚦 Routes: method/path/controller mapping

    📚 Repositories: custom public methods

Perfect to feed into LLMs like GPT for project understanding.

## Roadmap

- Entity extractor (done)

- Service extractor (done)

- Controller & route extractor (done)

- Repository extractor (done)

- Form & Validation extractor

- Event subscriber extractor

## Contributing

PRs welcome!
To contribute:

- Fork the repository
- Create a branch
- Write tests or improve extractors
- Submit a pull request

## License

Released under the MIT License

## Credits

Developed with ❤️ by Guillaume Valadas
Part of the AI Context Lab
