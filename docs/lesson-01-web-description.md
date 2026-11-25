# Lesson 1: From an SNMP check to a web page

## Goal and supporting files

Follow one piece of data from a Windows SNMP agent into a PHP variable and then into a browser page. This lesson implements only `sysDescr.0`, not the full System page.

The handout does not require `check-snmp.php` or `local.php` by name. We created the check to verify the connection independently, and the local configuration to separate credentials from shared code.

| File | Responsibility |
| --- | --- |
| `scripts/check-snmp.php` | Read the agent from the terminal, independently of the UI |
| `config/local.php` | Store this workstation's ignored connection settings |
| `config/example.php` | Show the configuration structure without real credentials |
| `public/index.php` | Fetch data and generate HTML |
| `public/assets/style.css` | Define appearance and responsive behavior |
| `public/assets/app.js` | Format timestamps and provide waiting feedback |

## Core concepts

- **Manager:** our PHP program requesting information.
- **Agent:** the Windows service answering requests about the managed device.
- **OID:** the numeric identifier of an object. `1.3.6.1.2.1.1.1.0` identifies the system description scalar instance.
- **MIB:** definitions describing object names, identifiers, types, and access.
- **GET:** an SNMP operation that reads an object. This page performs no SET operation.
- **Community:** the SNMP v2c access string; it does not encrypt the connection.
- **Loopback:** `127.0.0.1` refers to the same computer. Manager and agent remain separate programs.

SNMP agent requests normally use UDP port 161. HTTP from the browser to PHP is a separate exchange.

## Running the page

```powershell
.\scripts\php.bat -d display_errors=0 -S 127.0.0.1:8080 -t public
```

Open `http://127.0.0.1:8080/` and keep the terminal open. Press `Ctrl+C` to stop. IntelliJ's **SNMP Lab** configuration uses port 8081 instead.

The wrapper selects the local runtime and ini. `-d display_errors=0` avoids exposing internal diagnostics. `-S` starts a local development server. `-t public` sets the web document root, keeping configuration outside it.

## Execution order

1. The browser requests the page over HTTP.
2. PHP executes the code at the top of `index.php`.
3. PHP loads and validates local connection settings.
4. PHP sends an SNMP v2c GET to the agent.
5. The agent responds, or the request fails.
6. PHP generates HTML containing data or an error message.
7. The browser renders HTML and loads CSS and JavaScript.

The browser receives generated output, not PHP source or the community.

## PHP syntax used here

| Syntax | Meaning |
| --- | --- |
| `<?php` | Start PHP code |
| `$name` | A variable |
| `=` | Assign a value |
| `===` | Compare both type and value |
| `null` | No value for this state yet |
| `!` | Logical negation |
| `||` | Logical OR |
| `.` | Concatenate strings |
| `;` | End a statement |
| `//` | A comment |
| `<?= ... ?>` | Print an expression |

### 1. Headers and routes

`header('Cache-Control: no-store')` asks clients not to store the page. It changes HTTP metadata, not visible content.

`$_SERVER['REQUEST_URI']` supplies the requested URI. `?? '/'` provides a fallback when it is missing or null. `parse_url(..., PHP_URL_PATH)` extracts the path without query parameters.

Only `/` and `/index.php` are accepted. Other paths return HTTP 404 and stop execution. This avoids presenting a successful home page for an unknown route when the development server falls back to `index.php`.

### 2. Initial state

```php
$description = null;
$error = null;
$oid = '1.3.6.1.2.1.1.1.0';
$started = microtime(true);
```

Initially there is no result or error. The OID selects the information, and the start time lets us measure duration.

### 3. Preconditions and configuration

Inside `try`, `function_exists('snmp2_get')` checks availability of the required function.

`__DIR__` is the current file's directory (`public`). `dirname(__DIR__)` returns its parent, the repository root. Appending `/config/local.php` creates the settings path.

`is_file()` checks existence. `require` executes the configuration file and captures its returned array in `$config`.

| Key | Meaning |
| --- | --- |
| `host` | Agent address |
| `community` | Access string matching the agent settings |
| `timeout` | Wait time in microseconds; 1,000,000 is one second |
| `retries` | Number of retries after the initial attempt |

Validation requires an array, all four non-null entries, nonempty string address/community, a positive integer timeout, and a nonnegative integer retry count. Validation catches common input mistakes; reachability still requires a real request.

### 4. The network operation

