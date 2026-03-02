# Infrastructure Unification (PHP 8.5 + Workflows + Ruleset) Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Align all three packages (utils, auth, starter) on PHP 8.5, unified GitHub Actions workflows using reusable `patrikjak/workflows`, and a shared `ruleset.xml`.

**Architecture:** Each package gets identical `lint.yml`, `test.yml`, and `release.yml` workflow files that call the reusable workflows in `patrikjak/workflows`. The reusable `test.yml` is updated to support optional Codecov upload. The auth `docker-compose.yml` is updated to use `patrikjak/php-frankenphp:8.5-cli` and drops the explicit network block. Auth and utils `ruleset.xml` are aligned to the PSR-12 + PER-3 standard already used by starter.

**Tech Stack:** Docker Compose, GitHub Actions reusable workflows, PHPCS (Slevomat + PSR-12), PHPStan, PHPUnit, Codecov

---

## Task 1: Update reusable `test.yml` in `patrikjak/workflows`

**Files:**
- Modify: `/Users/patrikjakab/workspace/workflows/.github/workflows/test.yml`

**Step 1: Update the reusable workflow to support optional Codecov upload**

Replace the file contents with:

```yaml
name: Test

on:
  workflow_call:
    inputs:
      php-version:
        required: false
        default: '8.4'
        type: string
      codecov-slug:
        required: false
        default: ''
        type: string
    secrets:
      composer_auth_token:
        required: false
      codecov-token:
        required: false

jobs:
  test:
    runs-on: ubuntu-latest
    name: Run Tests
    env:
      XDEBUG_MODE: coverage

    steps:
      - uses: actions/checkout@v4

      - uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ inputs.php-version }}
          coverage: xdebug

      - name: Set up Composer auth
        run: |
          if [ -n "${{ secrets.composer_auth_token }}" ]; then
            composer config --global --auth github-oauth.github.com ${{ secrets.composer_auth_token }}
          fi

      - name: Install dependencies
        run: composer install -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist

      - name: PHPUnit tests
        run: vendor/phpunit/phpunit/phpunit --configuration phpunit.xml --coverage-clover ./coverage.xml

      - name: Upload coverage to Codecov
        if: ${{ inputs.codecov-slug != '' }}
        uses: codecov/codecov-action@v5
        with:
          token: ${{ secrets.codecov-token }}
          slug: ${{ inputs.codecov-slug }}
          files: ./coverage.xml
```

**Step 2: Commit in the workflows repo**

```bash
cd /Users/patrikjakab/workspace/workflows
git add .github/workflows/test.yml
git commit -m "feat: add optional Codecov coverage upload to test workflow"
git push
```

---

## Task 2: Update `auth` — `docker-compose.yml`

**Files:**
- Modify: `/Users/patrikjakab/workspace/packages/auth/docker-compose.yml`

**Step 1: Replace file contents**

```yaml
name: auth

services:
  cli:
    image: patrikjak/php-frankenphp:8.5-cli
    container_name: "auth-cli"
    environment:
      XDEBUG_MODE: develop,debug,coverage
    volumes:
      - ".:/var/www"
    depends_on:
      db:
        condition: service_healthy

  db:
    container_name: "auth-test-db"
    image: mariadb:10
    environment:
      MYSQL_ROOT_PASSWORD: rootpassword
      MYSQL_ROOT_HOST: '%'
      MYSQL_USER: user
      MYSQL_PASSWORD: password
      MYSQL_ALLOW_EMPTY_PASSWORD: 'yes'
      MYSQL_DATABASE: testing
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 3s
      timeout: 3s
      retries: 5
```

Key changes: PHP image updated to `patrikjak/php-frankenphp:8.5-cli`, `networks` removed from all services and top-level, unquoted `name`.

**Step 2: Verify Docker Compose syntax**

```bash
cd /Users/patrikjakab/workspace/packages/auth
docker compose config
```
Expected: valid YAML output with no errors.

---

## Task 3: Replace `auth` — `ruleset.xml`

**Files:**
- Modify: `/Users/patrikjakab/workspace/packages/auth/ruleset.xml`

**Step 1: Replace file contents with utils' version (PSR-12 + PER-3 + minimal Slevomat)**

