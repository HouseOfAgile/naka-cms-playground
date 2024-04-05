

## naka useful command

### Command to generate translations thanks to deepl

Add deepl key in .env file

    DEEPL_AUTH_KEY=your_auth_key

You should have configured it in services.yml


    HouseOfAgile\NakaCMSBundle\Service\DeepLTranslationService:
        arguments:
            $authKey: '%env(DEEPL_AUTH_KEY)%'

    HouseOfAgile\NakaCMSBundle\Command\TranslateCommand:
        arguments:
            $deepLTranslationService: '@HouseOfAgile\NakaCMSBundle\Service\DeepLTranslationService'
            $translationsDir: '%kernel.project_dir%/translations'
        tags:
            - { name: 'console.command' }


Usage:

    php bin/console naka:translate EN DE --domain=messages --skip-existing

    # should translate your exisiting `translations/messages.en.yml` into `translations/messages.de.yml`, and skip exisiting translations