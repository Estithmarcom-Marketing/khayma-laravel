# Khayma README

> Developer reference — not full documentation. Read this before touching the codebase.

---

## Database Structure

### Product & Variations

A `Product` holds base info ( name/description/slug, category, brand, SEO meta). Pricing and stock live on `ProductVariation` — never on the product itself.

```
products
  id, name_ar, name_en, description_ar, description_en,
  slug_ar, slug_en, category_id, brand_id, is_published,
  meta_title_ar, meta_title_en, meta_description_ar, meta_description_en

product_variations
  id, product_id, color_id, size_id,
  sku, price, stock_quantity, is_active,
  offer, offer_started_date, offer_expired_date

properties                    (extra key-value specs per variation)
  id, name_ar, name_en

variant_properties            (pivot)
  product_variation_id, property_id, value_ar, value_en
```

### Relations at a glance

| From | Relation | To |
|---|---|---|
| `Product` | `hasMany` | `ProductVariation` |
| `Product` | `belongsTo` | `Category`, `Brand` |
| `Product` | `hasMany` | `Favourite`, `Review` |
| `Product` | `hasManyThrough` | `CartProduct` via `ProductVariation` |
| `Product` | `hasManyThrough` | `ProductReminder` via `ProductVariation` |
| `Product` | `hasManyThrough` | `Order` via `OrderProduct` |
| `ProductVariation` | `belongsTo` | `Product`, `Color`, `Size` |
| `ProductVariation` | `belongsToMany` | `Property` via `variant_properties` (with `value_ar`, `value_en`) |

### Price & Offer logic
- `$product->price` and `$product->offer` are **computed accessors** — they return values from the cheapest variation to be shown at the product itself not in variations. Requires `productVariations` to be eager loaded.
- Offer validity is handled by `scopeSelectWithActiveOffer()` on `ProductVariation` using a `CASE` SQL expression. An offer is active when `offer_started_date <= now <= offer_expired_date`, or when both dates are null.

---

## Architecture Patterns

### Services
Business logic lives in service classes under `app/Services/`. Controllers stay thin — they call services, not Eloquent directly.

### Events & Listeners

Only custom application events are listed here. Framework/package events (Telescope, Sentry, etc.) are wired automatically.

| Event | Listeners |
|---|---|
| `Order\OrderPlacement` | `StoreOrderPlacedNotification` · `SendOrderPlacedSms` |
| `Product\ProductStockUpdated` | `StoreProductBackInStockNotification` · `SendProductBackInStockSms` |

### Scopes

Check the model before writing a raw `where()` — the scope probably already exists.
it's the most important but still more is exist 

| Scope | Model | Purpose |
|---|---|---|
| `scopePublished()` | `Product` | Filter by `is_published = true` |
| `scopeActive()` | `ProductVariation` | Filter by `is_active = true` |
| `scopeSelectWithActiveOffer()` | `ProductVariation` | Select columns + resolve active offer via SQL `CASE` |
| `scopeWithIsInReminder()` | `ProductVariation` | Appends `is_in_reminder` boolean for the current user |

---

## Third-Party Integrations

| Service | Package | Purpose |
|---|---|---|
| **Tabby** | custom integration | Installment — payment gateway |
| **Tamara** | custom integration | Installment — payment gateway |
| **My Fatoorah** |  custom  integration | Payment gateway — online payments (cards, Apple Pay, KNET, etc.) |
| **Tqnyat SMS** | custom integration (`tqnyat/api`) | SMS provider — sending OTPs, notifications|
| **Algolia** | `algolia/algoliasearch-client-php` + `laravel/scout` | Full-text search. `toSearchableArray()` defines indexed fields. |
| **Sentry** | `sentry/sentry-laravel` | Error tracking & performance monitoring |

---

## Key Packages

| Package | Purpose |
|---|---|
| `spatie/laravel-permission` | Roles & permissions — assign via `Role` model, check via gates/middleware |
| `spatie/laravel-medialibrary` | File/image uploads — use `addMedia()`, collections defined per model via `registerMediaCollections()` |
| `maatwebsite/excel` | Excel import/export |
| `mpdf/mpdf` | PDF generation |
| `propaganistas/laravel-phone` | Phone number validation |
| `opcodesio/log-viewer` | Web-based log viewer at `/log-viewer` |
| `laravel/sanctum` | API token authentication |
| `laravel/telescope` *(dev)* | Request/query/job debugging |
| `pestphp/pest` *(dev)* | Testing framework |

---

## Notes

- All text content is **bilingual** (`_ar` / `_en` suffixes). Keep this consistent when adding new fields.
- `SoftDeletes` is enabled on `Product` — use `withTrashed()` / `onlyTrashed()` when needed.
- Run `php artisan event:list` to see all registered event/listener pairs.
- Run `php artisan permission:show` to inspect roles and permissions.