```xml
<?xml version="1.0"?>
<ruleset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         name="PSR-12 + PER-3 Standard"
         xsi:noNamespaceSchemaLocation="vendor/squizlabs/php_codesniffer/phpcs.xsd">

    <description>PSR-12 and PER-3 coding standards with minimal Slevomat additions for PHP 8.5+</description>

    <!-- Configuration -->
    <config name="show_progress" value="1"/>
    <config name="tab_width" value="4"/>
    <config name="php_version" value="8.5"/>
    <config name="installed_paths" value="vendor/slevomat/coding-standard"/>

    <!-- Command line arguments -->
    <arg name="colors"/>
    <arg name="parallel" value="8"/>
    <arg value="sp"/>
    <arg name="extensions" value="php"/>

    <!-- Files to check -->
    <file>src</file>
    <file>tests</file>

    <!-- ===================================================================== -->
    <!-- PSR-12 BASE STANDARD                                                  -->
    <!-- ===================================================================== -->

    <rule ref="PSR12"/>

    <!-- ===================================================================== -->
    <!-- PER-3 ESSENTIAL ADDITIONS                                             -->
    <!-- ===================================================================== -->

    <rule ref="SlevomatCodingStandard.TypeHints.LongTypeHints"/>
    <rule ref="SlevomatCodingStandard.Arrays.TrailingArrayComma"/>
    <rule ref="SlevomatCodingStandard.TypeHints.DNFTypeHintFormat"/>
    <rule ref="SlevomatCodingStandard.Classes.EnumCaseSpacing"/>
    <rule ref="SlevomatCodingStandard.Classes.BackedEnumTypeSpacing"/>

    <!-- ===================================================================== -->
    <!-- RECOMMENDED ADDITIONS (Best Practices)                                -->
    <!-- ===================================================================== -->

    <rule ref="SlevomatCodingStandard.TypeHints.DeclareStrictTypes">
        <properties>
            <property name="spacesCountAroundEqualsSign" value="0"/>
            <property name="declareOnFirstLine" value="false"/>
        </properties>
    </rule>

    <rule ref="SlevomatCodingStandard.Classes.ModernClassNameReference"/>
    <rule ref="SlevomatCodingStandard.Namespaces.AlphabeticallySortedUses"/>

    <rule ref="SlevomatCodingStandard.Namespaces.UseSpacing">
        <properties>
            <property name="linesCountBeforeFirstUse" value="1"/>
            <property name="linesCountBetweenUseTypes" value="1"/>
            <property name="linesCountAfterLastUse" value="1"/>
        </properties>
    </rule>

    <rule ref="SlevomatCodingStandard.TypeHints.ParameterTypeHintSpacing"/>
    <rule ref="SlevomatCodingStandard.TypeHints.ReturnTypeHintSpacing"/>
    <rule ref="SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue"/>
    <rule ref="SlevomatCodingStandard.Classes.ClassConstantVisibility"/>
    <rule ref="SlevomatCodingStandard.TypeHints.UselessConstantTypeHint"/>
    <rule ref="SlevomatCodingStandard.PHP.UselessParentheses"/>
    <rule ref="SlevomatCodingStandard.Whitespaces.DuplicateSpaces"/>

    <rule ref="SlevomatCodingStandard.TypeHints.ParameterTypeHint"/>
    <rule ref="SlevomatCodingStandard.TypeHints.ReturnTypeHint"/>
    <rule ref="SlevomatCodingStandard.TypeHints.PropertyTypeHint"/>
    <rule ref="SlevomatCodingStandard.Operators.DisallowEqualOperators"/>
    <rule ref="SlevomatCodingStandard.Functions.DisallowEmptyFunction"/>
    <rule ref="SlevomatCodingStandard.Classes.RequireConstructorPropertyPromotion"/>

</ruleset>
```

**Step 2: Auto-fix violations with phpcbf**

```bash
docker compose run --rm cli vendor/bin/phpcbf --standard=ruleset.xml
```

Expected: output showing files fixed (mainly `declare(strict_types = 1)` → `declare(strict_types=1)` across ~70 files).

**Step 3: Check remaining violations**

```bash
docker compose run --rm cli vendor/bin/phpcs --standard=ruleset.xml
```

Expected: 0 errors. If any remain, fix them manually and re-run until clean.

---

## Task 4: Replace `auth` — GitHub Actions workflows

**Files:**
- Delete: `/Users/patrikjakab/workspace/packages/auth/.github/workflows/main.yml`
- Create: `/Users/patrikjakab/workspace/packages/auth/.github/workflows/lint.yml`
- Create: `/Users/patrikjakab/workspace/packages/auth/.github/workflows/test.yml`
- Create: `/Users/patrikjakab/workspace/packages/auth/.github/workflows/release.yml`

**Step 1: Create `lint.yml`**

