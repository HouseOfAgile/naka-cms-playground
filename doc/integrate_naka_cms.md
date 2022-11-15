## Integrate NakaCMS in your project
* Start from blank project ased on symfony create project
* Integrate the bundle (through composer or git local repository).
* Configure composer
* Configure Security


## Configure Composer
* Add autoload psr4
```
    },
    "autoload": {
        "psr-4": {
            "HouseOfAgile\\NakaCMSBundle\\": "lib/NakaCMSBundle/src/",
            "App\\": "src/"
        }
    },
```
* Add bundles loading in bundles.php
```
    HouseOfAgile\NakaCMSBundle\NakaCMSBundle::class => ['all' => true],

```
* Add the services bindings
```
    bind:
        $uploadsPath: "%kernel.project_dir%/public/uploads"
        $allLocales: "%all_locales%"
        $applicationName: "%env(APPLICATION_NAME)%"
        $applicationSenderEmail: "%env(APPLICATION_SENDER_EMAIL)%"
        $applicationContactEmail: "%env(APPLICATION_CONTACT_EMAIL)%"
        $projectDir: "%kernel.project_dir%"
        $devMode: "%env(bool:DEV_MODE)%"
```

* Add naka services definition files

In your `config/services.yml` file

```
imports:
    - { resource: "@NakaCMSBundle/Resources/config/naka_services_menu.yaml" }
    - { resource: "@NakaCMSBundle/Resources/config/naka_services.yaml" }
```
## Configure Security