```php
$response = @snmp2_get(
    $config['host'],
    $config['community'],
    $oid,
    $config['timeout'],
    $config['retries']
);
```

The function sends the request. The `2` denotes SNMP v2c, not a number of requested values. `@` suppresses warnings from this call; it does not fix a failure.

If `$response === false`, an exception is thrown. Otherwise `$description = $response` saves the actual result for display.

### 5. Error handling

`throw new RuntimeException(...)` interrupts the normal path and transfers control to a matching `catch`. `$exception->getMessage()` retrieves the message and assigns it to `$error`.

The first catch handles intended runtime failures. The second catches other `Throwable` values and supplies a generic message instead of exposing internal details.

| Outcome | Description | Error |
| --- | --- | --- |
| Success | Actual agent response | `null` |
| Failure | `null` | Readable message |

### 6. Timing

`(microtime(true) - $started) * 1000` converts elapsed seconds to milliseconds. `round()` rounds the number, and `(int)` converts it to an integer. Duration includes local processing, not just network time.

`gmdate('Y-m-d\TH:i:s\Z')` records UTC time at the end of the attempt. JavaScript later formats its visible representation in the browser's time zone.

### 7. Safe output

```php
function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
```

The function accepts and returns a string. It converts HTML-sensitive characters into safe representations, handles quotation marks, and substitutes invalid encoding sequences. Agent data must be displayed as text rather than executed as HTML or script.

## HTML walkthrough

### Document setup

`<!doctype html>` selects modern HTML. `<html lang="en" dir="ltr">` declares English and left-to-right layout.

The head sets UTF-8, the mobile viewport, browser-tab title, CSS stylesheet, and a deferred script. `defer` runs the script after the HTML is parsed.

### Structure and semantics

`header` contains identity, `main` contains the introduction and response, and `footer` contains the project label. `div` is a general container; `span` groups inline content; `h1` is the main heading; `p` is a paragraph.

`class` groups elements for styling. `id` uniquely identifies an element. `aria-labelledby` links a section to its heading. Decorative icons use `aria-hidden="true"` so screen readers do not announce meaningless symbols.

### Conditional status

```php
<?= $error === null ? 'success' : 'failure' ?>
```

This ternary expression chooses a CSS class. The same pattern selects the visible status text. PHP evaluates it on the server; the browser sees only the chosen HTML.

### Response or error

`if ($error !== null)` selects the error panel and prints `escape($error)`. Otherwise the template prints `escape($description)` inside `pre`, which preserves whitespace. `endif` closes the template condition.

`<?= ... ?>` is shorthand for printing an expression. `if: ... else: ... endif;` is an alternative conditional syntax convenient in templates.

The error panel has `role="alert"`. Status is communicated in words as well as colors.

### Metadata

`dl` is a description list: `dt` names a field and `dd` supplies its value. The page displays OID, last-attempt time, and duration.

The `time` element stores a machine-readable UTC `datetime`. `data-local-time` is a custom attribute used by JavaScript to find the element and update its visible text.

### Refresh interaction

The button is `type="submit"` inside a `form method="get"`. With no explicit action, it requests the current page again. That re-executes PHP and sends a new SNMP GET.

HTTP GET and SNMP GET are distinct operations. This implementation reloads the whole document; it is not AJAX.

`aria-describedby` connects the button with its explanatory hint. An initially empty paragraph with `role="status"` and `aria-live="polite"` receives waiting feedback.

### Explanatory flow

The Browser / PHP Manager / SNMP Agent diagram is static educational content. It is not a network traffic monitor.

## CSS and JavaScript

CSS defines spacing, typography, status styling, the response card, focus indicators, and mobile breakpoints. Animation respects reduced-motion preferences.

JavaScript formats timestamps with `Intl.DateTimeFormat('en-GB', ...)`. On form submission it disables the button, updates its text, marks it busy, and announces waiting status. It does not perform SNMP requests.

Without JavaScript, reading and refresh still work; waiting enhancements and local-time formatting are absent.

## Verification and next steps

Completed manual checks: PHP syntax, live CLI/HTTP reads, refreshed timestamps, a 390-pixel layout without horizontal overflow, missing-extension feedback, 404 for `/config/local.php`, and absence of the community from HTML. The student confirmed IntelliJ execution after setup.

These are manual checks, not an automated regression suite. The next increment is to explain and add the remaining System objects, then implement controlled updates. TCP statistics and cryptographic scripts follow separately.