```yaml
name: Lint

on:
  push:
    branches:
      - '*'
  pull_request:

jobs:
  lint:
    uses: patrikjak/workflows/.github/workflows/lint.yml@main
    with:
      php-version: '8.5'
```

**Step 2: Create `test.yml`**

```yaml
name: Test

on:
  push:
    branches:
      - '*'
  pull_request:

jobs:
  test:
    uses: patrikjak/workflows/.github/workflows/test.yml@main
    with:
      php-version: '8.5'
      codecov-slug: patrikjak/auth
    secrets:
      codecov-token: ${{ secrets.CODECOV_TOKEN }}
```

**Step 3: Create `release.yml`**

```yaml
name: Release

on:
  workflow_dispatch:
    inputs:
      release_type:
        description: 'Release type'
        required: true
        type: choice
        options:
          - patch
          - minor
          - major

jobs:
  release:
    uses: patrikjak/workflows/.github/workflows/release-package.yml@main
    with:
      release_type: ${{ inputs.release_type }}
    permissions:
      contents: write
```

**Step 4: Delete old `main.yml`**

```bash
rm /Users/patrikjakab/workspace/packages/auth/.github/workflows/main.yml
```

---

## Task 5: Replace `utils` — GitHub Actions workflows

**Files:**
- Delete: `/Users/patrikjakab/workspace/packages/utils/.github/workflows/main.yml`
- Create: `/Users/patrikjakab/workspace/packages/utils/.github/workflows/lint.yml`
- Create: `/Users/patrikjakab/workspace/packages/utils/.github/workflows/test.yml`
- Keep: `/Users/patrikjakab/workspace/packages/utils/.github/workflows/release.yml` (already correct)

**Step 1: Create `lint.yml`**

```yaml
name: Lint

on:
  push:
    branches:
      - '*'
  pull_request:

jobs:
  lint:
    uses: patrikjak/workflows/.github/workflows/lint.yml@main
    with:
      php-version: '8.5'
```

**Step 2: Create `test.yml`**

```yaml
name: Test

on:
  push:
    branches:
      - '*'
  pull_request:

jobs:
  test:
    uses: patrikjak/workflows/.github/workflows/test.yml@main
    with:
      php-version: '8.5'
      codecov-slug: patrikjak/utils
    secrets:
      codecov-token: ${{ secrets.CODECOV_TOKEN }}
```

**Step 3: Delete old `main.yml`**

```bash
rm /Users/patrikjakab/workspace/packages/utils/.github/workflows/main.yml
```

---

## Task 6: Add `test.yml` to `starter`

**Files:**
- Create: `/Users/patrikjakab/workspace/packages/starter/.github/workflows/test.yml`

**Step 1: Create `test.yml`**

```yaml
name: Test

on:
  push:
    branches:
      - '*'
  pull_request:

jobs:
  test:
    uses: patrikjak/workflows/.github/workflows/test.yml@main
    with:
      php-version: '8.5'
```

Note: starter has no Codecov configured yet — omit `codecov-slug`.

---

## Task 7: Run PHPStan on `auth` to confirm no regressions

**Step 1: Run analysis**

```bash
cd /Users/patrikjakab/workspace/packages/auth
docker compose run --rm cli php -d memory_limit=2G vendor/bin/phpstan analyse
```

Expected: same result as before (no new errors). If new errors appear, fix them.

---

## Task 8: Commit all changes

**Step 1: Commit `auth`**

```bash
cd /Users/patrikjakab/workspace/packages/auth
git add docker-compose.yml ruleset.xml src/ tests/ .github/workflows/
git commit -m "chore: align to PHP 8.5, unified ruleset and CI workflows"
```

**Step 2: Commit `utils`**

```bash
cd /Users/patrikjakab/workspace/packages/utils
git add .github/workflows/
git commit -m "chore: split CI into separate lint and test reusable workflows"
```

**Step 3: Commit `starter`**

```bash
cd /Users/patrikjakab/workspace/packages/starter
git add .github/workflows/test.yml
git commit -m "chore: add test workflow"
```

---

## Open Questions / Assumptions

- Auth tests run on SQLite in CI (Orchestra Testbench default) — no MariaDB service needed in workflows. Locally, MariaDB via Docker is still used.
- The `npm install && npm run build` step is removed from CI workflows. PHP lint and test steps do not require compiled frontend assets.
- `declare(strict_types = 1)` (with spaces) will be auto-fixed by phpcbf to `declare(strict_types=1)` to satisfy the new ruleset.
- Starter has no Codecov configured; test.yml is created without coverage upload.
