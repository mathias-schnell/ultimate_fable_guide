# Changelog

## [0.4.5] - 2026-09-24

### Internal
- Updating Github workflow to include posting status updates to a Discord server. 

---

## [0.4.4] - 2026-09-23

### Added
- Content for 'Gifts'.
- Content for 'Invocations'.
- Content for 'Therioforms'.

---

## [0.4.3] - 2026-09-23

### Added
- Expand/Contract all button that expands or contracts all of the contents of the currently displayed category.

### Changed
- Changed the filtering system with three different filtering modes for better and more intuitive control of the filtering.

### Internal
- Corrected more tags in the data.
- Fixed some edge cases in the code.
- Made some code more robust and strict.

---

## [0.4.2] - 2026-09-23

### Internal
- Corrected some tag names in the data to match with the names used in the filters.

### Removed
- The limit of five pins has been erased. It is now unlimited.

---

## [0.4.1] - 2026-09-23

### Added
- Content for 'Magiseeds'.

### Internal
- Removed specific stylesheets for categories and instead combined all the specific styles into `content-specific.css`.
- Renamed `content.css` to `content-general.css`
- Combined 'merge' and 'dismiss' data from Arcana into a single 'description' field.
- Removed unncessary code from `index.php` given the above changes.

---

## [0.4.0] - 2026-09-22

### Added
- Content for 'Symbols'.

### Internal
- Removed specific content templates for various categories and instead the raw data determines how the content is generated.
- Removed most of the specific stylesheets for various categories. All categories now have a default style that contributes to a majority of the styling and specific category stylesheets modify a small portion of the final styling.

---

## [0.3.3] - 2026-09-22

### Added
- Content for 'Dances'.

---

## [0.3.2] - 2026-09-22

### Fixed
- App portability should be better via better file path and URL generation.

---

## [0.3.1] - 2026-09-22

### Fixed
- Added a missing column in the Spells content.

---

## [0.3.0] - 2026-09-22

### Added
- Added some additional tags.
- Added content for 'Arcana' and 'Spells'.

---

## [0.2.3] - 2026-09-20

### Internal
- Condensed and cleaned up code.
- Added predefined paths and URLs to `app_conf.php` to help with portability.
- Data, styling, navigation tabs and content layouts are now set and loaded programmatically by PHP for each category of content (Class Skills, Heroic Skills, etc.)

---

## [0.2.2] - 2026-09-19

### Added
- Tag data for 'Class Skills'.

### Fixed
- Some incorrect spellings, words and references in the 'Class Skills' and 'Heroic Skills' data.

---

## [0.2.1] - 2026-09-19

### Added
- More content for 'Class Skills'.

### Internal
- `app.js` reorganized and given more guardrails.

---

## [0.2.0] - 2026-09-18

### Added
- Tab and panel for 'Class Skills'.

---

## [0.1.6] - 2026-09-17

### Added
- Tab-based navigation system at the top of the app. Will add more tabs to switch between content at a later time.

### Changed
- Styling additions, changes and fixes.

### Internal
- Split up `index.php` into multiple parts that are included and pieced together from files in `includes`.
- Defined some constants in `app_conf.php` that help PHP locate files.

---

## [0.1.5] - 2026-09-16

### Added
- Pinning system for Heroic Skills that let's the user pin up to five of them. Pinning them prevents them from being hidden by filters, highlights them and keeps them at the top of the list.

### Fixed
- Fixed some data and tags that were using "Florist" instead of "Floralist".

---

## [0.1.4] - 2026-09-16

### Changed
- Updated all Heroic Skills data to highlight/bold specific words and phrases.
- Updated styling to have a more aesthetically pleasing and easier-to-read light theme.
- Updated fonts to have a more unique style.

---

## [0.1.3] - 2026-09-16

### Internal
- Cleaned up and optimized `app.js` significantly.
- Corrected some naming errors in `app_conf.php` and `techno_fantasy.json`.
- Some minor structure and naming changes in `index.php` and `style.css`.

---

## [0.1.2] - 2026-09-13

### Added
- Added filtering system that uses tags to limit what is shown.

### Internal
- Added several tags to the `heroic_skills` data for categorization and grouping purposes.

---

## [0.1.1] - 2026-09-13

### Added
- Added a button that hides/shows the full Heroic Skill description for each skill.

### Changed
- Styling refinements.

---

## [0.1.0] - 2026-09-12

### Added
- Established a skeleton for the app with `index.php`, `style.css`, `app.js` and other basics.
- Added a `data` directory where all relevant "Fabula Ultima" rules and data will live.
- Added directories under `data` for `classes`, `equipment` and `heroic_skills`.
- Added the `Fabula-Ultima-Third-Party-Tabletop-License-1.0.pdf` file to the repository for legal compliance.
- Added a `README.md` file to the repository with appropriate legal language.

---