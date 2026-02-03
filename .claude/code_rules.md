## General code instructions

- use actions to handle logic and bussines logic
- for vue files, use composables to handle logic
- for every feature, we will a test flow to test the feature
- high typed code
- controllers DOES NOT HAVE any logic, only orchestrate the actions
-  use polocies to handle permission
- focus on composition over inheritance
- when needed, use strategy pattern when diverse ways to do the same thing, create an interface, the calsses taht use this interface and the orchestrate class
- only comments when needed, explain complex logic
- Don't generate code comments above the methods or code blocks if they are obvious. Don't add docblock comments when defining variables, unless instructed to, like `/** @var \App\Models\User $currentUser */`. Generate comments only for something that needs extra explanation for the reasons why that code was written.
- For new features, you MUST generate Pest automated tests.
-for magical strings like status, create enums in a aproppiate folder
-in actions folder, crete a folder for each feature, for example, for invoice feature, create a folder named `Invoice` and put all actions related to invoice in this folder, the same in enums folder inside app


## General code instructions
 
- Don't generate code comments above the methods or code blocks if they are obvious. Don't add docblock comments when defining variables, unless instructed to, like `/** @var \App\Models\User $currentUser */`. Generate comments only for something that needs extra explanation for the reasons why that code was written.
- For new features, you MUST generate Pest automated tests.
 
---
 
## PHP instructions
 
- In PHP, use `match` operator over `switch` whenever possible
- Generate Enums always in the folder `app/Enums`, not in the main `app/` folder, unless instructed differently.
- Always use Enum value as the default in the migration if column values are from the enum. Always casts this column to the enum type in the Model.
- Don't create temporary variables like `$currentUser = auth()->user()` if that variable is used only one time.
- Always use Enum where possible instead of hardcoded string values, if Enum class exists. For example, in vue files, and in the tests when creating data if field is casted to Enum then use that Enum instead of hardcoding the value.
- magical strings should
 
---
 
## Laravel instructions
 
- Using Services in Controllers: if Service class is used only in ONE method of Controller, inject it directly into that method with type-hinting. If Service class is used in MULTIPLE methods of Controller, initialize it in Constructor.
- **Eloquent Observers** should be registered in Eloquent Models with PHP Attributes, and not in AppServiceProvider. Example: `#[ObservedBy([UserObserver::class])]` with `use Illuminate\Database\Eloquent\Attributes\ObservedBy;` on top
- Aim for "slim" Controllers and put larger logic pieces in actions classes
- Use Laravel helpers instead of `use` section classes. Examples: use `auth()->id()` instead of `Auth::id()` and adding `Auth` in the `use` section. Other examples: use `redirect()->route()` instead of `Redirect::route()`, or `str()->slug()` instead of `Str::slug()`.
- Don't use `whereKey()` or `whereKeyNot()`, use specific fields like `id`. Example: instead of `->whereKeyNot($currentUser->getKey())`, use `->where('id', '!=', $currentUser->id)`.
- Don't add `::query()` when running Eloquent `create()` statements. Example: instead of `User::query()->create()`, use `User::create()`.
- When adding columns in a migration, update the model's `$fillable` array to include those new attributes.
- Never chain multiple migration-creating commands (e.g., `make:model -m`, `make:migration`) with `&&` or `;` — they may get identical timestamps. Run each command separately and wait for completion before running the next.
- Enums: If a PHP Enum exists for a domain concept, always use its cases (or their `->value`) instead of raw strings everywhere — routes, middleware, migrations, seeds, configs, and UI defaults.
- Don't create Controllers with just one method which just returns `view()`. Instead, use `Route::view()` with vue  file directly.
 
---
 
## Testing instructions
 
### Before Writing Tests
 
  1. **Check database schema** - Use `database-schema` tool to understand:
     - Which columns have defaults
     - Which columns are nullable
     - Foreign key relationship names
 
  2. **Verify relationship names** - Read the model file to confirm:
     - Exact relationship method names (not assumed from column names)
     - Return types and related models
 
  3. **Test realistic states** - Don't assume:
     - Empty model = all nulls (check for defaults)
     - `user_id` foreign key = `user()` relationship (could be `author()`, `employer()`, etc.)
     - When testing form submissions that redirect back with errors, assert that old input is preserved using `assertSessionHasOldInput()`.

## Vue rules

- create composables in lib folder to handle logic
- dont use any 
- every vue has script above template
