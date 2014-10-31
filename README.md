# The Symfony2 Bundle

The bundle of timezone name for Google Timezone API.

## Instalion

### Composer

    php composer.phar require ahonymous/timezone-bundle

### AppKernel

Include the bundle in your AppKernel

    public function registerBundles()
    {
        $bundles = array(
            ...
            new Ahonymous\TimezoneBundle\AhonymousTimezoneBundle(),
        );
        ...
        
        return $bundles;
    }

### Configuration

Your configuration should be something like this

    ahonymous_timezone:
        api_key: <GOOGLE_API_KEY>
        url: "https://maps.googleapis.com"
