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
        $applicationSenderName: "%env(APPLICATION_SENDER_NAME)%"
        $applicationContactEmail: "%env(APPLICATION_CONTACT_EMAIL)%"
        $applicationContactName: "%env(APPLICATION_CONTACT_NAME)%"
        $projectDir: "%kernel.project_dir%"
        $devMode: "%env(bool:DEV_MODE)%"
```

Also add the related tiwg parameters

* Add naka services definition files

In your `config/services.yml` file

```
imports:
    - { resource: "@NakaCMSBundle/Resources/config/naka_services_menu.yaml" }
    - { resource: "@NakaCMSBundle/Resources/config/naka_services.yaml" }
```
## Configure Security

Add binging for the form_login Service
    $formLoginAuthenticator: '@security.authenticator.form_login.main'


## Configure Specific feature
### dynamic reconfigure
Need to add the link to dragndrop js
```
in webpack.config.js
    .addEntry('dragndrop', './lib/NakaCMSBundle/assets/js/components/dragndrop.js')

```

### i18n websiteinfo metas

We need a new twig service in twig.yml
```
        websiteInfo: '@HouseOfAgile\NakaCMSBundle\Component\WebsiteInfo\WebsiteInfoService'
```

## Add Services stub
For ease of work, we add some general services/component

## Main changes
Update to vich uploader 2: need to use php attributes


