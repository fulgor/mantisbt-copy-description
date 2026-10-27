# CopyDescription

**MantisBT 2.x plugin** that adds a button to the *View Issue Details* page. One click copies
the issue number, the summary and the description to the clipboard, as raw text
exactly as stored in the issue:

```
0001234: Summary of the issue

Description text
```

The button is placed right behind "Jump to History". If that button is not
available, it is shown in the header of the issue details instead.

![Screenshot shows button](https://github.com/fulgor/mantisbt-copy-description/blob/main/CopyDescription.scr.png)

## Languages
DE, EN, ES, FR, HU, IT, PL, SK, SL, ZS

## Requirements

- MantisBT 2.x or newer

## Installation

1. Copy the folder `CopyDescription` into the `plugins/` directory of your MantisBT installation.
2. In MantisBT open *Manage → Manage Plugins* and click **Install** next to "Copy Description".

## Settings

Which fields are copied is defined in the method `config()` in `CopyDescription.php`
(`ON` = copied, `OFF` = not copied):

| Setting                       | Default |
|-------------------------------|---------|
| `copy_id`                     | ON      |
| `copy_summary`                | ON      |
| `copy_description`            | ON      |
| `copy_steps_to_reproduce`     | OFF     |
| `copy_additional_information` | OFF     |

The head line is `0001234: Summary`; the colon only appears if both ID and summary are
active. All other fields follow, separated by a blank line. Empty fields are skipped.
If nothing is left to copy, no button is shown.


## Notes

- On installations without HTTPS the browser does not offer the modern clipboard
  API. The plugin then falls back to an older method, so the button works there as well.
- The button label is available in English and German, depending on the user's language setting.

## License

Copyright (C) 2026 fulgor <fulgor@posteo.eu>

Licensed under the GNU General Public License, version 2 or (at your option) any
later version (GPL-2.0-or-later). See the file `LICENSE`.
