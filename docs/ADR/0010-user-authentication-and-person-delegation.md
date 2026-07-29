# 10. User Authentication and Person Delegation Architecture

## Context
In AccountAnt, user identity was originally contained entirely within the `users` table via `name` and `email` columns. Following HR schema refactoring (migration `2026_07_23_030603`), the `name` column on `users` was dropped in favor of linking each user to a `Person` record via `person_id` and a `PersonAffiliation` record via `person_affiliations_id`.

## Decision
1. **Separation of Concerns**:
   - `User` handles authentication credentials (`email`, `password`, tokens, auth guard).
   - `Person` handles personal demographic details (`first_name`, `last_name`, `birth_date`, `civil_status`, `gender`).
   - `PersonAffiliation` handles corporate role context (`company_id`, `position_id`, `employee_id`, `affiliation_level`).

2. **Accessor Delegation**:
   - `User` model defines a virtual `name` accessor (`getNameAttribute()`) that dynamically constructs `${person->first_name} ${person->last_name}` or defaults to email prefix if `person` relationship is unassigned.

3. **Database Seeder**:
   - `UserSeeder` will seed default system accounts linked to `Person` and `PersonAffiliation` records.

## Consequences
- Clean separation between auth credentials and human demographic records.
- JSON responses automatically append virtual `name` attribute when serialized while preserving relational models.
