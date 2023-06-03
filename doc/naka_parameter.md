NakaParameter is a bascially a key value entity to store some specific variable for a custom website/platform.
It gives you the ability to store some important variable and make them available almost anywhere in the platform.

In twig, you can get the value of a defined NakaParameter if it is not set, then 

For example, to integrate the google analytics:

```
    {% set ga_id = 'google-analytics'| nakaParameter %}
    {% if ga_id %}
        {% include '@NakaCMS/fragment/_google_analytics.html.twig' %}
    {% endif %}
```

Another example to show a list of social links :

```
    {% for socialLink in ['facebook', 'instagram', 'twitter' ,'linkedin'] %}
        {% if (socialLink ~ '-page-link') | nakaParameter | default(null) is not empty %}
            <a href="{{ (socialLink ~ '-page-link') | nakaParameter }}" target="_new" class="social-link px-3">
                <i class="fa-brands fa-lg fa-2x fa-{{ socialLink }}"></i>
            </a>
        {% endif %}
    {% endfor %}
```

## Configuration

In the `webpack.config.js` file, you need to add the following sassOptions (adding the sass files form NakaCMS).
```
    // enables Sass/SCSS support
    .enableSassLoader(function (options) {
        options.sassOptions.includePaths = ['./lib/NakaCMSBundle/assets/styles/section/'];
    })

```