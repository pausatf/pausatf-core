# PAUSATF Core widget override

This repository contains `pausatf-core.php`, version 1.1.0, requiring PHP 8.1 or newer.
It is a small WordPress plugin that overrides visibility for widget `custom_html-61` on these page IDs:
70819, 71502, 71472, 70939 and 73880. It leaves admin requests and other widgets unchanged.

## Install and validate

Install the file in its own directory under `wp-content/plugins/`, then activate **PAUSATF Core** in WordPress.
Before deployment, confirm the target widget/page IDs match the intended site. Test the affected pages and an
unaffected page in staging; a PHP syntax check does not establish widget visibility correctness.

```bash
mise exec php@8.1.34 -- php -l pausatf-core.php
```

Rollback by restoring the previous reviewed file or deactivating the plugin after checking dependent behavior.
Follow current-head CI, reviewer and repository merge requirements; deployment is a separate authorized action.

## Related repositories

The different version 2.0.0 core plugin in
[pausatf-wordpress](https://github.com/pausatf/pausatf-wordpress/tree/main/plugins/pausatf-core) has its own
Composer/npm tooling. Its dependency upgrades do not change this standalone widget override.
[pausatf-deployment](https://github.com/pausatf/pausatf-deployment) supplies the local development stack.
Maintainer: @somethingwithproof. License: GPL-2.0-or-later, as declared in the plugin header.
