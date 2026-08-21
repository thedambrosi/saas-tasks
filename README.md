# SaaS Tasks

A task manager built as a subscription-based SaaS, using Laravel Cashier for recurring billing via Stripe. Built as a portfolio project to practice recurring subscription billing and background job processing (queues) — the two gaps not covered by my previous projects.

## Features

- Task management (create, complete, delete) scoped to the authenticated user
- **Free plan**: limited to 7 active tasks
- **Pro plan**: unlimited tasks + daily email reminders for tasks due tomorrow
- Subscription checkout via Stripe Checkout (Cashier), in test mode
- Self-service billing management via Stripe Customer Portal (update card, cancel)
- Subscription lifecycle handled entirely through Stripe webhooks (no manual "mark as paid" logic)
- Queued email reminders, dispatched by a scheduled Artisan command
- Pest test suite covering authorization, plan gating, and access control

## Tech stack

- Laravel 13
- Livewire 4 (single-file page components)
- PostgreSQL
- Laravel Cashier (Stripe subscriptions)
- Pest

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Add your database and Stripe **test mode** keys to `.env`:

```
DB_CONNECTION=pgsql
DB_DATABASE=saas_tarefas
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_PRICE_PRO=price_...
STRIPE_WEBHOOK_SECRET=whsec_...
CASHIER_CURRENCY=brl
```

```bash
php artisan migrate
npm install && npm run build
```

To receive Stripe webhooks locally, run the [Stripe CLI](https://stripe.com/docs/stripe-cli) alongside the app:

```bash
stripe listen --forward-to https://your-local-domain/stripe/webhook
```

To test the daily email reminder without waiting for the scheduler:

```bash
php artisan queue:work
php artisan tasks:send-due-reminders
```

## Security & design decisions

- **IDOR protection**: task update/delete actions are authorized through a `TaskPolicy`, checked with `$this->authorize()` before any write — a user can never modify another user's task, even by tampering with the ID sent from the client.
- **Mass assignment protection**: `user_id` is intentionally excluded from `Task`'s `$fillable`, so it can never be set by user input.
- **Webhook signature verification**: handled automatically by Cashier via `STRIPE_WEBHOOK_SECRET`, so forged webhook requests are rejected.
- **Subscription state lives entirely in Stripe**: the app never manually marks a user as "subscribed" — it only reacts to webhook events, which is the correct source of truth for billing state.
- **Trade-off, documented on purpose**: the full Stripe Checkout flow isn't covered by automated tests, since properly mocking Stripe's API is a significant undertaking outside this project's scope. Everything downstream of a successful checkout (webhook handling, plan gating, access control) _is_ covered.

## Tests

```bash
php artisan test
```
