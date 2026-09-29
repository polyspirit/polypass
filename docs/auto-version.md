# RU

## Авто-версия при коммите

Единый источник версии — файл `VERSION` в корне репозитория.

Формат: **`MAJOR.MINOR.PATCH`** (semver для Composer), например `1.7.1`.
Без префикса `v`, без суффиксов `-gSHORTSHA` / pre-release — иначе `composer update` не видит релиз в constraint вроде `^1.1`.

### Установка хуков (один раз на клон)

```bash
./scripts/install-git-hooks.sh
```

Скрипт выставляет `core.hooksPath=.githooks`. Без этого шага хуки не сработают.

### Как это работает

1. **pre-commit** — увеличивает patch в `VERSION` на 1 и добавляет файл в индекс.
   Если в индекс уже положена **другая** версия (например `1.2.0` вместо `1.1.5`) — она берётся как есть, без `+1` к patch.
   Если в индексе тот же `VERSION`, что в HEAD — patch всё равно поднимается.
2. **post-commit** — читает `VERSION` из коммита и ставит локальный lightweight-тег **`MAJOR.MINOR.PATCH`** (например `1.7.1`).
3. **pre-push** — при `git push` ветки:
   - если тег из `VERSION` указывает на более старый коммит (коммит без bump, `--no-verify`, Git GUI без stdin) — **сам создаёт следующий patch** (`VERSION` + тег) и пушит его вместе с кодом;
   - ставит недостающий тег на tip, если `post-commit` не сработал;
   - пушит semver-теги вместе с веткой.
   Не используйте `git push --no-verify`: хук не запустится, и Composer останется на старом теге.

После коммита и push:

```bash
git describe --tags --always
# 0.1.1

git push origin main
# ... Pushing Composer version tag(s): 0.1.1
```

### Major / minor вручную

Отредактируйте и застейджите `VERSION` до коммита (например `1.8.0` или `2.0.0`).
Авто-bump patch пропускается **только если** застейдженное значение отличается от версии в HEAD.

### Пропуск авто-версии

```bash
GIT_AUTO_VERSION_SKIP=1 git commit ...
```

### Файлы

- `VERSION` — текущая версия в репозитории
- `.githooks/pre-commit` — bump patch
- `.githooks/post-commit` — создание Composer-тега
- `.githooks/pre-push` — авто-релиз, если тег VERSION отстаёт от tip + автопуш semver-тегов
- `scripts/install-git-hooks.sh` — подключение хуков

---

# EN

## Auto version on commit

The shared source of truth is the `VERSION` file at the repo root.

Format: **`MAJOR.MINOR.PATCH`** (Composer semver), e.g. `1.7.1`.
No `v` prefix and no `-gSHORTSHA` / pre-release suffixes — otherwise Composer will not resolve constraints like `^1.1`.

### Install hooks (once per clone)

```bash
./scripts/install-git-hooks.sh
```

This sets `core.hooksPath=.githooks`. Without this step the hooks will not run.

### How it works

1. **pre-commit** — bumps the patch in `VERSION` by 1 and stages the file.
   If a **different** version is already staged (e.g. `1.2.0` instead of `1.1.5`), it is kept as-is (no patch +1).
   If the staged `VERSION` equals HEAD, the patch is still bumped.
2. **post-commit** — reads `VERSION` from the commit and creates a local lightweight tag **`MAJOR.MINOR.PATCH`** (e.g. `1.7.1`).
3. **pre-push** — on `git push` of a branch:
   - if the `VERSION` tag points at an older commit (commit without a bump, `--no-verify`, Git GUI with empty stdin) — **creates the next patch** (`VERSION` + tag) and pushes it with the code;
   - creates a missing tag on the tip if `post-commit` did not run;
   - pushes semver tags with the branch.
   Do not use `git push --no-verify`: the hook will not run, and Composer will keep the old tag.

After commit and push:

```bash
git describe --tags --always
# 0.1.1

git push origin main
# ... Pushing Composer version tag(s): 0.1.1
```

### Manual major / minor

Edit and stage `VERSION` before the commit (e.g. `1.8.0` or `2.0.0`).
The automatic patch bump is skipped **only if** the staged value differs from HEAD.

### Skip auto versioning

```bash
GIT_AUTO_VERSION_SKIP=1 git commit ...
```

### Files

- `VERSION` — current version in the repository
- `.githooks/pre-commit` — patch bump
- `.githooks/post-commit` — Composer tag creation
- `.githooks/pre-push` — auto-release when the VERSION tag is behind the tip + auto-push semver tags
- `scripts/install-git-hooks.sh` — enable hooks
