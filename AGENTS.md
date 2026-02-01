# Repository Guidelines

## Project Structure & Module Organization
- `app/`: Laravel application code (controllers, models, policies).
- `routes/`: HTTP routes (`web.php`, `api.php`).
- `resources/`: Vue/Inertia UI, JS/TS, and styles; primary frontend source.
- `public/`: Public web root; Vite build output lands in `public/build/`.
- `database/`: Migrations, factories, seeders.
- `tests/`: PHPUnit tests (`tests/Unit`, `tests/Feature`).
- `config/`: Framework and package configuration.

## Build, Test, and Development Commands
- `composer setup`: Full setup (installs deps, creates `.env`, generates key, migrates, installs npm deps, builds assets).
- `composer dev`: Runs Laravel server, queue listener, and Vite dev server concurrently.
- `npm run dev`: Frontend dev server only (Vite).
- `npm run build`: Production asset build (Vite).
- `composer test`: Clears config cache and runs `php artisan test`.

## Coding Style & Naming Conventions
- PHP: Follow Laravel conventions (PSR-12 style, studly class names, snake_case table names).
- Frontend: Use Prettier and ESLint (`npm run format`, `npm run lint`).
- TypeScript/Vue files live under `resources/`; keep component names in `PascalCase`.
- Keep public assets in `public/`; do not edit generated `public/build/`.

## Testing Guidelines
- PHPUnit suites: `tests/Unit` for isolated logic, `tests/Feature` for HTTP/integration.
- Run full suite with `composer test` or `php artisan test`.
- Database for tests uses in-memory SQLite per `phpunit.xml`.

## Commit & Pull Request Guidelines
- Git history shows short, direct summaries (e.g., `Add privacy policy page`, `+updates`) and merge commits.
- Prefer concise, descriptive commit messages in the imperative mood.
- PRs should include: purpose summary, key changes, and how you tested (command + result). Add screenshots for UI changes.

## Configuration & Security Notes
- Use `.env` for local settings; do not commit secrets.
- If adding env vars, update `.env.example` and relevant config in `config/`.

## Agent-Specific Instructions
- Avoid editing `vendor/`, `node_modules/`, or generated build output.
- Keep changes focused in `app/`, `resources/`, `routes/`, `tests/`, and `config/`.
