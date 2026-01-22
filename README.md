# Deep Search

Search plugin for WordPress

<br>

## Environment Requirements

* PHP >= 8.1
* NodeJS >= 22.x
* WordPress >= 6.8

<br>

## Plugin Dev Environment Setup

Install composer and npm packages, and build JavaScript

```bash
composer install

npm install

npm run build:admin
npm run build:block
```

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
│       │   ├── block.json             # Block configuration
│       │   ├── edit.js                # Block editor component
│       │   ├── style.scss             # Block styles
│       │   ├── view.js                # Frontend view entry
│       │   └── view-components/       # Frontend React components
│       │       ├── DeepSearch.jsx     # Main search component
│       │       ├── SearchBar.jsx      # Search input component
│       │       ├── SearchOptions.jsx  # Search options component
│       │       └── PostList.jsx       # Search results component
│       ├── build/                     # Compiled block assets
│       └── view.php                   # Block render template
├── vendor/                            # Composer dependencies
├── node_modules/                      # NPM dependencies
├── search.php                         # Plugin entry file
├── composer.json                      # PHP dependencies
├── package.json                       # JavaScript dependencies
├── bundler                            # WP Bundler configuration
├── pint.json                          # Laravel Pint config
└── README.md                          # Documentation
```
