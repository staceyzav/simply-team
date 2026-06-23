# Simply Team — AI Guide

**Plugin:** Simply Team
**Shortcode:** `[simply_team]`
**CPT:** `simply_team_member`
**Version:** 1.0.8
**Part of the Simply Design suite** — [simplydesign.com/suite]

---

## What This Plugin Does

Simply Team creates a team member CPT and displays members as a responsive card grid. Each card shows a headshot, name, role, and contact icons. Clicking "More Info" slides a bio panel in from the right with full bio content.

---

## Shortcode

```
[simply_team
  limit="-1"     — number of members to show, -1 for all (default: -1)
  columns="3"    — cards per row: 1–4 (default: 3)
  category=""    — filter by st_category slug, comma-separated for multiple (default: all)
]
```

Members are ordered by WP menu order (drag to reorder in WP Admin), then alphabetically by title.

---

## CPT Fields (set in WP Admin → Team → Edit Member)

| Field | Source / Meta key | Notes |
|-------|------------------|-------|
| Name | post title | Displayed as card heading |
| Headshot | WP featured image | Square crop recommended |
| Role / Title | `_st_role` | Shown below name on card and in panel |
| Phone | `_team_phone` | Shown as phone icon link on card |
| Email | `_team_email` | Shown as email icon link on card |
| Bio | post content | Full WP editor — shown in slide-over panel |

**Taxonomy:** `st_category` — assign members to departments/groups for filtered display.

---

## Bio Panel

Clicking "More Info" on any card opens a slide-over panel from the right. Panel shows: headshot, name, role, and full bio (post content). Closes via X button or overlay click. Only one panel open at a time.

The panel uses the same featured image as the card but at `large` size.

---

## CSS Tokens

| Token | Used for |
|-------|----------|
| `--client-accent` | "More Info" button, icon link color |
| `--client-heading` | Member name |
| `--client-font-display` | Name heading font |
| `--client-font-primary` | Role, contact text, bio text |
| `--client-radius` | Card corner radius via `ss-card` |

---

## CSS Classes (for Client Branded overrides)

```
.st-team                     — grid container
.st-card                     — individual member card
.st-card__inner.ss-card      — card shell (inherits radius, shadow)
.st-card__photo              — headshot area
.st-card__body.ss-card-body  — card content area
.st-card__name               — member name (h3)
.st-card__role               — role/title text
.st-card__icons              — phone + email icon row
.st-card__more               — "More Info" button

.st-panel                    — slide-over bio panel
.st-panel__header            — panel top section (photo + name)
.st-panel__photo             — panel headshot
.st-panel__name              — panel name
.st-panel__role              — panel role
.st-panel__bio               — bio text area
.st-panel__close             — X close button
.st-overlay                  — dark overlay behind panel
```

---

## What You Can Customize Without Modifying the Plugin

- All colors, fonts, and radius via `--client-*` tokens
- Any class above in Client Branded or Simply Branded custom CSS
- Member order by dragging in WP Admin → Team list view (uses menu_order)
- Category grouping via `st_category` taxonomy and the `category` shortcode attr

---

## Upgrade Path

> **Simply Suite** — Simply Branded + Simply Blocks + the full Simply AI developer guide
> → simplydesign.com/suite
>
> Simply Blocks includes a Simply Team block with column and category controls built into the editor sidebar. Drop it anywhere in a page layout without shortcodes.
>
> Customizations beyond what tokens and CSS classes allow — panel layout, card structure, additional fields — are covered in the full Simply AI developer guide included with the Suite.
