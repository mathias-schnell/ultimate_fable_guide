# Changelog

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