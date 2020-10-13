# JSON:API Middlewares
Collection of middlewares that can help implement [JSON:API](https://jsonapi.org/).

## Path middleware
Parsing API path and assign parsed attributes. Usage:
```php
use Middleware\JSONAPI\Path;

new Path('/api'); // Init middleware with custom base path

 // Is request is root or "/"
$request->getAttribute(Path::ATTRIBUTE_ROOT);

// Get resource in "/people/1" result will be "people"
$request->getAttribute(Path::ATTRIBUTE_RESOURCE);

// Get the id in "/people/1" result will be "1"
$request->getAttribute(Path::ATTRIBUTE_ID);

// Get related relation in "/people/1/articles" result will be "articles"
$request->getAttribute(Path::ATTRIBUTE_RELATED);

// Get the relation in relationships request in "/people/1/relationships/articles" result will be "articles"
$request->getAttribute(Path::ATTRIBUTE_RELATIONSHIP);
```