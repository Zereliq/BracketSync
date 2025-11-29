# Translation Quick Reference

## Quick Start

### In Blade Views
```blade
{{ __('common.save') }}
{{ __('tournaments.player_count', ['count' => 5]) }}
```

### In Controllers
```php
return back()->with('success', __('tournaments.successfully_created'));
```

## Available Translation Files

| File | Purpose | Example Keys |
|------|---------|--------------|
| `common.php` | Buttons, navigation, common actions | `save`, `cancel`, `edit`, `delete` |
| `tournaments.php` | Tournament features | `create_tournament`, `registered_players` |

## Supported Languages

| Code | Language | Status |
|------|----------|--------|
| `en` | English | ✅ Complete |
| `nl` | Nederlands | ✅ Complete |

## Add New Language in 3 Steps

1. **Edit `config/app.php`**: Add to `available_locales` and `locale_names`
2. **Copy files**: `cp -r lang/en lang/xx`
3. **Translate**: Edit all files in `lang/xx/`

## Common Patterns

```blade
{{-- Simple --}}
{{ __('common.logout') }}

{{-- With parameter --}}
{{ __('tournaments.invited_by', ['name' => $user->name]) }}

{{-- In attributes --}}
<button title="{{ __('common.close') }}">X</button>

{{-- Pluralization --}}
{{ trans_choice('tournaments.player_count', $count) }}
```

## Testing

Switch language: Click globe icon in header or add `?lang=xx` to URL
