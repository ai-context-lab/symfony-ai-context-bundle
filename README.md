# Symfony AI Context Bundle

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ai-context/symfony-ai-context-bundle.svg)](https://packagist.org/packages/ai-context/symfony-ai-context-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/ai-context/symfony-ai-context-bundle.svg)](https://packagist.org/packages/ai-context/symfony-ai-context-bundle)
[![Tests](https://github.com/ai-context-lab/symfony-ai-context-bundle/actions/workflows/ci.yml/badge.svg)](https://github.com/ai-context-lab/symfony-ai-context-bundle/actions/workflows/ci.yml)
[![License](https://img.shields.io/github/license/ai-context-lab/symfony-ai-context-bundle.svg)](https://github.com/ai-context-lab/symfony-ai-context-bundle/blob/main/LICENSE)
[![PHPStan](https://github.com/ai-context-lab/symfony-ai-context-bundle/actions/workflows/phpstan.yml/badge.svg)](https://github.com/ai-context-lab/symfony-ai-context-bundle/actions/workflows/phpstan.yml)


> 🔍 Automatically generate a structured, AI-readable JSON context from your Symfony application — including entities, services, controllers, routes and repositories.

---

## Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Example prompts](#example-prompts)
- [Contributing](#contributing)
- [License](#license)
- [Credits](#credits)

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

Configure the bundle in `config/packages/ai_context.yaml` you can edit the default values:

```yaml
ai_context:
    output_dir: '%kernel.project_dir%/'
    output_filename: 'ai-context.json'
    output_dir_checksum: '%kernel.project_dir%/var/ai-context/ai-context-checksum.json'
    include:
        routes: true
        entities: true
        services: true
        controllers: true
        repositories: true
        events: true
    paths:
        entities: '%kernel.project_dir%/src/Entity'
        services: '%kernel.project_dir%/src/Service'
        controllers: '%kernel.project_dir%/src/Controller'
        repository: '%kernel.project_dir%/src/Repository'
        events: '%kernel.project_dir%/src/Event'
```

## Usage

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

Then you can use the AI context in your preferred LLMs or AI tools to enhance your Symfony development experience.

## Example prompts

```text
 Can you give me a list of all the routes in my Symfony application?
 
 Generate a readme file with install process.
 
 List all services available with a quick description of each one.
 
 Give me full details of how work the <name service> service.
```

Whatever you need, the AI context is here to help you!
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
