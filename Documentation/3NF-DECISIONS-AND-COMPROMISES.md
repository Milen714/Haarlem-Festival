# 3NF Descisions and Compromises

## Design Decision 1: SCHEDULE Table — Nullable Foreign Keys

The **SCHEDULE** table has foreign keys for `restaurant_id`, `landmark_id`, `venue_id`, and `artist_id`, but only one of these will be filled in per row, depending on the type of event. The others stay NULL.

We considered splitting this into separate tables per event type, but decided against it. Every schedule entry, regardless of type, has the same fields: a date, a start and end time, a capacity, and an event ID. There's no real reason to fragment that into multiple tables. A unified table means simpler queries, less code, and a cleaner structure overall.

The nullable foreign keys aren't a flaw; they're doing a job. Exactly one will be populated per row, which tells you what kind of event it is. This is a well-known pattern called an **"Exclusive Arc"**, and it's a practical choice when subtypes share most of their structure.

## Design Decision 2: TICKET_SCHEME Table — ticket_language Column

The `ticket_language` column only matters for History tour tickets. For every other ticket type, it's NULL.

Strictly speaking, you could move this into its own table. But it's one column. Introducing an extra join and an extra table just for a single optional field isn't worth it, at least not for this small use case project. A NULL here simply means "not applicable" — which is clear, honest, and easy to work with.
