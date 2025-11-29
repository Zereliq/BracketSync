# Localization Guide

This guide explains how to use and extend the multilingual support in BracketSync.

## Overview

BracketSync supports multiple languages with an easy-to-use translation system. Currently supported languages:
- **English (en)** - Default
- **Nederlands (nl)** - Dutch

## How It Works

### 1. Language Detection Priority

The system detects the user's language in this order:
1. **URL Parameter**: `?lang=nl`
2. **Session**: Previously selected language
3. **User Preference**: Stored in user profile (future feature)
4. **Browser Language**: Automatic detection from browser settings
5. **Default**: Falls back to English

### 2. Language Files

Translation files are located in `lang/{locale}/`:
- `lang/en/common.php` - Common translations (buttons, navigation, etc.)
- `lang/en/tournaments.php` - Tournament-specific translations
- `lang/nl/common.php` - Dutch common translations
- `lang/nl/tournaments.php` - Dutch tournament translations

## Using Translations

### In Blade Templates

```blade
{{-- Simple translation --}}
{{ __('common.save') }}

{{-- Translation with parameters --}}
{{ __('tournaments.invited_by', ['name' => $user->name]) }}

{{-- Alternative syntax --}}
@lang('common.cancel')
```

### In Controllers

```php
// Flash message with translation
return back()->with('success', __('tournaments.successfully_created'));

// With parameters
return back()->with('success', __('tournaments.invitation_sent', ['name' => $user->name]));
```

### In Validation

```php
// In Form Request classes
public function messages()
{
    return [
        'name.required' => __('validation.required', ['attribute' => 'name']),
    ];
}
```

## Adding a New Language

### Step 1: Add Language Code to Config

Edit `config/app.php`:

```php
'available_locales' => ['en', 'nl', 'ja'], // Add 'ja' for Japanese

'locale_names' => [
    'en' => 'English',
    'nl' => 'Nederlands',
    'ja' => '日本語', // Add Japanese
],
```

### Step 2: Create Language Files

```bash
mkdir lang/ja
cp lang/en/common.php lang/ja/common.php
cp lang/en/tournaments.php lang/ja/tournaments.php
```

### Step 3: Translate the Content

Edit `lang/ja/common.php` and `lang/ja/tournaments.php` with Japanese translations.

### Step 4: Test

Visit `?lang=ja` to test the new language.

## Adding New Translation Keys

### Step 1: Add to English File

Edit `lang/en/yourfile.php`:

```php
return [
    'new_feature' => 'New Feature',
    'welcome_message' => 'Welcome, :name!',
];
```

### Step 2: Add to All Other Languages

Add the same keys to `lang/nl/yourfile.php`, `lang/ja/yourfile.php`, etc.

### Step 3: Use in Code

```blade
{{ __('yourfile.new_feature') }}
{{ __('yourfile.welcome_message', ['name' => $user->name]) }}
```

## Language Switcher

The language switcher is available in the dashboard header (globe icon). It:
- Shows all available languages
- Highlights the current language
- Persists selection in session
- Works via URL parameter (?lang=xx)

## Best Practices

### DO:
✅ Use translation keys for all user-facing text
✅ Keep keys organized by feature/section
✅ Use descriptive key names: `'registration_success'` not `'msg1'`
✅ Add new keys to ALL language files at once
✅ Use parameters for dynamic content: `'invited_by' => 'Invited by :name'`

### DON'T:
❌ Don't hardcode text in views
❌ Don't mix languages in the same file
❌ Don't use long sentences as keys
❌ Don't forget to translate error messages

## Translation File Organization

```
lang/
├── en/
│   ├── common.php          # Buttons, navigation, common words
│   ├── tournaments.php     # Tournament features
│   ├── auth.php           # Login, registration
│   └── validation.php     # Validation messages
└── nl/
    ├── common.php
    ├── tournaments.php
    ├── auth.php
    └── validation.php
```

## Examples

### Common Patterns

```blade
{{-- Button --}}
<button>{{ __('common.save') }}</button>

{{-- Title --}}
<h1>{{ __('tournaments.create_tournament') }}</h1>

{{-- Count with pluralization --}}
{{ trans_choice('tournaments.player_count', $count, ['count' => $count]) }}

{{-- Link --}}
<a href="{{ route('tournaments.index') }}">{{ __('common.tournaments') }}</a>

{{-- Flash message --}}
@if(session('success'))
    <div class="alert">{{ session('success') }}</div>
@endif
```

### In Controllers

```php
// Success message
return redirect()
    ->route('tournaments.show', $tournament)
    ->with('success', __('tournaments.successfully_created'));

// Error message
return back()->with('error', __('common.error'));

// With parameter
return back()->with('success', __('tournaments.invitation_sent', [
    'name' => $user->name
]));
```

## Testing Translations

1. **Switch language**: Click globe icon → Select language
2. **Check URL**: Should have `?lang=xx` parameter
3. **Verify persistence**: Navigate to another page, language should stay
4. **Test all features**: Make sure all text is translated

## Future Enhancements

- [ ] User language preference in profile
- [ ] Automatic country-based language detection
- [ ] Translation management interface
- [ ] Community translation contributions
- [ ] RTL language support (Arabic, Hebrew)
- [ ] Date/time localization
- [ ] Number formatting per locale

## Need Help?

- Check existing translation files for examples
- Use `__('file.key')` helper function
- Keep translations short and clear
- Test on all supported languages before committing
