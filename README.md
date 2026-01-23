# Deep Search

Search plugin for WordPress

<br>

## Environment Requirements

* PHP >= 8.1
* WordPress >= 6.8
* PHP >= 8.3 (Only for test suite)
* NodeJS >= 22.x (Only for development env)

<br>

## Plugin Dev Environment Setup

Install composer and npm packages, and build JavaScript.

```bash
composer install

npm install

npm run build:admin
npm run build:block
```

<br>

## Test

For better test coverage, test suite need to be run on PHP-8.3. This can be done with a seperate docker container.

<br>

## File Structure

```
deep-search/
├── app/                               # PHP application code
│   ├── Core.php                       # Plugin core initialization
│   ├── Lib/                           # Library classes
│   │   ├── BaseService.php            # Base service class
│   │   └── SingleTon.php              # Singleton trait
│   └── Services/                      # Service classes
│       ├── AdminMenu.php              # Admin menu registration
│       ├── AssetsManager.php          # Asset loading management
│       ├── Block.php                  # Gutenberg block registration
│       └── SearchConfigs.php          # Search configuration handler
├── resources/                         # Frontend resources
│   ├── admin/                         # Admin panel assets
│   │   ├── src/                       # Admin source files
│   │   │   ├── app.js                 # Admin entry point
│   │   │   ├── Admin.jsx              # Admin React component
│   │   │   └── style.scss             # Admin styles
│   │   ├── build/                     # Compiled admin assets
│   │   └── view.php                   # Admin view template
│   └── block/                         # Gutenberg block assets
│       ├── src/                       # Block source files
│       │   ├── index.js               # Block entry point
│       │   ├── edit.js                # Block editor component
│       │   ├── style.scss             # Block styles
│       │   ├── view.js                # Frontend view entry
│       │   └── view-components/       # Frontend React components
│       │       ├── DeepSearch.jsx     # Main search component
│       │       ├── SearchBar.jsx      # Search input component
│       │       ├── SearchOptions.jsx  # Search options component
│       │       └── PostList.jsx       # Search results component
│       ├── block.json                 # Block configuration
│       ├── build/                     # Compiled block assets
│       └── view.php                   # Block render template
├── tests/                             # Test files
│   ├── Feature/                       # Feature tests
│   │   └── AdminMenuTest.php          # Admin menu tests
│   ├── Unit/                          # Unit tests
│   │   └── CoreTest.php               # Core class tests
│   ├── Pest.php                       # Pest configuration
│   ├── TestCase.php                   # Base test case class
│   └── _ide_helper.php                # IDE helper for tests
├── vendor/                            # Composer dependencies
├── vendor-test/                       # Test-only dependencies (for IDE)
├── node_modules/                      # NPM dependencies
├── test                               # Test runner script (Docker)
├── search.php                         # Plugin entry file
├── composer.json                      # PHP dependencies
├── composerTest.json                  # Test dependencies config
├── package.json                       # JavaScript dependencies
├── bundler                            # WP Bundler configuration
├── pint.json                          # Laravel Pint config
└── README.md                          # Documentation
```
