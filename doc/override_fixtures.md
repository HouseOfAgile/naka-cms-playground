
To override fixtures, create your fixtures in `App\DataFixtures` directory and extends the original ones from HouseOfAgile\NakaCMSBundle

Then use fixtureGroup feature from doctrine fixtures and define a specific group (here we use `thisApp`)

```
<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use HouseOfAgile\NakaCMSBundle\DataFixtures\PageGalleryFixtures as NakaPageGalleryFixtures;

class PageGalleryFixtures extends NakaPageGalleryFixtures implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['thisApp'];
    }
}
```

Then load your fixtures:

    bin/console d:f:l --group thisApp -n