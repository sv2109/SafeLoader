## 🛡 SafeLoader — safe ionCube model loader for OpenCart

SafeLoader adds a safe way to load ionCube‑encoded models so that instead of fatal errors your store keeps running, while all problems are cleanly logged and (optionally) shown on the screen.

---

## ✨ Features

- 🧱 **Protection from fatal errors**  
  If something is wrong with an ionCube model (license, permissions, corrupted file, etc.), script execution is **not stopped**.

- 🧾 **Logging of all issues**  
  All errors and abnormal situations are written to the standard OpenCart log with the `[SafeLoader]` prefix.

- 🐞 **Convenient debug mode**  
  When you add the `debug_safeloader` GET parameter to the URL, detailed debug information is shown directly on the page.

- 🧩 **Support for ionCube error files**  
  If a module provides error files like `module_name_error.php`, they can throw `SafeLoaderException` with a detailed error description.

---

## 🔧 How it works

SafeLoader is registered as an OpenCart library (see `system/saveloader.ocmod.xml`) and becomes available in controllers via `$this->safeloader`.

The main class is located in `system/library/safeloader.php`:

```php
class SafeLoader {
    private $registry;

    public function __construct($registry) {
        $this->registry = $registry;
    }

    public function model($route) {
        // 1. Check ionCube Loader
        // 2. Try to load the model
        // 3. Handle SafeLoaderException and any other errors
        // 4. Log and safely return null
    }
}
```

### ionCube check

Before loading a model SafeLoader checks:

- If the `ionCube Loader` extension is **not installed**, the model is not loaded, a message is written to the log, and the method returns `null`.

### Exception handling

- If a `SafeLoaderException` (for example, from a `*_error.php` file) or any other `Throwable` is thrown during loading or execution, SafeLoader:
  - writes a detailed message to the log;
  - **does not stop** script execution;
  - returns `null` instead of a model object.

---

## 🧪 Usage examples

### 🚫 Standard loading (risk of fatal error)

```php
// May cause a fatal error if something is wrong with ionCube
$this->load->model('extension/module/module_name');
$this->model_extension_module_module_name->doSomething();
```

### ✅ Safe loading through SafeLoader

```php
// Safe model loading
if (!$this->safeloader->model('extension/module/module_name')) {
    // Model was not loaded — just exit the method.
    // The store continues to work without a fatal error.
    return;
}

// If we are here — the model was successfully loaded
// and is available as usual:
$this->model_extension_module_module_name->doSomething();
```

Under the hood, SafeLoader calls the standard `$this->load->model($route)` and returns the model object by name `model_...`, but wraps this in error protection.

---

## ⚙️ ionCube error file support

For correct work with ionCube it is recommended that your module provides an error file of the form:

- `catalog/model/extension/module/module_name_error.php`  
  (or the corresponding path for your module)

This file should define an `ioncube_event_handler` function that converts ionCube error codes to human‑readable messages and throws `SafeLoaderException`.

Example (simplified):

```php
require_once DIR_SYSTEM . 'library/safeloader_exception.php';

function ioncube_event_handler($err_code, $params) {
    $filename = basename(__FILE__, '.php');
    $filename = preg_replace('/_error$/', '', $filename);
    $module_name = ucwords(str_replace('_', ' ', $filename));

    // ... build a readable description based on $err_code ...

    throw new SafeLoaderException($module_name, $message, $err_code);
}
```

The `SafeLoaderException` (see `system/library/safeloader_exception.php`) is caught by SafeLoader and does **not** break the store.

---

## 🐛 Debug mode (`debug_safeloader`)

To see detailed error information in the browser, add the parameter to the URL:

```text
?debug_safeloader=1
```

For example:

```text
https://example.com/?route=common/home&debug_safeloader=1
```

In this case, SafeLoader will show a debug block with the error text and additional details (if any) on the page, in addition to writing to the log.

---

## 📚 Integration into OpenCart

The module is connected via modification (`system/saveloader.ocmod.xml`):

- In `system/engine/loader.php` after `$this->registry` initialization a call to load the `safeloader` library is added:
  ```php
  $this->library('safeloader');
  ```
- After that you can use in controllers:
  ```php
  if (!$this->safeloader->model('extension/module/module_name')) {
      return;
  }
  ```

---

## ✅ Summary

- SafeLoader protects your store from fatal errors related to ionCube‑encoded modules.
- All issues are written to the log and, if requested, shown via a debug block.
- The typical usage pattern is:
  ```php
  if (!$this->safeloader->model('extension/module/module_name')) {
      return;
  }
  ```
- It is recommended to use SafeLoader together with `*_error.php` files and `SafeLoaderException` for maximum diagnostic value.
