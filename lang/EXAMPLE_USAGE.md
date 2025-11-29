# Translation Examples

## Example 1: Simple Button

### Before (Hardcoded)
```blade
<button class="btn">Save Changes</button>
```

### After (Translated)
```blade
<button class="btn">{{ __('common.save') }}</button>
```

## Example 2: Page Title

### Before
```blade
<h1>Create Tournament</h1>
```

### After
```blade
<h1>{{ __('tournaments.create_tournament') }}</h1>
```

## Example 3: Dynamic Message

### Before
```blade
<p>Successfully invited {{ $user->name }} to the tournament.</p>
```

### After
```blade
<p>{{ __('tournaments.invitation_sent', ['name' => $user->name]) }}</p>
```

## Example 4: Controller Flash Message

### Before
```php
return back()->with('success', 'Tournament created successfully!');
```

### After
```php
return back()->with('success', __('tournaments.successfully_created'));
```

## Example 5: Form Labels

### Before
```blade
<label for="name">Tournament Name</label>
<input type="text" id="name" name="name" placeholder="Enter tournament name">
```

### After
```blade
<label for="name">{{ __('tournaments.name') }}</label>
<input type="text"
       id="name"
       name="name"
       placeholder="{{ __('tournaments.name') }}">
```

## Example 6: Validation Messages

### Before
```php
public function messages()
{
    return [
        'name.required' => 'The tournament name is required.',
    ];
}
```

### After
```php
public function messages()
{
    return [
        'name.required' => __('validation.required', ['attribute' => __('tournaments.name')]),
    ];
}
```

## Example 7: Dropdown Options

### Before
```blade
<select name="status">
    <option value="draft">Draft</option>
    <option value="announced">Announced</option>
    <option value="ongoing">Ongoing</option>
</select>
```

### After
```blade
<select name="status">
    <option value="draft">{{ __('tournaments.status_draft') }}</option>
    <option value="announced">{{ __('tournaments.status_announced') }}</option>
    <option value="ongoing">{{ __('tournaments.status_ongoing') }}</option>
</select>
```

## Example 8: Navigation Menu

### Before
```blade
<nav>
    <a href="/dashboard">Dashboard</a>
    <a href="/tournaments">Tournaments</a>
    <a href="/profile">Profile</a>
</nav>
```

### After
```blade
<nav>
    <a href="/dashboard">{{ __('common.dashboard') }}</a>
    <a href="/tournaments">{{ __('common.tournaments') }}</a>
    <a href="/profile">{{ __('common.profile') }}</a>
</nav>
```

## Pro Tips

1. **Keep it Simple**: Use clear, descriptive keys
   ```php
   ✅ __('tournaments.create_tournament')
   ❌ __('t.ct')
   ```

2. **Parameters**: Use named parameters for clarity
   ```php
   ✅ __('msg.hello', ['name' => $user->name])
   ❌ __('msg.hello', [$user->name])
   ```

3. **Consistency**: Use the same key for the same text
   ```php
   // Use 'common.save' everywhere, not sometimes 'save', sometimes 'save_changes'
   ```

4. **Organize**: Group related translations in the same file
   ```php
   // All tournament-related in tournaments.php
   // All common UI in common.php
   ```
